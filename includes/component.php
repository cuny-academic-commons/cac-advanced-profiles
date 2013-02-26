<?php

class CAC_Advanced_Profiles extends BP_Component {
	/**
	 * Constructor
	 */
	function __construct() {
		// For now, we require xprofile
		if ( ! bp_is_active( 'xprofile' ) ) {
			_doing_it_wrong( __METHOD__, __( 'CAC Advanced Profiles requires BP xprofile component' ), '0.1' );
			return;
		}

		parent::start(
			'cacap',
			__( 'CAC Advanced Profiles', 'cacap' ),
			CACAP_PLUGIN_DIR
		);

		buddypress()->active_components[$this->id] = '1';

		$this->includes_dir = trailingslashit( CACAP_PLUGIN_DIR ) . trailingslashit( 'includes' );

		$this->setup_container();
		$this->includes();
		$this->setup_view();
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
		require( $this->includes_dir . 'functions.php' );
	}

	function setup_hooks() {

	}

	public function get_user( $user_id ) {
		return $this->container->get_user( $user_id );
	}
}

function cacap_load_component() {
	buddypress()->cacap = new CAC_Advanced_Profiles();
}
add_action( 'bp_init', 'cacap_load_component' );
