<div class="cacap-row">
	<ul>
		<?php $is_commons_profile = ! bp_is_user_profile() || ( ! empty( $_GET['commons-profile'] ) && '1' === $_GET['commons-profile'] ) ?>
		<?php $url = trailingslashit( bp_displayed_user_domain() . BP_XPROFILE_SLUG ) ?>

		<li<?php if ( ! $is_commons_profile ) : ?> class="current-tab"<?php endif ?>>
			<a href="<?php echo $url ?>"><?php _e( 'Public Portfolio', 'cacap' ) ?></a>
		</li>

		<li<?php if ( $is_commons_profile ) : ?> class="current-tab"<?php endif ?>>
			<a href="<?php echo add_query_arg( 'commons-profile', '1', $url ) ?>"><?php _e( 'Commons Profile', 'cacap' ) ?></a>
		</li>

		<div style="clear: both;"></div>
	</ul>
</div>

<?php if ( bp_is_user_profile() && ! bp_is_user_profile_edit() && ( bp_is_my_profile() || current_user_can( 'bp_moderate' ) ) ) : ?>
	<div class="cacap-row">
		<a id="cacap-edit-button" class="button secondary" href="<?php echo $url . 'edit/' ?>"><?php _e( 'Edit', 'cacap' ) ?></a>
	</div>
<?php endif ?>

<div style="clear: both;"></div>
