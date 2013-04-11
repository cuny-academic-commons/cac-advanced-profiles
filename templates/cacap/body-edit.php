<?php

$h = cacap_html_gen();

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
<?php $h->input( 'submit', 'cacap-edit-submit', __( 'Submit', 'cacap' ) ) ?>
</div>

</form>

<div id="cacap-body">
	<div id="cacap-user-widgets">
		<ul>
		<?php foreach ( cacap_user_widget_instances() as $widget_instance ) : ?>
			<?php /* temp */ ?>
			<li>
				<div id="cacap-widget-title"><?php echo $widget_instance->display_title() ?></div>
				<div id="cacap-widget-content"><?php echo $widget_instance->display_content() ?></div>
			</li>
		<?php endforeach; ?>
		</ul>
	</div>

	<div id="cacap-user-widget-new">
		<h2><?php _e( 'Create New Widget', 'cacap' ) ?></h2>
		<?php if ( empty( $_GET['cacap-new-widget-type'] ) ) : ?>
			<form action="" method="get">
				<?php include( 'widget-new-selector.php' ) ?>
				<?php $h->input( 'submit', '', __( 'Create', 'cacap' ) ) ?>
			</form>
		<?php else : ?>
			<form action="" method="post">
				<?php
				$widget_types = cacap_widget_types();
				// @todo better checks
				if ( isset( $widget_types[ $_GET['cacap-new-widget-type'] ] ) ) {
					echo $widget_types[ $_GET['cacap-new-widget-type'] ]->create_widget_markup();
				}
				wp_nonce_field( 'cacap_new_widget' );
				?>

				<input type="hidden" name="cacap-widget-type" value="<?php echo esc_attr( $_GET['cacap-new-widget-type'] ) ?>" />

				<?php $h->input( 'submit', 'cacap-widget-create-submit', __( 'Create', 'cacap' ) ) ?>
			</form>
		<?php endif ?>
	</div>
</div>


