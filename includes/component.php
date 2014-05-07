<?php

class CAC_Advanced_Profiles extends BP_Component {
	public $current_user = null;
	public $view;

	/**
	 * Constructor
	 */
	public function __construct() {
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
		$this->setup_controller();

		do_action( 'cacap_init' );
	}

	public function setup_controller() {
		if ( empty( $this->controller ) ) {
			if ( ! class_exists( 'CACAP_Controller' ) ) {
				require( $this->includes_dir . 'controller.php' );
			}

			$this->controller = new CACAP_Controller();
		}
	}

	public function includes( $includes = array() ) {
		require( $this->includes_dir . 'functions.php' );
		require( $this->includes_dir . 'user.php' );
		require( $this->includes_dir . 'widget.php' );
		require( $this->includes_dir . 'default-widgets.php' );
		require( $this->includes_dir . 'widget_instance.php' );
		require( $this->includes_dir . 'vital.php' );

		if ( is_admin() && current_user_can( 'bp_moderate' ) ) {
			require( $this->includes_dir . 'admin/admin.php' );
		}
	}

	public function get_user( $user_id ) {
		if ( ! class_exists( 'CACAP_User' ) ) {
			require( $this->includes_dir . 'user.php' );
		}

		return new CACAP_User( $user_id );
	}

	public function get_current_user() {
		if ( is_null( $this->current_user ) && bp_displayed_user_id() ) {
			return $this->get_user( bp_displayed_user_id() );
		}
	}
}

function cacap_load_component() {
	buddypress()->cacap = new CAC_Advanced_Profiles();
}
add_action( 'bp_init', 'cacap_load_component' );
