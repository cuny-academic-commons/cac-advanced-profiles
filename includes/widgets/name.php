<?php

class CACAP_Widget_Name extends CACAP_Widget {
	public function __construct() {
		$args = array(
			'name' => __( 'Name', 'cacap' ),
			'slug' => 'name',
			'context' => 'header',
		);
		parent::init( $args );
	}
}
