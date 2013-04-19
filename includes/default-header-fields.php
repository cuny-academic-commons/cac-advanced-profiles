<?php

if ( ! class_exists( 'CACAP_Header_Field' ) ) {
	require( cacap_includes_dir() . 'header_field.php' );
}

$hdir = __DIR__ . '/header-fields';
if ( $h = opendir( $hdir ) ) {
	while ( false !== ( $file = readdir( $h ) ) ) {
		if ( 0 === strpos( $file, '.' ) ) {
			continue;
		}

		include( $hdir . '/' . $file );
	}
	closedir( $h );
}

function cacap_header_fields() {
	return array(
		'name' => new CACAP_Header_Field_Name,
		'short_description' => new CACAP_Header_Field_Short_Description,
	);
}
