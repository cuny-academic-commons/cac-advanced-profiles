<?php

/**
 * temp implementation - will be structured data
 */
class CACAP_Widget_Positions extends CACAP_Widget {
	public function __construct() {
		parent::init( array(
			'name' => __( 'Positions', 'cacap' ),
			'slug' => 'positions',
		) );
	}
}
