<?php

class CACAP_Widget_Short_Description extends CACAP_Widget {
	public function __construct() {
		$args = array(
			'name' => __( 'Short Description', 'cacap' ),
			'slug' => 'short-description',
			'context' => 'header',
		);
		parent::init( $args );
	}
}
