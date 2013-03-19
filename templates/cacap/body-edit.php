<?php

require_once trailingslashit( CACAP_PLUGIN_DIR ) . 'lib/wp-sdl/wp-sdl.php';
$wpsdl = WP_SDL::support( '1.0' );
$h = $wpsdl->html();

?>

<form action="" method="post">

<div id="cacap-header">
	<ul>
	<?php foreach ( cacap_header_fields() as $field ) : ?>
		<li>
			<?php /* @todo abstract this stuff */ ?>
			<?php $id = 'cacap-edit-' . $field->get_field_id(); ?>
			<?php $h->label( $id, $field->get_field_name() ) ?>

			<?php if ( 'textarea' == $field->get_field_type() ) : ?>
				<?php $h->textarea( $id, array( 'id' => $id ), $field->get_value() ) ?>
				<?php $h->textarea_close() ?>
			<?php else: ?>
				<?php $h->input( $field->get_field_type(), 'cacap-edit-' . $field->get_field_id(), $field->get_value(), array( 'id' => $id ) ) ?>
			<?php endif; ?>
		</li>
	<?php endforeach; ?>
	</ul>
</div>

<div id="cacap-body">
	<?php foreach ( cacap_user_widgets() as $widget ) : ?>
		<?php var_dump( $widget ) ?>
	<?php endforeach; ?>
</div>

<?php $h->input( 'submit', 'cacap-edit-submit', __( 'Submit', 'cacap' ) ) ?>

</form>
