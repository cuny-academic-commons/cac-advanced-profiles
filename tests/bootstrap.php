<?php

if ( ! defined( 'BP_TESTS_DIR' ) ) {
	define( 'BP_TESTS_DIR', dirname( __FILE__ ) . '/../../buddypress/tests' );
}

if ( file_exists( BP_TESTS_DIR . '/bootstrap.php' ) ) :

	require_once getenv( 'WP_TESTS_DIR' ) . '/includes/functions.php';

	function _bootstrap_cacap() {
		// Make sure BP is installed and loaded first
		require BP_TESTS_DIR . '/includes/loader.php';

		// Then load CACAP
		require dirname( __FILE__ ) . '/../cac-advanced-profiles.php';
	}
	tests_add_filter( 'muplugins_loaded', '_bootstrap_cacap' );

	// We need pretty permalinks for some tests
	function _set_permalinks() {
		update_option( 'permalink_structure', '/%year%/%monthnum%/%day%/%postname%/' );
	}
	tests_add_filter( 'init', '_set_permalinks', 1 );

	// We need pretty permalinks for some tests
	function _flush() {
		flush_rewrite_rules();
	}
	tests_add_filter( 'init', '_flush', 1000 );

	require getenv( 'WP_TESTS_DIR' ) . '/includes/bootstrap.php';

	// Load the BP test files
	require BP_TESTS_DIR . '/includes/testcase.php';

	// include our testcase
//	require( dirname(__FILE__) . '/bp-docs-testcase.php' );

endif;
