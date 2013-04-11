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

		$meta_value = array(
			'title' => $r['title'],
			'content' => $r['content'],
		);

		// @todo - uniqueness? what about updating existing?
		$meta_key = 'cacap_widget_instance_' . sanitize_title_with_dashes( $r['title'] );

		if ( update_user_meta( $r['user_id'], $meta_key, $meta_value ) ) {
			return CACAP_Widget_Instance::format_instance( array(
				'user_id' => $r['user_id'],
				'key' => $meta_key,
				'value' => $meta_value,
				'type' => $this->slug,
			) );
		} else {
			// do something bad
		}
	}

	public function get_instance_for_user( $args = array() ) {
		$r = wp_parse_args( $args, array(
			'user_id' => 0,
			'key' => null,
		) );

		return get_user_meta( absint( $r['user_id'] ), $r['key'], true );
	}
}
