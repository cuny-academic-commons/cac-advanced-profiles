<?php

class CACAP_Widget_Text extends CACAP_Widget {
	public function __construct() {
		parent::init( array(
			'name' => __( 'Text', 'cacap' ),
			'slug' => 'text',
			'allow_custom_title' => true,
		) );
	}

	/**
	 * Saves instance of Text widget for user
	 *
	 * Overrides the parent method, because on the default schema, Text
	 * widgets are not stored in xprofile data tables (since users can
	 * create arbitrary Text widgets, making it impossible to map onto
	 * xprofile fields)
	 *
	 * @since 1.0
	 */
	public function save_instance_for_user( $args = array() ) {
		$r = wp_parse_args( $args, array(
			'user_id' => 0,
			'title' => '',
			'content' => '',
		) );

		// @todo better error reporting
		if ( ! $r['user_id'] || ! $r['title'] ) {
			return false;
		}

		return cacap_profile_data_schema()->save_custom_flat_data_for_user( $r['title'], absint( $r['user_id'] ), $r['content'] );
	}
}
