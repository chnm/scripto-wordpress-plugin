<?php
/*
Plugin Name: Scripto
Plugin URI: http://scripto.org/
Description: Adds the ability to transcribe files using the Scripto library.
Version: 1.1
Author: Center for History and New Media
Author URI: http://chnm.gmu.edu/
License: GPL2
*/

/*
This program is free software; you can redistribute it and/or modify it under 
the terms of the GNU General Public License, version 2, as published by the Free 
Software Foundation.

This program is distributed in the hope that it will be useful, but WITHOUT ANY 
WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A 
PARTICULAR PURPOSE. See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with 
this program; if not, write to the Free Software Foundation, Inc., 51 Franklin 
St, Fifth Floor, Boston, MA  02110-1301  USA
*/

require_once 'class-scripto-plugin.php';

register_activation_hook( __FILE__, 'Scripto_Plugin::activation' );
// Uninstall hook will not be registered until it can be adequately tested.
//register_uninstall_hook( __FILE__, 'Scripto_Plugin::uninstall' );

add_action( 'admin_menu', 'Scripto_Plugin::admin_menu_settings' );
add_action( 'admin_init', 'Scripto_Plugin::admin_init_settings' );
add_action( 'admin_init', 'Scripto_Plugin::admin_init_meta_box' );
add_action( 'save_post', 'Scripto_Plugin::save_post_meta_box' );
add_action( 'wp', 'Scripto_Plugin::set_scripto_application' );

add_filter( 'plugin_action_links', 'Scripto_Plugin::plugin_action_links_settings', 10, 2 );
add_filter( 'attachment_fields_to_edit', 'Scripto_Plugin::attachment_fields_to_edit', 10, 2 );
add_filter( 'attachment_fields_to_save', 'Scripto_Plugin::attachment_fields_to_save', 10, 2 );
add_filter( 'the_content', 'Scripto_Plugin::the_content_document_page_list' );

add_shortcode( 'scripto_application', 'Scripto_Plugin::scripto_application' );

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
