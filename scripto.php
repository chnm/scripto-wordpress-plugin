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

require_once 'class-scripto-plugin.php';

register_activation_hook( __FILE__, 'Scripto_Plugin::activation' );
register_uninstall_hook( __FILE__, 'Scripto_Plugin::uninstall' );

add_action( 'admin_menu', 'Scripto_Plugin::admin_menu_settings' );
add_action( 'admin_init', 'Scripto_Plugin::admin_init_settings' );

add_filter( 'plugin_action_links', 'Scripto_Plugin::plugin_action_links_settings', 10, 2 );
add_filter( 'attachment_fields_to_edit', 'Scripto_Plugin::attachment_fields_to_edit', 10, 2 );
add_filter( 'attachment_fields_to_save', 'Scripto_Plugin::attachment_fields_to_save', 10, 2 );
add_filter( 'the_content', 'Scripto_Plugin::the_content_document_page_list' );

add_shortcode( 'scripto', 'Scripto_Plugin::scripto' );

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
