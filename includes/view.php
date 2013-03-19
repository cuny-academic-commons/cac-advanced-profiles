<?php

class CACAP_View {

	public function __construct() {
		add_action( 'bp_actions', array( $this, 'catch_profile_edit' ), 5 );
		add_filter( 'bp_located_template', array( $this, 'filter_top_level_template' ) );
		add_filter( 'bp_get_template_stack', array( $this, 'filter_template_stack' ) );
	}

	public function catch_profile_edit() {
		if ( bp_is_user_profile_edit() && ! empty( $_POST['cacap-edit-submit'] ) ) {
			// @todo nonce
			$submitted = array();
			foreach ( cacap_header_fields() as $field_key => $field ) {
				if ( isset( $_POST[ $field->get_field_input_id() ] ) ) {
					$submitted[ $field_key ] = $_POST[ $field->get_field_input_id() ];
				}
			}

			$user = new CACAP_User( bp_displayed_user_id() );
			$result = $user->save_fields( $submitted );
			var_dump( $result ); die();
		}
	}

	/**
	 * CACAP hijacks the entire top-level template, including header, sidebar, etc
	 */
	public function filter_top_level_template( $template ) {
		if ( ! bp_displayed_user_id() ) {
			return $template;
		}

		$template = $this->locate_top_level_template();
		return $template;
	}

	/**
	 * Finds cacap.php
	 *
	 * The logic here allows you to put it in your own theme, your own BP
	 * template pack, or a number of other places. If no custom file is
	 * found, the packaged template is used as a fallback.
	 */
	public function locate_top_level_template() {
		$template = bp_locate_template( 'cacap/home.php' );
		return $template;
	}

	/**
	 * Add our local plugin template directory to the bottom of the
	 * template stack
	 */
	public function filter_template_stack( $stack ) {
		if ( ! bp_displayed_user_id() ) {
			return $template;
		}

		$stack[] = CACAP_PLUGIN_DIR . 'templates';
		return $stack;
	}
}
