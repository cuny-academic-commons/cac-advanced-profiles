<?php

/**
 * Load the files out of default-widgets
 *
 * I'll probably load widgets from plugins here too at some point
 */

$wdir = __DIR__ . '/widgets';
if ( $h = opendir( $wdir ) ) {
	while ( false !== ( $file = readdir( $h ) ) ) {
		if ( 0 === strpos( $file, '.' ) ) {
			continue;
		}

		include( $wdir . '/' . $file );
	}
	closedir( $h );
}
