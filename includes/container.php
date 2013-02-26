<?php

/**
 * Dependency injection container for CAC AP
 */
class CACAP_Container {

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

	public function get_db() {
	}
}
