<?php
/**
 * The top-matter of the user header, displayed both in Edit and non-Edit mode
 */
?>

<div class="cacap-row cacap-hero-row">
	<div class="cacap-hero">
		<h1><a href="<?php echo bp_displayed_user_domain() ?>"><?php echo xprofile_get_field_data( 1, bp_displayed_user_id() ) ?></a></h1>
		<h4 class="cacap-short-aboutme"><?php echo xprofile_get_field_data( 'Brief Descriptor', bp_displayed_user_id() ) ?></h4>
		<div class="cacap-long-aboutme">
			<?php echo wpautop( xprofile_get_field_data( 'About You', bp_displayed_user_id() ) ) ?>
		</div>
	</div>

	<div class="cacap-avatar">
		<?php bp_displayed_user_avatar( array(
			'type' => 'full',
			'width' => '130px',
			'height' => '130px',
		) ) ?>
		<div class="activity"><?php bp_last_activity( bp_displayed_user_id() ) ?></div>
		<?php bp_add_friend_button( bp_displayed_user_id() ) ?>
		<?php bp_send_private_message_button( bp_displayed_user_id() ) ?>
		<?php bp_send_public_message_button( bp_displayed_user_id() ) ?>
	</div>

	<?php if ( bp_is_user_profile_edit() ) : ?>
		<input type="submit" value="<?php _e( 'Save Changes', 'cacap' ) ?>" class="cacap-edit-submit" />
	<?php endif ?>
</div>

