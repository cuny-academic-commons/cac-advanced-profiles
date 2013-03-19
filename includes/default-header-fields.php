<?php

if ( ! class_exists( 'CACAP_Header_Field' ) ) {
	require( cacap_includes_dir() . 'header_field.php' );
}

function cacap_header_fields() {
	return array(
		'name' => new CACAP_Name_Header_Field,
	);
}

/**
 * Defines the header field classes
 */
class CACAP_Name_Header_Field extends CACAP_Header_Field {
	public function __construct( $user_id = 0 ) {
		$args = array( 'field_id' => xprofile_get_field_id_from_name( 'Name' ) );

		if ( $user_id ) {
			$args['user_id'] = intval( $user_id );
		}

		parent::init( $args );
	}
}
