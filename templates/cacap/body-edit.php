<div id="cacap-body">
	<?php foreach ( cacap_user_widget_instances() as $widget_instance ) : ?>
		<div class="cacap-row cacap-widget-edit">
			<div class="cacap-widget-title"><?php echo $widget_instance->display_title() ?></div>
			<div class="cacap-widget-content"><?php echo $widget_instance->display_content() ?></div>
		</div>
	<?php endforeach; ?>

	<div class="cacap-row" id="cacap-user-widget-new">
		<h2><?php _e( 'Create New Widget', 'cacap' ) ?></h2>
		<?php if ( empty( $_GET['cacap-new-widget-type'] ) ) : ?>
			<form action="" method="get">
				<?php include( 'widget-new-selector.php' ) ?>
				<?php cacap_html_gen()->input( 'submit', '', __( 'Create', 'cacap' ) ) ?>
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

				<?php cacap_html_gen()->input( 'submit', 'cacap-widget-create-submit', __( 'Create', 'cacap' ) ) ?>
			</form>
		<?php endif ?>
	</div>
</div>


