<?php

/**
 * "Vital" object.
 *
 * @since 0.2
 */
class CACAP_Vital {
	/**
	 * @var array
	 */
	public $args = array();

	/**
	 * @var string
	 */
	public $id;

	/**
	 * @var string
	 */
	public $css_class;

	/**
	 * Constructor.
	 */
	public function __construct( $args = array() ) {
		$this->args = $args;

		if ( empty( $this->args['id'] ) ) {
			return;
		}

		$this->id = $this->args['id'];

		$this->set_up_css_class();
		$this->set_up_title();
		$this->set_up_content();
		$this->set_up_position();
	}

	/**
	 * Set up CSS class.
	 */
	protected function set_up_css_class() {
		if ( isset( $this->args['css_class'] ) ) {
			$c = $this->args['css_class'];
		} else {
			$c = 'cacap-vitals-' . $this->args['id'];
		}

		$this->css_class = esc_attr( $c );
	}

	/**
	 * Set up title.
	 */
	protected function set_up_title() {
		if ( isset( $this->args['title'] ) ) {
			$t = $this->args['title'];
		} else {
			$t = '';
		}

		$this->title = $t;
	}

	/**
	 * Set up content.
	 */
	protected function set_up_content() {
		if ( isset( $this->args['content'] ) ) {
			$c = $this->args['content'];
		} else {
			$c = '';
		}

		$this->content = $c;
	}

	/**
	 * Set up position.
	 */
	protected function set_up_position() {
		$default = 50;

		if ( isset( $this->args['position'] ) ) {
			// Parse separately in case someone wants a 0
			if ( is_int( $this->args['position'] ) ) {
				$p = $this->args['position'];
			} else {
				$p = intval( $this->args['position'] );
				if ( empty( $p ) ) {
					$p = $default;
				}
			}
		} else {
			$p = $default;
		}

		$this->position = $p;
	}
}

/**
 * Pull up a list of registered vitals.
 *
 * @todo Config params (allow empty, etc)
 *
 * @return array Array of CACAP_Vital objects.
 */
function cacap_vitals() {
	$vitals = apply_filters( 'cacap_vitals', array() );

	// Remove any invalid items
	foreach ( $vitals as $k => $v ) {
		if ( ! is_a( $v, 'CACAP_Vital' ) ) {
			unset( $vitals[ $k ] );
		}
	}

	// Sort by position
	usort( $vitals, function( $a, $b ) {
		if ( $a->position > $b->position ) {
			return 1;
		} else if ( $a->position < $b->position ) {
			return -1;
		} else {
			return 0;
		}
	} );

	return $vitals;
}
