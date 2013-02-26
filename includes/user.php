<?php

class CACAP_User {
	protected $user_id;

	function __construct( $user_id = 0 ) {
		$this->set_user_id( $user_id );
	}

	public function set_user_id( $user_id = 0 ) {
		$this->user_id = absint( $user_id );
	}

	public function get_user_id() {
		return $this->user_id;
	}

	public function get_widgets() {
		if ( ! isset( $this->widgets ) ) {
			$widget_instance_ids = $this->get_widget_instance_ids();
			$container = new CACAP_Container();

			foreach ( $widget_instance_ids as $widget_instance_id ) {
				$this->widgets[] = $container->get_widget_instance( $widget_instance_id );
			}
		}
	}

	public function get_widget_instance_ids() {
		$widget_instance_ids = bp_get_user_meta( $this->user_id, 'cacap_widget_instance_ids', true );
		if ( ! is_array( $widget_instance_ids ) ) {
			$widget_instance_ids = array();
		}
		return $widget_instance_ids;
	}
}
