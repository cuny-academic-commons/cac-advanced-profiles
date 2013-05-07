<?php
/**
 * The top-matter of the user header, displayed both in Edit and non-Edit mode
 */
?>

<div class="cacap-row cacap-hero-row">
	<div class="cacap-hero">
		<h1><?php echo xprofile_get_field_data( 'Full Name', bp_displayed_user_id() ) ?></h1>
		<h4 class="cacap-short-aboutme"><?php echo xprofile_get_field_data( 'Brief Descriptor', bp_displayed_user_id() ) ?></h4>
		<div class="cacap-long-aboutme">
			<?php echo wpautop( xprofile_get_field_data( 'About You', bp_displayed_user_id() ) ) ?>
		</div>
	</div>

	<div class="cacap-avatar">
		<?php bp_displayed_user_avatar( array(
			'type' => 'full',
			'width' => '180px',
			'height' => '180px',
		) ) ?>
	</div>

	<?php if ( bp_is_user_profile_edit() ) : ?>
		<input type="submit" value="<?php _e( 'Save Changes', 'cacap' ) ?>" id="cacap-edit-submit" />
	<?php endif ?>
</div>

