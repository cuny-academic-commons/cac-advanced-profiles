<?php

abstract class CACAP_Widget {
	/**
	 * Initialize a widget type
	 *
	 * All extending classes should call this method in their constructors
	 *
	 * @since 1.0
	 *
	 * @param array $args
	 */
	protected function init( $args = array() ) {
		$r = wp_parse_args( $args, array(
			'name' => '',
			'slug' => '',

			'allow_custom_title' => false,

			'context' => 'body',
			'position' => 50, // @todo
		) );

		if ( empty( $r['name'] ) || empty( $r['slug'] ) ) {
			return new WP_Error( 'missing_params', __( '"name" and "slug" are required parameters', 'cacap' ) );
		}

		$this->name = $r['name'];

		// @todo unique?
		$this->slug = $r['slug'];

		$this->allow_custom_title = $r['allow_custom_title'];

		// @todo whitelist? how to make extensible?
		$this->context = $r['context'];
	}

	public function option_markup() {
		return sprintf(
			'<option value="%s">%s</option>',
			esc_attr( $this->slug ),
			esc_attr( $this->name )
		);
	}

	/**
	 * Save widget instance for a given user
	 *
	 * In this base method, it's assumed that you're storing data in the BP
	 * xprofile tables, and that the field name will be the same as the
	 * 'title' attribute passed in the $args param (or, as a fallback,
	 * $this->name). If your widget's data schema does not match this, you
	 * should override this method in your widget class.
	 *
	 * @since 1.0
	 *
	 * @param array $args
	 * @return array See CACAP_Widget_Instance::format_instance() for format
	 */
	public function save_instance_for_user( $args = array() ) {
		$r = wp_parse_args( $args, array(
			'user_id' => 0,
			'title' => $this->name,
			'content' => '',
		) );

		if ( ! $r['user_id'] ) {
			return false;
		}

		if ( ! $r['title'] ) {
			$r['title'] = $this->name;
		}

		if ( xprofile_set_field_data( $r['title'], absint( $r['user_id'] ), $r['content'] ) ) {
			return CACAP_Widget_Instance::format_instance( array(
				'user_id' => $r['user_id'],
				'key' => $r['title'],
				'value' => $r['content'],
				'widget_type' => $this->slug,
			) );
		} else {
			// phooey
		}
	}

	public function get_instance_for_user( $args = array() ) {
		$r = wp_parse_args( $args, array(
			'user_id' => 0,
			'key' => null,
		) );

		return xprofile_get_field_data( $this->name, absint( $r['user_id'] ) );
	}

	/**
	 * Generates the markup for creating a new widget
	 *
	 * @since 1.0
	 */
	public function create_widget_markup() {
		$html = '';
		$html .= $this->create_title_markup();
		$html .= $this->create_content_markup();
		return $html;
	}

	/**
	 * Generates the markup for the Title section of Create Widget
	 *
	 * @since 1.0
	 */
	public function create_title_markup() {
		$id = $name = 'cacap-new-widget-title';

		$html = sprintf(
			'<label for="%s">%s</label>',
			$id,
			__( 'Title', 'cacap' )
		);

		if ( $this->allow_custom_title ) {
			$disabled = '';
			$value = '';
		} else {
			$disabled = ' disabled="disabled" ';
			$value = esc_attr( $this->name );
		}

		$html .= sprintf(
			'<input %s type="text" name="%s" id="%s" value="%s"',
			$disabled,
			$name,
			$id,
			$value
		);

		return $html;
	}

	public function create_content_markup() {
		$id = $name = 'cacap-new-widget-content';

		$html = sprintf(
			'<label for="%s">%s</label>',
			$id,
			__( 'Content', 'cacap' )
		);

		$html .= sprintf(
			'<textarea name="%s" id="%s"></textarea>',
			$name,
			$id
		);

		return $html;
	}

	/**
	 * Returns display-ready value
	 *
	 * Override this in a widget type class if the widget type stores a
	 * complex object/array for 'value', and the display value needs to be
	 * parsed out.
	 *
	 * @param mixed $value
	 * @return sting
	 */
	public function get_display_value_from_value( $value ) {
		return $value;
	}

	public function display_title_markup( $value ) {
		return esc_html( $this->name );
	}

	// @todo use bp xprofile functions for formatting potential arrays, etc
	public function display_content_markup( $value ) {
		return esc_html( $value );
	}

	public function edit_title_markup( $value, $key ) {
		$disabled = $this->allow_custom_title ? '' : 'disabled="disabled" ';
		return '<input class="cacap-edit-input" name="' . esc_attr( $key ) . '" value="' . esc_attr( $this->name ) . '" ' . $disabled . '/>';
	}

	public function edit_content_markup( $value, $key ) {
		return '<textarea class="cacap-edit-input" name="' . esc_attr( $key ) . '">' . esc_attr( $value ) . '</textarea>';
	}
}
