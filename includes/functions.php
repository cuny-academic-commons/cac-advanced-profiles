<?php

function cacap_includes_dir() {
	$includes_dir = '';

	if ( isset( buddypress()->cacap->includes_dir ) ) {
		$includes_dir = buddypress()->cacap->includes_dir;
	}

	return $includes_dir;
}

function cacap_assets_url() {
	return CACAP_PLUGIN_URL . '/assets/';
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
		'text'               => 'CACAP_Widget_Text',
		'academic-interests' => 'CACAP_Widget_Academic_Interests',
		'education'          => 'CACAP_Widget_Education',
		'positions'          => 'CACAP_Widget_Positions',
		'publications'       => 'CACAP_Widget_Publications',
		'rss'                => 'CACAP_Widget_RSS',
		'college'            => 'CACAP_Widget_College',
		'titlewidget'        => 'CACAP_Widget_Title',
		'twitter'            => 'CACAP_Widget_Twitter',
	);

	$types = apply_filters( 'cacap_widget_types', $types, $r );

	$widgets = array();
	foreach ( $types as $type => $class ) {
		if ( ! class_exists( $class ) ) {
			continue;
		}

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

function cacap_widget_type_is_disabled_for_user( $widget_type ) {
	$disabled = false;

	$wis = cacap_user_widget_instances();
	foreach ( $wis as $wi ) {
		if ( $widget_type->slug === $wi->widget_type->slug && ! $widget_type->allow_multiple ) {
			$disabled = true;
			break;
		}
	}

	return $disabled;
}

function cacap_field_is_visible_for_user( $field_id = 0, $displayed_user_id = 0, $current_user_id = 0 ) {
	if ( ! is_numeric( $field_id ) ) {
		$field_id = xprofile_get_field_id_from_name( $field_id );
	}

	if ( ! $field_id ) {
		return true;
	}

	$hidden_fields_for_user = bp_xprofile_get_hidden_fields_for_user( $displayed_user_id, $current_user_id );

	return ! in_array( $field_id, $hidden_fields_for_user );
}

function cacap_sanitize_content( $content ) {
	return wp_kses( $content, array(
		'a' => array(
			'href' => array(),
			'rel' => array(),
		),
		'b' => array(),
		'br' => array(),
		'div' => array(
			'align' => array(),
		),
		'h1' => array(),
		'h2' => array(),
		'h3' => array(),
		'i' => array(),
		'li' => array(),
		'p' => array(),
		'ol' => array(),
		'ul' => array(),
	) );
}

function cacap_is_commons_profile() {
	$retval = false;
	if ( bp_is_user() ) {
		if ( ! empty( $_GET['commons-profile'] ) && 1 == $_GET['commons-profile'] ) {
			$retval = true;
		}

		if ( ! bp_is_profile_component() ) {
			$retval = true;
		}
	}

	return apply_filters( 'cacap_is_commons_profile', $retval );
}

/**
 * URL for "public portfolio"
 */
function cacap_get_public_portfolio_url( $user_id ) {
	$url = trailingslashit( bp_core_get_user_domain( $user_id ) . buddypress()->profile->slug );
	return apply_filters( 'cacap_get_public_portfolio_url', $url, $user_id );
}

/**
 * URL for "commons portfolio"
 */
function cacap_get_commons_profile_url( $user_id ) {
	$url = trailingslashit( bp_core_get_user_domain( $user_id ) . buddypress()->profile->slug );
	$url = add_query_arg( 'commons-profile', '1', $url );
	return apply_filters( 'cacap_get_commons_profile_url', $url, $user_id );
}

/**
 * Get the header fields, as stored in the DB.
 */
function cacap_get_header_fields() {
	return bp_get_option( 'cacap_header_fields' );
}

/**
 * Get the profile field ID corresponding to the Brief Descriptor area.
 */
function cacap_get_brief_descriptor_field() {
	$fields = cacap_get_header_fields();

	$bd_field = isset( $fields['brief_descriptor'] ) ? intval( $fields['brief_descriptor'] ) : 0;
	return $bd_field;
}

/**
 * Get the profile field ID corresponding to the About You area.
 */
function cacap_get_about_you_field() {
	$fields = cacap_get_header_fields();

	$ay_field = isset( $fields['about_you'] ) ? intval( $fields['about_you'] ) : 0;
	return $ay_field;
}
