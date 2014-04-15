<div class="cacap-row">
	<ul>
		<?php $is_commons_profile = ! bp_is_user_profile() || ( ! empty( $_GET['commons-profile'] ) && '1' === $_GET['commons-profile'] ) ?>

		<li<?php if ( ! $is_commons_profile ) : ?> class="current-tab"<?php endif ?>>
			<a href="<?php echo cacap_get_public_portfolio_url( bp_displayed_user_id() ) ?>"><?php _e( 'Public Portfolio', 'cacap' ) ?></a>
		</li>

		<li<?php if ( $is_commons_profile ) : ?> class="current-tab"<?php endif ?>>
			<a href="<?php echo cacap_get_commons_profile_url( bp_displayed_user_id() ) ?>"><?php _e( 'Commons Profile', 'cacap' ) ?></a>
		</li>

		<div style="clear: both;"></div>
	</ul>
</div>

<?php if ( bp_is_user_profile() && ! bp_is_user_profile_edit() && ( bp_is_my_profile() || current_user_can( 'bp_moderate' ) ) ) : ?>
	<div class="cacap-row cacap-row-edit-button">
		<a id="cacap-edit-button" class="button secondary" href="<?php echo cacap_get_commons_profile_url( bp_displayed_user_id() ) . 'edit/' ?>"><?php _e( 'Edit', 'cacap' ) ?></a>
	</div>
<?php endif ?>

<div style="clear: both;"></div>
