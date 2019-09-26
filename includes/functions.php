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

	// Normalize quotes.
	$chr_map = array(
		// Windows codepage 1252
		"\xC2\x82" => "'", // U+0082⇒U+201A single low-9 quotation mark
		"\xC2\x84" => '"', // U+0084⇒U+201E double low-9 quotation mark
		"\xC2\x8B" => "'", // U+008B⇒U+2039 single left-pointing angle quotation mark
		"\xC2\x91" => "'", // U+0091⇒U+2018 left single quotation mark
		"\xC2\x92" => "'", // U+0092⇒U+2019 right single quotation mark
		"\xC2\x93" => '"', // U+0093⇒U+201C left double quotation mark
		"\xC2\x94" => '"', // U+0094⇒U+201D right double quotation mark
		"\xC2\x9B" => "'", // U+009B⇒U+203A single right-pointing angle quotation mark

		// Regular Unicode     // U+0022 quotation mark (")
							  // U+0027 apostrophe     (')
		"\xC2\xAB"     => '"', // U+00AB left-pointing double angle quotation mark
		"\xC2\xBB"     => '"', // U+00BB right-pointing double angle quotation mark
		"\xE2\x80\x98" => "'", // U+2018 left single quotation mark
		"\xE2\x80\x99" => "'", // U+2019 right single quotation mark
		"\xE2\x80\x9A" => "'", // U+201A single low-9 quotation mark
		"\xE2\x80\x9B" => "'", // U+201B single high-reversed-9 quotation mark
		"\xE2\x80\x9C" => '"', // U+201C left double quotation mark
		"\xE2\x80\x9D" => '"', // U+201D right double quotation mark
		"\xE2\x80\x9E" => '"', // U+201E double low-9 quotation mark
		"\xE2\x80\x9F" => '"', // U+201F double high-reversed-9 quotation mark
		"\xE2\x80\xB9" => "'", // U+2039 single left-pointing angle quotation mark
		"\xE2\x80\xBA" => "'", // U+203A single right-pointing angle quotation mark
	);
	$chr = array_keys  ( $chr_map ); // but: for efficiency you should
	$rpl = array_values( $chr_map ); // pre-calculate these two arrays
	$content = str_replace( $chr, $rpl, html_entity_decode( $content, ENT_QUOTES, "UTF-8" ) );

	// Remove illegal tags.
	$dom = new DOMDocument;
	$dom->loadHTML( '<?xml encoding="utf-8" ?>' . $content );
	$xPath = new DOMXPath( $dom );
	foreach ( $dom->getElementsByTagName( 'style' ) as $style ) {
		$style->parentNode->removeChild( $style );
	}
	$content = $dom->saveHTML();

	// KSES sanitization.
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

		// Change Avatar, etc.
		if ( bp_is_profile_component() && bp_current_action() && ! bp_is_current_action( 'edit' ) && ! bp_is_current_action( 'public' ) ) {
			$retval = true;
		}
	}

	return apply_filters( 'cacap_is_commons_profile', $retval );

	return bp_is_user() && ( empty( $_GET['commons-profile'] ) || 1 != $_GET['commons-profile'] || ! bp_is_profile_component()) ;
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
