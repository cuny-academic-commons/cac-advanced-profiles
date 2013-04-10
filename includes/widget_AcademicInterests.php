<?php

class CACAP_Widget_AcademicInterests extends CACAP_Widget {
	public function __construct() {
		parent::init( array(
			'name' => __( 'Academic Interests', 'cacap' ),
			'slug' => 'academic-interests',
		) );
	}
}
