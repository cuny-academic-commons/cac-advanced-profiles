<?php

if ( ! class_exists( 'CACAP_Header_Field' ) ) {
	require( cacap_includes_dir() . 'header_field.php' );
}

function cacap_header_fields() {
	return array(
		'name' => new CACAP_Name_Header_Field,
		'short_description' => new CACAP_ShortDescription_Header_Field,
	);
}

/**
 * Defines the header field classes
 */
class CACAP_Name_Header_Field extends CACAP_Header_Field {
	public function __construct() {
		$args = array(
			'field_name' => 'Name',
			'field_type' => 'text',
		);
		parent::init( $args );
	}
}

class CACAP_ShortDescription_Header_Field extends CACAP_Header_Field {
	public function __construct() {
		$args = array(
			'field_name' => 'Short Description',
			'field_type' => 'textarea',
		);
		parent::init( $args );
	}
}
