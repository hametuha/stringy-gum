<?php


/**
 * Register JS and CSS
 */
add_action( 'init', function () {
	$base = plugin_dir_url( __DIR__ ).'assets';

	// JS
	wp_register_script( 'select2', $base . '/js/select2.js', [ 'jquery' ], '4.0.3', true );
	wp_register_script( 'stringy-gum-admin', $base . '/js/term-dropdown.js', [ 'select2' ], '1.0', true );
	wp_localize_script( 'stringy-gum-admin', 'StringyGum', [
		'endpoint' => admin_url( 'admin-ajax.php' ),
	    'action'   => 'term_dropdow',
	] );
	// CSS
	wp_register_style( 'select2', $base . '/css/select2.min.css', [], '4.0.3', 'screen' );
	wp_register_style( 'stringy-gum-admin',  $base . '/css/admin.css', [ 'select2' ], '1.0.0', 'screen' );
} );



add_action( 'admin_enqueue_scripts', function(){
	wp_enqueue_style( 'stringy-gum-admin' );
	wp_enqueue_script( 'stringy-gum-admin' );
} );
