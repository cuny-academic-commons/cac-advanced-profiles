<?php

class CACAP_Header_Field_Short_Description extends CACAP_Header_Field {
	public function __construct() {
		$args = array(
			'field_name' => 'Short Description',
			'field_type' => 'textarea',
		);
		parent::init( $args );
	}
}
