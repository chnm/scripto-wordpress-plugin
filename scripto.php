<?php
/*
Plugin Name: Scripto
Plugin URI: http://scripto.org/
Description: Adds the ability to transcribe files using the Scripto library.
Version: 1.0
Author: Center for History and New Media
Author URI: http://chnm.gmu.edu/
License: GPL2
*/

register_activation_hook( __FILE__, 'scripto_activation' );

add_action( 'admin_menu', 'scripto_admin_menu_settings' );
add_action( 'admin_init', 'scripto_admin_init_settings' );

add_filter( 'plugin_action_links', 'scripto_plugin_action_links_settings', 10, 2 );
add_filter( 'attachment_fields_to_edit', 'scripto_attachment_fields_to_edit', 10, 2 );
add_filter( 'attachment_fields_to_save', 'scripto_attachment_fields_to_save', 10, 2 );
add_filter( 'the_content', 'scripto_the_content_document_page_list' );

add_shortcode( 'scripto', 'scripto' );

/**
 * Activate the plugin.
 */
function scripto_activation() {
	
	// Create the Scripto page if not already created. If for any reason the 
	// page is deleted, the administrator needs only to deactivate and 
	// reactivate the plugin to reset the page.
	$page_id = get_option( 'scripto_page_id' );
	$post = get_post( $page_id );
	if ( ! $post ) {
		$post = array(
			'post_type'      => 'page', 
			'post_status'    => 'publish', 
			'post_title'     => 'Scripto', 
			'post_content'   => '[scripto]', 
			'post_name'      => 'scripto', 
			'comment_status' => 'closed', 
			'ping-status'    => 'closed', 
		);
		$page_id = wp_insert_post( $post );
		update_option( 'scripto_page_id', $page_id );
	}
}

/**
 * Display the settings menu.
 */
function scripto_admin_menu_settings() {
	add_options_page( 'Scripto Settings', 
		'Scripto', 
		'manage_options', 
		'scripto-settings', 
		'scripto_settings' );
}

/**
 * Prepare the settings page.
 */
function scripto_admin_init_settings() {
	
	register_setting( 'scripto_settings_group', 
		'scripto_settings', 
		'scripto_settings_validate' );
	
	add_settings_section( 'scripto_settings_section_configuration', 
		'Configuration', 
		'scripto_settings_section_configuration', 
		'scripto_settings_sections_page' );
	
	add_settings_field( 'scripto_zend_framework_path', 
		'Path to Zend Framework', 
		'scripto_settings_field_zend_framework_path', 
		'scripto_settings_sections_page', 
		'scripto_settings_section_configuration' );
	
	add_settings_field( 'scripto_mediawiki_api_url', 
		'MediaWiki API URL', 
		'scripto_settings_field_mediawiki_api_url', 
		'scripto_settings_sections_page', 
		'scripto_settings_section_configuration' );
}

/**
 * Add a link to the Scripto settings page to the plugins browse page.
 */
function scripto_plugin_action_links_settings( $actions, $plugin_file ) {
	if ( plugin_basename( __FILE__ ) == $plugin_file ) {
		$settings_link = '<a href="options-general.php?page=scripto-settings">Settings</a>';
		array_unshift( $actions, $settings_link );
	}
	return $actions;
}

/**
 * Add the transcription field to the attachment form.
 */
function scripto_attachment_fields_to_edit( $form_fields, $post ) {
	$form_fields['scripto_attachment_transcription'] = array(
		'label' => 'Scripto Transcription', 
		'value' => get_post_meta( $post->ID, '_scripto_attachment_transcription', true ), 
		'input' => 'textarea', 
	); 
	return $form_fields;
}

/**
 * Save the attachment transcription to the database.
 */
function scripto_attachment_fields_to_save( $post, $attachment ) {
	if ( isset( $attachment['scripto_attachment_transcription'] ) ) {
		update_post_meta( $post['ID'], 
			'_scripto_attachment_transcription', 
			$attachment['scripto_attachment_transcription'] );
	}
	return $post;
}

/**
 * Display the document page list.
 */
