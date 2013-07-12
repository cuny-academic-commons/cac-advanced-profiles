<?php

class CACAP_Widget_Academic_Interests extends CACAP_Widget {
	public function __construct() {
		parent::init( array(
			'name' => __( 'Academic Interests', 'cacap' ),
			'slug' => 'academic-interests',
		) );
	}

	public function display_content_markup( $value ) {
		return $value;
	}

}
