<div id="cacap-body">

	<div class="cacap-row" id="cacap-user-widget-new">
		<div id="cacap-user-widget-new-content">
			<h2><?php _e( 'Add New Section', 'cacap' ) ?></h2>

			<ul id="cacap-new-widget-types">
			<?php foreach ( cacap_widget_types() as $widget_type ) : ?>
				<?php

				if ( ! $widget_type->allow_new ) {
					continue;
				}

				$css_classes = array();
				if ( cacap_widget_type_is_disabled_for_user( $widget_type ) ) {
					$css_classes[] = 'cacap-has-max';
				}

				if ( ! $widget_type->allow_multiple ) {
					$css_classes[] = 'disable-multiple';
				}

				?>

				<li class="<?php echo implode( ' ', $css_classes ) ?>" id="cacap-new-widget-<?php echo esc_attr( $widget_type->slug ) ?>">
					<a href="#cacap-user-widget-new-content?type=<?php echo esc_attr( $widget_type->slug ) ?>">
						<img src="<?php echo cacap_assets_url() ?>/images/plus.png" alt="" />
						<span class="cacap-widget-type-name"><?php echo esc_html( $widget_type->name ) ?></span>
						<?php if ( ! empty( $disabled ) ) : ?>
							<span class="cacap-has-max-tooltip"><?php _e( 'You already have a widget of this type.', 'cacap' ) ?></span>
						<?php endif ?>
					</a>
				</li>
			<?php endforeach ?>
			</ul>

			<div id="cacap-widget-prototypes">
			<?php foreach ( cacap_widget_types() as $widget_type ) : ?>
				<?php $wi_prototype = new CACAP_Widget_Instance( array( 'widget_type' => $widget_type, 'key' => 'newwidgetkey' ) ) ?>
				<div id="cacap-widget-prototype-<?php echo esc_attr( $widget_type->slug ) ?>">
					<div class="cacap-drag-handle"></div>
					<div class="cacap-widget-title <?php if ( $widget_type->allow_custom_title ) : ?>cacap-widget-section-editable <?php endif ?>cacap-click-to-edit"><?php echo $wi_prototype->edit_title() ?></div>
					<div class="cacap-widget-content cacap-widget-section-editable cacap-click-to-edit"><?php echo $wi_prototype->edit_content() ?></div>
					<input type="hidden" value="<?php echo esc_attr( $wi_prototype->widget_type->slug ) ?>" name="<?php echo esc_attr( $wi_prototype->css_id ) ?>[widget_type]" />
					<a href="#" class="cacap-widget-remove button confirm"><?php _e( 'Remove', 'cacap' ) ?></a>
				</div>

			<?php endforeach ?>
			</div>
		</div>
	</div>

	<div class="cacap-row cacap-widgets cacap-widgets-edit">
		<ul id="cacap-widget-list">
		<?php foreach ( cacap_user_widget_instances() as $widget_instance ) : ?>
                        <?php if ( ! $widget_instance->widget_type->allow_edit ) continue ?>

			<li id="cacap-widget-<?php echo esc_attr( $widget_instance->css_id ) ?>" class="cacap-widget-<?php echo esc_attr( $widget_instance->widget_type->slug ) ?>">
				<div class="cacap-drag-handle"></div>
				<div class="cacap-widget-title <?php if ( $widget_instance->widget_type->allow_custom_title ) : ?>cacap-widget-section-editable <?php endif ?>cacap-click-to-edit" id="<?php echo esc_attr( $widget_instance->css_id ) ?>-title"><?php echo $widget_instance->edit_title() ?></div>
				<div class="cacap-widget-content cacap-widget-section-editable cacap-click-to-edit" id="<?php echo esc_attr( $widget_instance->css_id ) ?>-content"><?php echo $widget_instance->edit_content() ?></div>
				<input type="hidden" value="<?php echo esc_attr( $widget_instance->widget_type->slug ) ?>" name="<?php echo esc_attr( $widget_instance->css_id ) ?>[widget_type]" />

				<a href="#" class="cacap-widget-remove button confirm"><?php _e( 'Remove', 'cacap' ) ?></a>
			</li>
		<?php endforeach; ?>
		</ul>
	</div>

	<input type="hidden" name="cacap-widget-order" id="cacap-widget-order" value="<?php echo cacap_widget_order() ?>" />

	<?php if ( bp_is_my_profile() || current_user_can( 'bp_moderate' ) ) : ?>
		<div class="cacap-edit-buttons cacap-edit-buttons-bottom">
		<?php if ( bp_is_user_profile_edit() ) : ?>
			<input type="submit" value="<?php _e( 'Save Changes', 'cacap' ) ?>" class="cacap-edit-submit" />
			<a href="<?php echo bp_displayed_user_domain() . buddypress()->profile->slug ?>/" class="cacap-edit-cancel button"><?php _e( 'Cancel', 'cacap' ) ?></a>
		<?php else : ?>
			<a href="<?php echo bp_displayed_user_domain() ?>/<?php echo buddypress()->profile->slug ?>/edit/" class="cacap-edit-button button"><?php _e( 'Edit', 'cacap' ) ?></a>
		<?php endif ?>
		</div>
	<?php endif ?>
</div>


