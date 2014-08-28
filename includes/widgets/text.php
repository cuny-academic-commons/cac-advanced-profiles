<?php

class CACAP_Widget_Text extends CACAP_Widget {
	public function __construct() {
		parent::init( array(
			'name' => __( 'Free Entry', 'cacap' ),
			'slug' => 'text',
			'allow_custom_title' => true,
			'allow_multiple' => true,
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
			'key' => '',
			'user_id' => 0,
			'title' => '',
			'content' => '',
		) );

		// @todo better error reporting
		if ( ! $r['user_id'] || ! $r['title'] ) {
			return false;
		}

		// Sanitize
		$r['title'] = strip_tags( $r['title'] ); 
		$r['content'] = cacap_sanitize_content( $r['content'] );

		$meta_value = array(
			'title' => $r['title'],
			'content' => $r['content'],
		);

		// @todo - uniqueness? what about updating existing?
		$meta_key = empty( $r['key'] ) ? 'cacap_widget_instance_' . sanitize_title_with_dashes( $r['title'] ) : $r['key'];

		if ( update_user_meta( $r['user_id'], $meta_key, $meta_value ) ) {
			return CACAP_Widget_Instance::format_instance( array(
				'user_id' => $r['user_id'],
				'key' => $meta_key,
				'value' => $meta_value,
				'widget_type' => $this->slug,
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

	public function get_display_value_from_value( $value ) {
		return $value['content'];
	}

	/**
	 * Return the HTML-ready title of the widget
	 *
	 * We override the parent method because the title is stored in the
	 * $value variable
	 *
	 * @param array $value
	 * @return string
	 */
	public function display_title_markup( $value ) {
		return esc_html( $value['title'] );
	}

	public function edit_title_markup( $value, $key ) {
		$title = isset( $value['title'] ) ? $value['title'] : '';
		$html  = '<article class="editable-content" contenteditable="true">' . $title . '</article>';
		$html .= '<input name="' . esc_attr( $key ) . '[title]" class="editable-content-stash" type="hidden" value="' . esc_attr( $title ) . '" />';
		return $html;
	}

	public function edit_content_markup( $value, $key ) {
		$html  = '<article class="editable-content richtext">' . $value['content'] . '</article>';
		$html .= '<input name="' . esc_attr( $key ) . '[content]" class="editable-content-stash" type="hidden" value="' . esc_attr( $value['content'] ) . '" />';
		return $html;
	}
}
