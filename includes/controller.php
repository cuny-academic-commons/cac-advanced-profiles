<?php

class CACAP_Controller {

	public function __construct() {
		add_action( 'bp_actions', array( $this, 'catch_profile_edit' ), 5 );
		add_action( 'bp_actions', array( $this, 'catch_widget_create' ), 5 );
		add_filter( 'bp_located_template', array( $this, 'filter_top_level_template' ) );
		add_filter( 'bp_get_template_stack', array( $this, 'filter_template_stack' ) );
		add_filter( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ) );
		add_filter( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_filter( 'body_class', array( $this, 'body_class' ) );

		add_action( 'xprofile_updated_profile', array( $this, 'save_profile_data' ) );

		// AJAX handlers
		add_action( 'wp_ajax_cacap_reorder_widgets', array( $this, 'reorder_widgets' ) );
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
		}
	}

	public function catch_widget_create() {
		if ( bp_is_user_profile_edit() && ! empty( $_POST['cacap-widget-create-submit'] ) ) {
			// CSRF protection
			check_admin_referer( 'cacap_new_widget' );

			// Cap check
			if ( ! bp_is_my_profile() && ! current_user_can( 'bp_moderate' ) ) {
				return;
			}

			if ( isset( $_POST['cacap-widget-type'] ) ) {
				$widget_type = $_POST['cacap-widget-type'];
			}

			$widget_types = cacap_widget_types();

			if ( ! isset( $widget_type ) || ! isset( $widget_types[ $widget_type ] ) ) {
				return;
			}

			$title = isset( $_POST['cacap-new-widget-title'] ) ? $_POST['cacap-new-widget-title'] : '';
			$content = isset( $_POST['cacap-new-widget-content'] ) ? $_POST['cacap-new-widget-content'] : '';

			$user = new CACAP_User( bp_displayed_user_id() );
			$result = $user->create_widget_instance( array(
				'widget_type' => $widget_type,
				'title' => $title,
				'content' => $content,
			) );
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
			return $stack;
		}

		$stack[] = CACAP_PLUGIN_DIR . 'templates';
		return $stack;
	}

	public function enqueue_styles() {
		wp_enqueue_style( 'cacap-css', CACAP_PLUGIN_URL . '/assets/css/screen.css' );
	}

	public function enqueue_scripts() {
		wp_register_script( 'cacap-autogrow', CACAP_PLUGIN_URL . '/assets/js/autogrow.min.js', array( 'jquery' ) );
		wp_enqueue_script(
			'cacap',
			CACAP_PLUGIN_URL . '/assets/js/cacap.js',
			array(
				'jquery',
				'jquery-ui-sortable',
				'cacap-autogrow',
			)
		);
	}

	public function body_class( $classes ) {
		if ( bp_is_user_profile() ) {
			$classes[] = 'cacap';
		}

		return $classes;
	}

	public function save_profile_data() {
		$user = new CACAP_User( bp_displayed_user_id() );

		// Widget order
		if ( isset( $_POST['cacap-widget-order'] ) ) {
			$user->save_widget_order( $_POST['cacap-widget-order'] );
		}

		// The widgets themselves
		// Use the widget-order array as a list of keys to check
		if ( isset( $_POST['cacap-widget-order'] ) ) {
			$widget_order = explode( ',', $_POST['cacap-widget-order'] );
			// Trim the 'cacap-widget-' bit
			foreach ( $widget_order as &$wo ) {
				$wo = substr( $wo, 13 );
			}

			// First check to see if any have been deleted
			foreach ( cacap_user_widget_instances() as $wi ) {
				if ( ! in_array( $wi->css_id, $widget_order ) ) {
					$user->delete_widget_instance( $wi->key );
				}
			}

			// Now edit and add
			foreach ( $widget_order as $key ) {
				$title       = isset( $_POST[ $key ]['title'] ) ? $_POST[ $key ]['title'] : '';
				$content     = isset( $_POST[ $key ]['content'] ) ? $_POST[ $key ]['content'] : '';
				$widget_type = isset( $_POST[ $key ]['widget_type'] ) ? $_POST[ $key ]['widget_type'] : '';

				if ( 0 === strpos( array_pop( explode( '-', $key ) ), 'newwidget' ) ) {
					$user->create_widget_instance( array(
						'widget_type' => $widget_type,
						'title'       => $title,
						'content'     => $content,
					) );
				} else {
					$user->save_widget_instance( array(
						'key'         => $key,
						'widget_type' => $widget_type,
						'title'       => $title,
						'content'     => $content,
					) );
				}
			}
		}
	}
}
