<?php

class CACAP_Header_Field_Name extends CACAP_Header_Field {
	public function __construct() {
		$args = array(
			'field_name' => 'Name',
			'field_type' => 'text',
		);
		parent::init( $args );
	}
}
