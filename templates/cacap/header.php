<?php bp_get_template_part( 'cacap/header-top' ) ?>

<?php if ( ! cacap_is_commons_profile() ) : ?>
	<div class="cacap-row cacap-avatar-buttons-row">
		<div class="cacap-avatar-buttons">
			<?php if ( bp_is_active( 'friends' ) ) : ?>
				<?php bp_add_friend_button( bp_displayed_user_id() ) ?>
			<?php endif ?>

			<?php if ( bp_is_active( 'messages' ) ) : ?>
				<?php bp_send_private_message_button( bp_displayed_user_id() ) ?>
			<?php endif ?>

			<?php if ( bp_is_active( 'activity' ) ) : ?>
				<?php bp_send_public_message_button( bp_displayed_user_id() ) ?>
			<?php endif ?>

			<?php do_action( 'cacap_avatar_actions' ) ?>
		</div>
	</div>

	<div class="cacap-row cacap-vitals-row">
		<dl id="cacap-vitals">
		<?php foreach ( cacap_vitals() as $vital ) : ?>
			<dt class="<?php echo esc_attr( $vital->css_class ) ?>"><?php echo esc_html( $vital->title ) ?></dt>

			<?php /* Don't escape content, because it may contain HTML */ ?>
			<dd class="<?php echo esc_attr( $vital->css_class ) ?>"><?php echo $vital->content ?></dd>

			<div class="cleardiv"></div>

		<?php endforeach ?>
		</dl>
	</div>
<?php endif ?>

<?php do_action( 'cacap_after_header' ) ?>
