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

		$this->includes();
		$this->setup_view();
	//	$this->setup_hooks();
	}

	function setup_view() {
		if ( empty( $this->view ) ) {
			if ( ! class_exists( 'CACAP_View' ) ) {
				require( $this->includes_dir . 'view.php' );
			}

			$this->view = new CACAP_View();
		}
	}

	function includes() {
		require( $this->includes_dir . 'functions.php' );
	}

	function setup_hooks() {

	}

	public function get_user( $user_id ) {
		if ( ! class_exists( 'CACAP_User' ) ) {
			require( $this->includes_dir . 'user.php' );
		}

		return new CACAP_User( $user_id );
	}
}

function cacap_load_component() {
	buddypress()->cacap = new CAC_Advanced_Profiles();
}
add_action( 'bp_init', 'cacap_load_component' );
