<?php

function cacap_includes_dir() {
	$includes_dir = '';

	if ( isset( buddypress()->cacap->includes_dir ) ) {
		$includes_dir = buddypress()->cacap->includes_dir;
	}

	return $includes_dir;
}

function cacap_user_widgets() {
	// @todo abstract
	$user_id = bp_displayed_user_id();

	$user = buddypress()->cacap->get_user( $user_id );
	return $user->get_widgets();
}


