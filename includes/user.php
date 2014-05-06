<?php

class CACAP_User {
	protected $user_id;
	protected $widget_instances;

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

	public function get_widget_instances( $args = array() ) {
		$r = wp_parse_args( $args, array(
			'context' => 'body',
			'omit_legacy_positions' => true,
		) );

		if ( is_null( $this->widget_instances ) ) {
			$this->widget_instances = array();

			$widget_instance_data = $this->get_widget_instance_data();

			// @todo Should probably be configurable by user?
			$widget_types = cacap_widget_types();

			// See whether we have a Positions widget, for later reference
			$omit_title_college = false;
			if ( $r['omit_legacy_positions'] ) {
				foreach ( $widget_instance_data as $widget_instance_datum ) {
					if ( isset( $widget_instance_datum['widget_type'] ) && 'positions' === $widget_instance_datum['widget_type'] ) {
						$omit_title_college = true;
						break;
					}
				}
			}

			foreach ( $widget_instance_data as $widget_instance_datum ) {
				$key = $widget_instance_datum['key'];
				$widget_type = isset( $widget_instance_datum['widget_type'] ) ? $widget_instance_datum['widget_type'] : '';

				if ( $omit_title_college && in_array( $widget_type, array( 'college', 'titlewidget' ) ) ) {
					continue;
				}

				if ( $key && isset( $widget_types[ $widget_type ] ) ) {
					$this->widget_instances[ $key ] = new CACAP_Widget_Instance( $widget_instance_datum );
				}
			}
		}

		$widget_instances = $this->widget_instances;

		// Filter by context
		foreach ( $widget_instances as $instance_key => $instance ) {
			if ( 'all' !== $r['context'] && $instance->widget_type->context !== $r['context'] ) {
				unset( $widget_instances[ $instance_key ] );
			}
		}

		// Sort by position
		uasort( $widget_instances, array( $this, 'sort_widget_instances_callback' ) );

		return $widget_instances;
	}

	protected function sort_widget_instances_callback( $a, $b ) {
		if ( $a->position === $b->position ) {
			return 0;
		}

		return $a->position > $b->position ? 1 : -1;
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
			'widget_type' => '',
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

	public function save_widget_instance( $args = array() ) {
		$r = wp_parse_args( $args, array(
			'key'         => '',
			'widget_type' => '',
			'title'       => '',
			'content'     => '',
		) );

		$r['user_id'] = $this->user_id;

		$widget_instance = new CACAP_Widget_Instance( array(
			'key'         => $r['key'],
			'widget_type' => $r['widget_type'],
			'user_id'     => $r['user_id'],
		) );

		// todo - what if it's new
		$widget_instance->widget_type->save_instance_for_user( array(
			'key' => $widget_instance->key,
			'user_id' => $widget_instance->user_id,
			'title' => $r['title'],
			'content' => $r['content'],
		) );
	}

	public function delete_widget_instance( $key ) {
		// For now, save the data and delete the metadata only
		$existing = $this->get_widget_instance_data();
		unset( $existing[ $key ] );
		bp_update_user_meta( $this->user_id, 'cacap_widget_instance_data', $existing );
	}

	public function store_widget_instance( $data ) {
		$existing = $this->get_widget_instance_data();
		$existing[ $data['key'] ] = $data;
		bp_update_user_meta( $this->user_id, 'cacap_widget_instance_data', $existing );
	}

	public function refresh_widget_instances() {
		$this->widget_instances = null;
		$this->get_widget_instances();
	}

	public function save_widget_order( $order ) {
		$widget_order = array_flip( explode( ',', $order ) );

		foreach ( cacap_user_widget_instances() as $widget_instance ) {
			if ( isset( $widget_order[ 'cacap-widget-' . $widget_instance->css_id ] ) ) {
				$widget_instance->position = $widget_order[ 'cacap-widget-' . $widget_instance->css_id ];

				$data = CACAP_Widget_Instance::format_instance( array(
					'user_id' => $widget_instance->user_id,
					'key' => $widget_instance->key,
					'widget_type' => $widget_instance->widget_type->slug,
					'position' => $widget_instance->position,
				) );

				$this->store_widget_instance( $data );
			}
		}
	}
}
