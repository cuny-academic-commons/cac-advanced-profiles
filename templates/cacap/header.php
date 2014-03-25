<?php bp_get_template_part( 'cacap/header-top' ) ?>

<?php if ( ! cacap_is_commons_profile() ) : ?>
	<div class="cacap-row">
		<dl id="cacap-vitals">
		<?php foreach ( cacap_vitals() as $vital ) : ?>
			<dt class="<?php echo esc_attr( $vital->css_class ) ?>"><?php echo esc_html( $vital->title ) ?></dt>

			<?php /* Don't escape content, because it may contain HTML */ ?>
			<dd class="<?php echo esc_attr( $vital->css_class ) ?>"><?php echo $vital->content ?></dt>
		<?php endforeach ?>
		</dl>
	</div>
<?php endif ?>
