<?php

function cacap_includes_dir() {
	$includes_dir = '';

	if ( isset( buddypress()->cacap->includes_dir ) ) {
		$includes_dir = buddypress()->cacap->includes_dir;
	}

	return $includes_dir;
}

function cacap_user_widget_instances() {
	// @todo abstract
	$user_id = bp_displayed_user_id();

	$user = buddypress()->cacap->get_user( $user_id );
	return $user->get_widget_instances();
}

function cacap_widget_types() {
	// hardcoding for now
	$types = array(
		'text' => 'CACAP_Widget_Text',
		'academic-interests' => 'CACAP_Widget_AcademicInterests',
	);

	$widgets = array();
	foreach ( $types as $type => $class ) {
		$widgets[ $type ] = new $class;
	}

	return $widgets;
}

function cacap_html_gen() {
	static $wpsdl;

	if ( empty( $wpsdl ) ) {
		require_once trailingslashit( CACAP_PLUGIN_DIR ) . 'lib/wp-sdl/wp-sdl.php';
		$wpsdl = WP_SDL::support( '1.0' );
	}

	return $wpsdl->html();
}

