<?php

/**
 * Dependency injection container for CAC AP
 */
class CACAP_Container {

	protected $includes_dir;
	protected $view;
	protected $model;

	public function __construct() {
		$this->includes_dir = trailingslashit( CACAP_PLUGIN_DIR ) . trailingslashit( 'includes' );
	}

	public function get_view() {
		if ( ! isset( $this->view ) ) {
			if ( ! class_exists( 'CACAP_View' ) ) {
				require( $this->includes_dir . 'view.php' );
			}

			$this->view = new CACAP_View();
		}

		return $this->view;
	}

	public function get_model() {
		if ( ! isset( $this->model ) ) {
			$this->model = new CACAP_Model();
		}

		return $this->model;
	}

	public function get_user( $user_id = 0 ) {
		if ( ! class_exists( 'CACAP_User' ) ) {
			require( $this->includes_dir . 'user.php' );
		}

		return new CACAP_User( $user_id );
	}

	public function get_widget_instance( $widget_instance_id ) {
		if ( ! class_exists( 'CACAP_Widget_Instance' ) ) {
			require( $this->includes_dir . 'widget_instance.php' );
		}

		return new CACAP_Widget_Instance( $widget_instance_id );
	}

	public function get_widget_instance_schema() {
		if ( ! isset( $this->widget_instance_schema ) ) {
			if ( ! class_exists( 'CACAP_Widget_Instance_Schema' ) ) {
				require( $this->includes_dir . 'widget_instance_schema.php' );
			}

			$this->widget_instance_schema = new CACAP_Widget_Instance_Schema();
		}

		return $this->widget_instance_schema;
	}

	public function get_db() {
	}
}
