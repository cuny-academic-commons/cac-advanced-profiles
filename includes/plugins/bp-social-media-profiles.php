<?php

/**
 * CACAP functionality related to BP Social Media Profiles
 *
 * @since 0.2
 */
function cacap_bpsmp_vitals( $items ) {
	// Init the social media fields
	buddypress()->social_media_profiles->setup_user_sm_fields();

	$user_sm_fields = buddypress()->social_media_profiles->user_sm_fields;

	if ( ! empty( $user_sm_fields ) ) {
		$user_sm_fields_html = '';

		foreach ( (array) $user_sm_fields as $field ) {
			$user_sm_fields_html .= $field['html'];
		}

		$items[] = new CACAP_Vital( array(
			'id'       => 'social-media',
			'title'    => __( 'Follow me Online', 'cac-advanced-profiles' ),
			'content'  => $user_sm_fields_html,
			'position' => 10,
		) );
	}

	return $items;
}
add_filter( 'cacap_vitals', 'cacap_bpsmp_vitals' );
