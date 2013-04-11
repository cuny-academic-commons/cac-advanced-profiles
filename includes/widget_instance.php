<?php

class CACAP_Widget_Instance {
	protected $id;
	protected $data;

	public function __construct( $id = null ) {
		if ( ! is_null( $id ) ) {
			$this->id = intval( $id );
			$this->get_data();
		}
	}

	public function get_data() {
		global $wpdb, $bp;
		$value = $wpdb->get_var( $wpdb->prepare( "SELECT value FROM {$bp->xprofile->table_name_data} WHERE id = %d", intval( $id ) ) );
		$this->data = maybe_unserialize( $value );
		return $this->data;
	}

	public function create( $args = array() ) {
		$r = wp_parse_args( $args, array(
			'type' => '',
			'title' => '',
			'content' => '',
		) );

		$types = cacap_widget_types();
		if ( isset( $types[ $r['type'] ] ) ) {
			$widget_type = $types[ $r['type'] ];
		} else {
			// do something bad
			return;
		}

		$widget_instance_id = $widget_type->save_instance_for_user( $r );

		return $widget_instance_id;
	}
}
