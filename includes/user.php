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

			$widget_instance_data = $this->get_widget_instance_data();

			foreach ( $widget_instance_data as $widget_instance_datum ) {
				$key = $widget_instance_datum['key'];
				$this->widgets[ $key ] = new CACAP_Widget_Instance( $widget_instance_datum );
			}
		}

		return $this->widgets;
	}

	public function get_widget_instance_data() {
		$widget_instance_data = bp_get_user_meta( $this->user_id, 'cacap_widget_instance_data', true );
		if ( ! is_array( $widget_instance_data ) ) {
			$widget_instance_data = array();
		}
		return $widget_instance_data;
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
		$widget_instance_data = $widget_instance->create( $r );

		if ( ! empty( $widget_instance_data ) ) {
			$this->store_widget_instance( $widget_instance_data );
			$this->refresh_widget_instances();
		} else {
			// cry me a river
		}
	}

	public function store_widget_instance( $data ) {
		$existing = $this->get_widget_instance_data();
		$existing[ $data['key'] ] = $data;
		bp_update_user_meta( $this->user_id, 'cacap_widget_instance_data', $existing );
	}

	public function refresh_widget_instances() {
		$this->widgets = null;
		$this->get_widgets();
	}
}
