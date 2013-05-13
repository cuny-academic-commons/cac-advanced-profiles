<?php

function cacap_includes_dir() {
	$includes_dir = '';

	if ( isset( buddypress()->cacap->includes_dir ) ) {
		$includes_dir = buddypress()->cacap->includes_dir;
	}

	return $includes_dir;
}

function cacap_user_widget_instances( $args = array() ) {
	// @todo abstract
	$user_id = bp_displayed_user_id();

	$user = buddypress()->cacap->get_user( $user_id );
	return $user->get_widget_instances( $args );
}

function cacap_widget_types( $args = array() ) {
	$r = wp_parse_args( $args, array(
		'context' => 'body',
	) );

	// hardcoding for now
	$types = array(
		'name' => 'CACAP_Widget_Name',
		'short-description' => 'CACAP_Widget_Short_Description',
		'text' => 'CACAP_Widget_Text',
		'academic-interests' => 'CACAP_Widget_Academic_Interests',
	);

	$widgets = array();
	foreach ( $types as $type => $class ) {
		$widgets[ $type ] = new $class;
	}

	// Filter for 'context'
	foreach ( $widgets as $widget_key => $widget ) {
		if ( $r['context'] !== $widget->context ) {
			unset( $widgets[ $widget_key ] );
		}
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

function cacap_widget_order() {
	$wis = cacap_user_widget_instances();
	$ids = array();
	foreach ( $wis as $wi ) {
		$ids[] = 'cacap-widget-' . $wi->css_id;
	}
	return esc_attr( implode( ',', $ids ) );
}
