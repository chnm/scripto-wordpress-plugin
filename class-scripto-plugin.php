<?php
/**
 * Contains the hooks and other functionality necessary to run a Scripto 
 * application in WordPress.
 */
class Scripto_Plugin
{
	/**
	 * Activate the plugin.
	 * 
	 * Creates the Scripto page if not already created. All requests to the 
	 * Scripto application are channeled through this page. If for any reason 
	 * the page is deleted, the administrator needs only to deactivate and 
	 * reactivate the plugin to reset the page.
	 */
	public static function activation() {
		
		// Create the Scripto page.
		$page_id = get_option( 'scripto_application_page_id' );
		$post = get_post( $page_id );
		if ( ! $post ) {
			$post = array(
				'post_type'      => 'page', 
				'post_status'    => 'publish', 
				'post_title'     => 'Scripto', 
				'post_content'   => '[scripto_application]', 
				'post_name'      => 'scripto', 
				'comment_status' => 'closed', 
				'ping-status'    => 'closed', 
			);
			$page_id = wp_insert_post( $post );
			update_option( 'scripto_application_page_id', $page_id );
		}
	}
	
	/**
	 * Uninstall the plugin.
	 * 
	 * Deletes all Scripto-oriented data from the WordPress database, but does 
	 * not delete anything from the MediaWiki database. The plugin can be 
	 * reactivated using the same configuration.
	 */
	public static function uninstall() {
		
		// Delete the Scripto page.
		wp_delete_post( get_option( 'scripto_application_page_id' ), true );
		
		// Delete the options.
		$scripto_options = array(
			'scripto_application_page_id', 
			'scripto_settings', 
		);
		foreach ( $scripto_options as $scripto_option ) {
			delete_option( $scripto_option );
		}
		
		// Delete the attachment transcriptions.
		$args = array(
			'post_type'   => 'attachment', 
			'numberposts' => -1, // get all attachment posts
		);
		$attachments = get_posts( $args );
		foreach ( $attachments as $attachment ) {
			delete_post_meta( $attachment->id, 'scripto_attachment_transcription' );
		}
	}
	
	/**
	 * Display the settings menu.
	 */
	public static function admin_menu_settings() {
		add_options_page( 'Scripto Settings', 
			'Scripto', 
			'manage_options', 
			'scripto-settings', 
			'Scripto_Plugin::settings' );
	}
	
	/**
	 * Prepare the settings page.
	 */
	public static function admin_init_settings() {
		
		register_setting( 'scripto_settings_group', 
			'scripto_settings', 
			'Scripto_Plugin::settings_validate' );
		
		add_settings_section( 'scripto_settings_section_configuration', 
			'Configuration', 
			'Scripto_Plugin::settings_section_configuration', 
			'scripto_settings_sections_page' );
		
		add_settings_field( 'scripto_zend_framework_path', 
			'Path to Zend Framework', 
			'Scripto_Plugin::settings_field_zend_framework_path', 
			'scripto_settings_sections_page', 
			'scripto_settings_section_configuration' );
		
		add_settings_field( 'scripto_mediawiki_api_url', 
			'MediaWiki API URL', 
			'Scripto_Plugin::settings_field_mediawiki_api_url', 
			'scripto_settings_sections_page', 
			'scripto_settings_section_configuration' );
	}
	
	/**
	 * Load the Scripto application.
	 * 
	 * Loading the application at this early stage is necessary because Scripto 
	 * may need to set cookies and redirect the page before output is sent to 
	 * the browser.
	 */
	public static function set_scripto_application() {
		
		// Return if not on the Scripto application page.
		if ( ! is_page( get_option( 'scripto_application_page_id' ) ) ) {
			return;
		}
		
		// Load the Scripto application environment.
		set_include_path(get_include_path() 
			. PATH_SEPARATOR . self::get_setting( 'zend_framework_path' ) 
			. PATH_SEPARATOR . self::get_scripto_path() );
		
		require_once 'Scripto.php';
		require_once 'class-scripto-adapter.php';
		require_once 'class-scripto-application.php';
		
		$scripto = new Scripto( new Scripto_Adapter, 
			array('api_url' => self::get_setting( 'mediawiki_api_url' )));
		$scripto_application = Scripto_Application::get_instance( $scripto );
		
		// Set the default action.
		if ( ! isset( $_GET['scripto_action'] ) ) {
			$_GET['scripto_action'] = 'index';
		}
		
		$scripto_application->run_action( $_GET['scripto_action'] );
	}
	
	/**
	 * Display the settings page.
	 */
	public static function settings() {
?>
<div class="wrap">
	<h2>Scripto Settings</h2>
	<form method="post" action="options.php">
		<?php settings_fields( 'scripto_settings_group' ); ?>
		<?php do_settings_sections( 'scripto_settings_sections_page' ); ?>
		<p class="submit"><input type="submit" class="button-primary" value="Save Changes" /></p>
	</form>
</div>
<?php
	}
	
	/**
	 * Display the configuration section content.
	 */
	public static function settings_section_configuration() {
?>
<p>This plugin requires you to download <a href="http://framework.zend.com/">Zend Framwork</a>, 
an open source, object-oriented web application framework that Scripto uses to 
power its API. It also requires you to download and install <a href="http://www.mediawiki.org/wiki/MediaWiki">MediaWiki</a>, 
a popular free web-based wiki software application that Scripto uses to manage 
user and transcription data. Once you have successfully downloaded Zend 
Framework and installed MediaWiki, you can configure the Scripto plugin below.</p>
<?php
	}
	
