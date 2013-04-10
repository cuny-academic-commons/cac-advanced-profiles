<?php

class CACAP_User {
	protected $user_id;
	protected $widgets;

	function __construct( $user_id = 0 ) {
		$this->set_user_id( $user_id );
	}

	public function set_user_id( $user_id = 0 ) {
		$this->user_id = absint( $user_id );
	}

	public function get_user_id() {
		return $this->user_id;
	}

	public function save_fields( $submitted = array() ) {
		$success = true;
		$header_fields = cacap_header_fields();

		foreach ( $submitted as $field_key => $field_value ) {
			$field = $header_fields[ $field_key ];

			$field->set_value( $field_value );
			$saved = $field->save();

			if ( ! $saved && $success ) {
				$success = false;
			}
		}

		return $success;
	}

	public function get_widgets() {
		if ( is_null( $this->widgets ) ) {
			$this->widgets = array();

			$widget_instance_ids = $this->get_widget_instance_ids();

			foreach ( $widget_instance_ids as $widget_instance_id ) {
				$this->widgets[] = new CACAP_Widget_Instance( $widget_instance_id );
			}
		}

		return $this->widgets;
	}

	public function get_widget_instance_ids() {
		$widget_instance_ids = bp_get_user_meta( $this->user_id, 'cacap_widget_instance_ids', true );
		if ( ! is_array( $widget_instance_ids ) ) {
			$widget_instance_ids = array();
		}
		return $widget_instance_ids;
	}

	public function create_widget_instance( $args = array() ) {
		$r = wp_parse_args( $args, array(
			'type' => '',
			'title' => '',
			'content' => '',
		) );

		$r['user_id'] = $this->user_id;
		// @todo error/empty checking

		$widget_instance = new CACAP_Widget_Instance();
		if ( $widget_instance->create( $r ) ) {
			// get the widget instance id and store
		} else {
			// cry me a river
		}
	}
}
