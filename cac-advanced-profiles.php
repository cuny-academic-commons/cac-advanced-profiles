<?php
/*
Plugin Name: CAC Advanced Profiles
Version: 0.1-alpha
Description: Advanced profiles for the CUNY Academic Commons
Author: Boone B Gorges
Author URI: http://boone.gorg.es
Plugin URI: http://commons.gc.cuny.edu
Text Domain: cac-advanced-profiles
Domain Path: /languages
*/

if ( ! defined( 'CACAP_PLUGIN_DIR' ) ) {
	define( 'CACAP_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
}

if ( ! defined( 'CACAP_PLUGIN_URL' ) ) {
	define( 'CACAP_PLUGIN_URL', plugins_url() . '/cac-advanced-profiles' );
}

function cacap_init() {
	require( dirname( __FILE__ ) . '/includes/component.php' );
}
add_action( 'bp_include', 'cacap_init' );

