<?php

class CACAP_Widget_Text extends CACAP_Widget {
	public function __construct() {
		parent::init( array(
			'name' => __( 'Text', 'cacap' ),
			'slug' => 'text',
		) );
	}
}
