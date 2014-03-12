<?php bp_get_template_part( 'cacap/header-top' ) ?>

<?php if ( ! cacap_is_commons_profile() ) : ?>
	<div class="cacap-row">
		<dl id="cacap-vitals">
		<?php foreach ( cacap_vitals() as $vital ) : ?>
			<dt class="<?php echo esc_attr( $vital->css_class ) ?>"><?php echo esc_html( $vital->title ) ?></dt>

			<?php /* Don't escape content, because it may contain HTML */ ?>
			<dd class="<?php echo esc_attr( $vital->css_class ) ?>"><?php echo $vital->content ?></dt>
		<?php endforeach ?>

			<div clear="both"> </div>

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
					<dt><?php _e( 'Quick Link', 'cacap' ) ?></dt>
					<dd><a href="http://cuny.is/<?php echo esc_attr( $shorturl ) ?>">http://cuny.is/<?php echo esc_html( $shorturl ) ?></a></dd>
				<?php endif ?>
			<?php endif ?>
		</dl>
	</div>
<?php endif ?>