	/**
	 * Display the MediaWiki API URL field.
	 */
	public static function settings_field_mediawiki_api_url() {
?>
<input id="scripto_mediawiki_api_url" name="scripto_settings[mediawiki_api_url]" size="60" type="text" value="<?php echo self::get_setting('mediawiki_api_url'); ?>" />
<?php
	}

	/**
	 * Display the Zend Framework path field.
	 */
	public static function settings_field_zend_framework_path() {
?>
<input id="scripto_zend_framework_path" name="scripto_settings[zend_framework_path]" size="60" type="text" value="<?php echo self::get_setting('zend_framework_path') ?>" />
<?php
	}
	
	/**
	 * Validate the setting page options.
	 * 
	 * @param array $options
	 * @return array
	 */
	public static function settings_validate( $options ) {
		
		// Return only options that exist on the settings form.
		$valid_options['zend_framework_path'] = $options['zend_framework_path'];
		$valid_options['mediawiki_api_url'] = $options['mediawiki_api_url'];
		
		// Validate path to Zend Framework.
		if ( ! is_dir( $options['zend_framework_path'] ) ) {
			add_settings_error( 'scripto_zend_framework_path', 
				'scripto_invalid_zend_framework_path', 
				'Invalid path to Zend Framework.' );
			
			// Must return the options if path to ZF is invalid because subsequent 
			// validations depend on it.
			return $valid_options;
		}
		
		// Validate MediaWiki API URL.
		set_include_path(get_include_path() 
			. PATH_SEPARATOR . $options['zend_framework_path'] 
			. PATH_SEPARATOR . self::get_scripto_path() );
		
		require_once 'Scripto.php';
		if ( ! Scripto::isValidApiUrl( $options['mediawiki_api_url'] ) ) {
			add_settings_error( 'scripto_mediawiki_api_url', 
				'scripto_invalid_mediawiki_api_url', 
				'Invalid MediaWiki API URL.' );
		}
		
		return $valid_options;
	}
	
	/**
	 * Add a link to the Scripto settings page to the plugins browse page.
	 */
	public static function plugin_action_links_settings( $actions, $plugin_file ) {
		if ( plugin_basename( dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'scripto.php' ) == $plugin_file ) {
			$settings_link = '<a href="options-general.php?page=scripto-settings">Settings</a>';
			array_unshift( $actions, $settings_link );
		}
		return $actions;
	}

	/**
	 * Add the transcription field to the attachment form.
	 */
	public static function attachment_fields_to_edit( $form_fields, $post ) {
		$form_fields['scripto_attachment_transcription'] = array(
			'label' => 'Scripto Transcription', 
			'value' => get_post_meta( $post->ID, 'scripto_attachment_transcription', true ), 
			'input' => 'textarea', 
		); 
		return $form_fields;
	}
	
	/**
	 * Save the attachment transcription to the database.
	 */
	public static function attachment_fields_to_save( $post, $attachment ) {
		if ( isset( $attachment['scripto_attachment_transcription'] ) ) {
			update_post_meta( $post['ID'], 
				'scripto_attachment_transcription', 
				$attachment['scripto_attachment_transcription'] );
		}
		return $post;
	}
	
	/**
	 * Display the document page list.
	 */
	public static function the_content_document_page_list( $content ) {
		
		// Display the list only when a single post or page is being displayed.
		if ( ! is_single() && ! is_page() ) {
			return $content;
		}
		
		// Get the post's attachments.
		$args = array(
			'post_type'   => 'attachment', 
			'post_parent' => get_the_ID(), 
			'numberposts' => -1, // get all attachment posts
			'orderby'     => 'menu_order', 
			'order'       => 'ASC',
		);
		$attachments = get_posts( $args );
		
		// Set the required parameters, where "p" is the Scripto application page ID 
		// and "scripto_doc_id" is the current post's ID. "p=?" always redirects to 
		// the associated permalink, if any.
		$params = array(
			'p'              => get_option( 'scripto_application_page_id' ), 
			'scripto_action' => 'transcribe', 
			'scripto_doc_id' => get_the_ID(), 
		);
		
		// Append the document page list to the content.
		ob_start();
?>
<?php if ($attachments): ?>
<h3>Transcribe</h3>
<ol>
	<?php foreach ( $attachments as $attachment ): ?>
	<?php $params['scripto_doc_page_id'] = $attachment->ID; ?>
	<li><a href="<?php echo site_url( '?' . http_build_query( $params ) ); ?>"><?php echo $attachment->post_title; ?></a></li>
	<?php endforeach; ?>
</ol>
<?php endif; ?>
<?php
		$content .= ob_get_contents();
		ob_end_clean();
		
		return $content;
	}
	
	/**
	 * Display the Scripto application.
	 */
	public static function scripto_application( $atts, $content, $code ) {
		$scripto_application = Scripto_Application::get_instance();
		echo $scripto_application->get_content();
	}
	
	/**
	 * Get a Scripto setting option from the settings array.
	 * 
	 * @param string $name
	 * @param mixed $default
	 * @return mixed
	 */
	public static function get_setting( $name, $default = false )
	{
		$options = get_option( 'scripto_settings' );
		if ( ! isset($options[$name]) ) {
			return $default;
		}
		return $options[$name];
	}
	
	/**
	 * Get the path to Scripto library.
	 * 
	 * @return string
	 */
	public static function get_scripto_path() {
		return dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'lib';
	}
}
