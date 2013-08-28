<?php bp_get_template_part( 'cacap/header-top' ) ?>

<div class="cacap-row">
	<dl id="cacap-vitals">
		<?php
		buddypress()->social_media_profiles->setup_user_sm_fields();

		$user_sm_fields = buddypress()->social_media_profiles->user_sm_fields;
		$user_sm_fields_html = '';

		foreach ( (array) $user_sm_fields as $field ) {
			$user_sm_fields_html .= $field['html'];
		}
		?>

		<?php if ( $user_sm_fields_html ) : ?>
			<dt class="cacap-vitals-contact">Follow me Online</dt>
			<dd class="cacap-vitals-contact"><?php echo $user_sm_fields_html ?></dd>
                        <div clear="both"> </div>
		<?php endif ?>

		<?php
		$contact_info = array();

		if ( cacap_field_is_visible_for_user( 'Phone' ) ) {
			$phone = xprofile_get_field_data( 'Phone', bp_displayed_user_id() );
			if ( $phone ) {
				$contact_info[] = $phone;
			}
		}

		if ( cacap_field_is_visible_for_user( 'Email Address' ) ) {
			$email = xprofile_get_field_data( 'Email Address', bp_displayed_user_id() );
			if ( $email ) {
				$contact_info[] = $email;
			}
		}

		$contact_info = implode( ' &middot; ', $contact_info );
		?>

		<?php if ( $contact_info ) : ?>
			<dt><?php _e( 'Contact', 'cacap' ) ?></dt>
			<dd><?php echo $contact_info ?></dd>
		<?php endif ?>

		<?php if ( cacap_field_is_visible_for_user( 'Website' ) ) : ?>
			<?php $website = xprofile_get_field_data( 'Website', bp_displayed_user_id() ) ?>
			<?php if ( $website ) : ?>
				<dt><?php _e( 'Website', 'cacap' ) ?></dt>
				<dd><a href="<?php echo esc_url( $website ) ?>"><?php echo esc_html( $website ) ?></a></dd>
			<?php endif ?>
		<?php endif ?>

		<?php if ( cacap_field_is_visible_for_user( 'Blog' ) ) : ?>
			<?php $blog = xprofile_get_field_data( 'Blog', bp_displayed_user_id() ) ?>
			<?php if ( $blog ) : ?>
				<dt><?php _e( 'Blog', 'cacap' ) ?></dt>
				<dd><a href="<?php echo esc_url( $blog ) ?>"><?php echo esc_html( $blog ) ?></a></dd>
			<?php endif ?>
		<?php endif ?>

		<?php if ( function_exists( 'cac_yourls_get_user_shorturl' ) ) : ?>
			<?php $shorturl = cac_yourls_get_user_shorturl( bp_displayed_user_id() ) ?>
			<?php if ( $shorturl ) : ?>
				<dt><?php _e( 'Short URL', 'cacap' ) ?></dt>
				<dd><a href="http://cuny.is/<?php echo esc_attr( $shorturl ) ?>">http://cuny.is/<?php echo esc_html( $shorturl ) ?></a></dd>
			<?php endif ?>
		<?php endif ?>
	</dl>
</div>

