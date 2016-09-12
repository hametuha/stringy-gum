<?php
/*
Plugin Name: Stringy Gum
Plugin URI: https://wordpress.org/plugins/stringy-gum/
Description: Term manager for huge taxonomy.
Author: Hametuha INC.
Version: 1.0.0
Author URI: https://hametuha.co.jp/
Text Domain: sg
Domain Path: /languages/
License: GPL v3 or later
*/

defined( 'ABSPATH' ) or die( 'Do not load directly.' );

load_plugin_textdomain( 'sg', false, 'stringy-gum/languages' );

// Start
if ( version_compare( phpversion(), '5.4.*', '<' ) ) {
	add_action( 'admin_notices', '_sg_admin_notice' );
} else {
	// Load all includes.
	foreach ( scandir( __DIR__.'/includes/' ) as $file ) {
		if ( preg_match( '#^[^.].*\.php#u', $file ) ) {
			require __DIR__.'/includes/'.$file;
		}
	}
}

/**
 * Show error message
 *
 * @ignore
 */
function _sg_admin_notice() {
	printf(
		'<div class="error"><p><strong>[Stringy Gum]</strong> %s</p></div>',
		sprintf( esc_html__( 'This plugin requires PHP 5.4 and over. You PHP is %s.', 'sg' ), phpversion() )
	);
}
