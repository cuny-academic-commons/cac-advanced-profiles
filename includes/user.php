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

			if ( ! class_exists( 'CACAP_Widget_Instance' ) ) {
				require( cacap_includes_dir() . 'widget_instance.php' );
			}

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
}
