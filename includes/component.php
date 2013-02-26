<?php

class CAC_Advanced_Profiles extends BP_Component {
	/**
	 * Constructor
	 */
	function __construct() {
		parent::start(
			'cacap',
			__( 'CAC Advanced Profiles', 'bp-docs' ),
			CACAP_PLUGIN_DIR
		);

		buddypress()->active_components[$this->id] = '1';

		$this->includes_dir = trailingslashit( CACAP_PLUGIN_DIR ) . trailingslashit( 'includes' );

		$this->setup_container();
		$this->setup_view();
	//	$this->includes();
	//	$this->setup_hooks();
	}

	function setup_container() {
		if ( empty( $this->container ) ) {
			if ( ! class_exists( 'CACAP_Container' ) ) {
				require( $this->includes_dir . 'container.php' );
			}

			$this->container = new CACAP_Container;
		}
	}

	function setup_view() {
		if ( empty( $this->view ) ) {
			$this->view = $this->container->get_view();
		}
	}

	function includes() {

	}

	function setup_hooks() {

	}
}

function cacap_load_component() {
	buddypress()->cacap = new CAC_Advanced_Profiles();
}
add_action( 'bp_init', 'cacap_load_component' );