function scripto_the_content_document_page_list( $content ) {
	
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
		'p' => get_option( 'scripto_page_id' ), 
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
	<li><a href="<?php echo home_url( '?' . http_build_query( $params ) ); ?>"><?php echo $attachment->post_title; ?></a></li>
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
function scripto( $atts, $content, $code ) {
	
	// Check for required parameters.
	if ( ! isset($_GET['scripto_doc_id']) || ! isset($_GET['scripto_doc_page_id']) ) {
		return;
	}
	
	try {
		// Load the Scripto document and page.
		$scripto = scripto_get_scripto();
		$doc = $scripto->getDocument($_GET['scripto_doc_id']);
		$doc->setPage($_GET['scripto_doc_page_id']);
		
		// Save the transcription.
		if (isset($_POST['scripto_transcripton'])) {
			$doc->editTranscriptionPage($_POST['scripto_transcripton']);
		}
		
	} catch (Scripto_Exception $e) {
		return '<p>' . $e->getMessage() . '</p>';
	}
	
?>
<form action="<?php  ?>" method="post">
	<div><img src="<?php echo $doc->getPageFileUrl(); ?>" /></div>
	<textarea name="scripto_transcripton" cols="45" rows="12"><?php echo $doc->getTranscriptionPageWikitext(); ?></textarea>
	<input type="submit" name="scripto_submit_transcription" value="Save Transcription" />
</form>
<?php
}

/**
 * Display the settings page.
 */
function scripto_settings() {
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
function scripto_settings_section_configuration() {
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
function scripto_settings_field_mediawiki_api_url() {
?>
<input id="scripto_mediawiki_api_url" name="scripto_settings[mediawiki_api_url]" size="60" type="text" value="<?php echo scripto_get_setting('mediawiki_api_url'); ?>" />
<?php
}

/**
 * Display the Zend Framework path field.
 */
function scripto_settings_field_zend_framework_path() {
?>
<input id="scripto_zend_framework_path" name="scripto_settings[zend_framework_path]" size="60" type="text" value="<?php echo scripto_get_setting('zend_framework_path') ?>" />
<?php
}

/**
 * Validate the setting page options.
 * 
 * @param array $options
 * @return array
 */
function scripto_settings_validate( $options ) {
	
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
		. PATH_SEPARATOR . scripto_get_scripto_path() );
	
	require_once 'Scripto.php';
	if ( ! Scripto::isValidApiUrl( $options['mediawiki_api_url'] ) ) {
		add_settings_error( 'scripto_mediawiki_api_url', 
			'scripto_invalid_mediawiki_api_url', 
			'Invalid MediaWiki API URL.' );
	}
	
	return $valid_options;
}

/**
 * Get a Scripto setting option from the settings array.
 * 
 * @param string $name
 * @param mixed $default
 * @return mixed
 */
function scripto_get_setting( $name, $default = false )
{
	$options = get_option( 'scripto_settings' );
	if ( ! isset($options[$name]) ) {
		return $default;
	}
	return $options[$name];
}

/**
 * Load the Scripto environment and return a Scripto object.
 * 
 * @return Scripto
 */
function scripto_get_scripto() {
	
	set_include_path(get_include_path() 
		. PATH_SEPARATOR . scripto_get_setting( 'zend_framework_path' ) 
		. PATH_SEPARATOR . scripto_get_scripto_path() );
	
	require_once 'Scripto.php';
	require_once 'ScriptoAdapterWordpress.php';
	$scripto = new Scripto( new ScriptoAdapterWordpress, 
		array('api_url' => scripto_get_setting( 'mediawiki_api_url' )));
	
	return $scripto;
}

/**
 * Get the path to Scripto library.
 * 
 * @return string
 */
function scripto_get_scripto_path() {
	return dirname( __FILE__ ) . '/lib';
}

/**
 * Debug a variable in the browser.
 * 
 * @param mixed $var
 * @param bool $exit
 */
function scripto_debug( $var, $exit = true, $dump = true ) {
	echo '<pre>';
	$dump ? var_dump( $var ) : print_r( $var );
	echo '</pre>';
	if ( $exit ) exit;
}
