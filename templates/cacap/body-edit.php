<div id="cacap-body">

	<div class="cacap-row" id="cacap-user-widget-new">
		<div id="cacap-user-widget-new-content">
			<h2><?php _e( 'Add New Section', 'cacap' ) ?></h2>

			<ul id="cacap-new-widget-types">
			<?php foreach ( cacap_widget_types() as $widget_type ) : ?>
				<li>
					<a href="">
						<img src="<?php echo cacap_assets_url() ?>/images/plus.png" />
						<span class="cacap-widget-type-name"><?php echo esc_html( $widget_type->name ) ?></span>
					</a>
				</li>
			<?php endforeach ?>
			</ul>

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

	<div class="cacap-row cacap-widgets cacap-widgets-edit">
		<ul id="cacap-widget-list">
		<?php foreach ( cacap_user_widget_instances() as $widget_instance ) : ?>
			<li id="cacap-widget-<?php echo esc_attr( $widget_instance->css_id ) ?>">
				<div class="cacap-drag-handle"></div>
				<div class="cacap-widget-title cacap-click-to-edit"><?php echo $widget_instance->edit_title() ?></div>
				<div class="cacap-widget-content cacap-click-to-edit"><?php echo $widget_instance->edit_content() ?></div>
			</li>
		<?php endforeach; ?>
		</ul>
	</div>

	<input type="hidden" name="cacap-widget-order" id="cacap-widget-order" value="<?php echo cacap_widget_order() ?>" />
	<input type="submit" value="<?php _e( 'Save Changes', 'cacap' ) ?>" class="cacap-edit-submit" />
</div>


