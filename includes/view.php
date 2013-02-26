<?php

class CACAP_View {
	public function __construct() {
		add_filter( 'bp_located_template', array( $this, 'filter_top_level_template' ) );
	}

	public function filter_top_level_template( $template ) {
		if ( ! bp_displayed_user_id() ) {
			return $template;
		}

		$template = $this->locate_top_level_template();
		return $template;
	}

	public function locate_top_level_template() {
		add_filter( 'bp_get_template_stack', array( $this, 'filter_template_stack' ) );
		$template = bp_locate_template( 'cacap.php' );
		return $template;
	}

	/**
	 * Add our local plugin template directory to the bottom of the
	 * template stack
	 */
	public function filter_template_stack( $stack ) {
		$stack[] = CACAP_PLUGIN_DIR . 'templates';
		return $stack;
	}
}
