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

add_action( 'admin_menu', 'scripto_admin_menu_settings' );
add_action( 'admin_init', 'scripto_admin_init_settings' );

add_filter( 'attachment_fields_to_edit', 'scripto_attachment_fields_to_edit', 10, 2 );
add_filter( 'attachment_fields_to_save', 'scripto_attachment_fields_to_save', 10, 2 );

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
	
	register_setting( 'scripto_options_group', 
		'scripto_options', 
		'scripto_options_validate' );
	
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
 * Display the settings page.
 */
function scripto_settings() {
?>
<div class="wrap">
<h2>Scripto Settings</h2>
<form method="post" action="options.php">
<?php settings_fields( 'scripto_options_group' ); ?>
<?php do_settings_sections( 'scripto_settings_sections_page' ); ?>
<p class="submit"><input type="submit" class="button-primary" value="<?php esc_attr_e('Save Changes') ?>" /></p>
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
<input id="scripto_mediawiki_api_url" name="scripto_options[mediawiki_api_url]" size="60" type="text" value="<?php echo scripto_get_option('mediawiki_api_url'); ?>" />
<?php
}

/**
 * Display the Zend Framework path field.
 */
function scripto_settings_field_zend_framework_path() {
?>
<input id="scripto_zend_framework_path" name="scripto_options[zend_framework_path]" size="60" type="text" value="<?php echo scripto_get_option('zend_framework_path') ?>" />
<?php
}

/**
 * Validate the setting page options.
 * 
 * @param array $options
 * @return array
 */
function scripto_options_validate($options) {
	
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
 * Get a Scripto option from the option array.
 * 
 * @param string $name
 * @param mixed $default
 * @return mixed
 */
function scripto_get_option( $name, $default = false )
{
	$options = get_option( 'scripto_options' );
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
		. PATH_SEPARATOR . scripto_get_option( 'zend_framework_path' ) 
		. PATH_SEPARATOR . scripto_get_scripto_path() );
	
	require_once 'Scripto.php';
	require_once 'ScriptoAdapterWordpress.php';
	$scripto = new Scripto( new ScriptoAdapterWordpress, 
		array('api_url' => scripto_get_option('mediawiki_api_url')));
	
	return $scripto;
}

/**
 * Get the path to Scripto library.
 * 
 * @return string
 */
function scripto_get_scripto_path() {
	return dirname(__FILE__) . '/lib';
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
