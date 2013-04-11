<?php

class CACAP_Widget_Instance {
	protected $user_id;
	protected $type;
	protected $key;
	protected $value;

	public function __construct( $data = null ) {
		if ( ! is_null( $data ) ) {
			if ( ! empty( $data['type'] ) && ! empty( $data['key'] ) && ! empty( $data['user_id'] ) ) {

				$widget_types = cacap_widget_types();
				if ( isset( $widget_types[ $data['type'] ] ) ) {
					$this->type = new $widget_types[ $data['type'] ];
				}

				$this->key = $data['key'];
				$this->user_id = $data['user_id'];

				$this->value = $this->get_value();
			}
		}
	}

	public function get_value() {
		return $this->type->get_instance_for_user( array(
			'user_id' => $this->user_id,
			'key' => $this->key,
		) );
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

		$widget_instance_data = $widget_type->save_instance_for_user( $r );

		return $widget_instance_data;
	}

	public static function format_instance( $args = array() ) {
		$r = wp_parse_args( $args, array(
			'user_id' => 0,
			'key' => '',
			'type' => '',
		) );

		$retval = array(
			'user_id' => $r['user_id'],
			'key' => $r['key'],
			'type' => $r['type'],
		);

		return $retval;
	}
}
