<?php

$cols = cacap_get_header_fields( 'edit' );
$cols = apply_filters( 'cacap_header_edit_columns', $cols );
$flat_fields = array_merge( $cols['right'], $cols['left'] );

global $wpdb, $bp;
$all_fields = $wpdb->get_col( "SELECT id FROM {$bp->profile->table_name_fields}" );

$profile_args = array(
	'exclude_fields' => array_diff( $all_fields, $flat_fields ),
	'hide_empty_fields' => false,
);

?>

<?php if ( bp_has_profile( $profile_args ) ) : ?>
	<div class="cacap-row cacap-bp-profile-fields">

	<?php
	/**
	 * We do a hack to sort into columns. Cols are defined in an array.
	 * We'll chop them up into two groups, sort them as defined here, then
	 * we'll run through the profile loop twice
	 */
	/*
	$cols = array(
		'left' => array(),
		'right' => array(),
	);*/


	global $profile_template;

	$profile_template->group_count = 2;

	$fields = array();
	foreach ( $profile_template->groups as $ptg ) {
		$fields = array_merge( $fields, $ptg->fields );
	}

	$new_groups = array(
		'left' => new stdClass,
		'right' => new stdClass,
	);

	$new_groups['left']->id = 1;
	$new_groups['left']->name = 'Base';
	$new_groups['right']->id = 2;
	$new_groups['right']->name = 'Base2';
	foreach ( $new_groups as &$new_group ) {
		$new_group->description = '';
		$new_group->group_order = '0';
		$new_group->can_delete = '0';
		$new_group->fields = array();
	}

	$field_ids = array();

	foreach ( $cols as $col_no => $col_fields ) {
		foreach ( $fields as $field ) {
			$which_col = null;
			if ( false !== $col_order = array_search( $field->id, $cols['left'] ) ) {
				$which_col = 'left';
			} elseif ( false !== $col_order = array_search( $field->id, $cols['right'] ) ) {
				$which_col = 'right';
			}

			if ( $which_col ) {
				$new_groups[ $which_col ]->fields[ $col_order ] = $field;
				$field_ids[] = $field->id;
			}
		}
	}

	// Fix indexes
	foreach ( $new_groups as &$new_group ) {
		ksort( $new_group->fields );
	}

	// replace the value in the global
	$profile_template->groups = array_values( $new_groups );

	?>

	<input type="hidden" name="field_ids" id="field_ids" value="<?php echo implode( ',', array_unique( $field_ids ) ) ?>" />

	<?php while ( bp_profile_groups() ) : bp_the_profile_group(); ?>

	<div class="cacap-half-col" id="cacap-half-col-<?php bp_the_profile_group_id() ?>">

	<?php while ( bp_profile_fields() ) : bp_the_profile_field(); ?>

		<div<?php bp_field_css_class( 'editfield' ); ?>>

			<?php if ( 'textbox' == bp_get_the_profile_field_type() ) : ?>

				<label for="<?php bp_the_profile_field_input_name(); ?>"><?php bp_the_profile_field_name(); ?> <?php if ( bp_get_the_profile_field_is_required() ) : ?><?php _e( '(required)', 'buddypress' ); ?><?php endif; ?></label>
				<input type="text" name="<?php bp_the_profile_field_input_name(); ?>" id="<?php bp_the_profile_field_input_name(); ?>" value="<?php bp_the_profile_field_edit_value(); ?>" <?php if ( bp_get_the_profile_field_is_required() ) : ?>aria-required="true"<?php endif; ?>/>

			<?php endif; ?>

			<?php if ( 'textarea' == bp_get_the_profile_field_type() ) : ?>

				<label for="<?php bp_the_profile_field_input_name(); ?>"><?php bp_the_profile_field_name(); ?> <?php if ( bp_get_the_profile_field_is_required() ) : ?><?php _e( '(required)', 'buddypress' ); ?><?php endif; ?></label>
				<textarea rows="5" cols="40" name="<?php bp_the_profile_field_input_name(); ?>" id="<?php bp_the_profile_field_input_name(); ?>" <?php if ( bp_get_the_profile_field_is_required() ) : ?>aria-required="true"<?php endif; ?>><?php bp_the_profile_field_edit_value(); ?></textarea>

			<?php endif; ?>

			<?php if ( 'selectbox' == bp_get_the_profile_field_type() ) : ?>

				<label for="<?php bp_the_profile_field_input_name(); ?>"><?php bp_the_profile_field_name(); ?> <?php if ( bp_get_the_profile_field_is_required() ) : ?><?php _e( '(required)', 'buddypress' ); ?><?php endif; ?></label>
				<select name="<?php bp_the_profile_field_input_name(); ?>" id="<?php bp_the_profile_field_input_name(); ?>" <?php if ( bp_get_the_profile_field_is_required() ) : ?>aria-required="true"<?php endif; ?>>
					<?php bp_the_profile_field_options(); ?>
				</select>

			<?php endif; ?>

			<?php if ( 'multiselectbox' == bp_get_the_profile_field_type() ) : ?>

				<label for="<?php bp_the_profile_field_input_name(); ?>"><?php bp_the_profile_field_name(); ?> <?php if ( bp_get_the_profile_field_is_required() ) : ?><?php _e( '(required)', 'buddypress' ); ?><?php endif; ?></label>
				<select name="<?php bp_the_profile_field_input_name(); ?>" id="<?php bp_the_profile_field_input_name(); ?>" multiple="multiple" <?php if ( bp_get_the_profile_field_is_required() ) : ?>aria-required="true"<?php endif; ?>>

					<?php bp_the_profile_field_options(); ?>

				</select>

				<?php if ( !bp_get_the_profile_field_is_required() ) : ?>

					<a class="clear-value" href="javascript:clear( '<?php bp_the_profile_field_input_name(); ?>' );"><?php _e( 'Clear', 'buddypress' ); ?></a>

				<?php endif; ?>

			<?php endif; ?>

			<?php if ( 'radio' == bp_get_the_profile_field_type() ) : ?>

				<div class="radio">
					<span class="label"><?php bp_the_profile_field_name(); ?> <?php if ( bp_get_the_profile_field_is_required() ) : ?><?php _e( '(required)', 'buddypress' ); ?><?php endif; ?></span>

					<?php bp_the_profile_field_options(); ?>

					<?php if ( !bp_get_the_profile_field_is_required() ) : ?>

						<a class="clear-value" href="javascript:clear( '<?php bp_the_profile_field_input_name(); ?>' );"><?php _e( 'Clear', 'buddypress' ); ?></a>

					<?php endif; ?>
				</div>

			<?php endif; ?>

			<?php if ( 'checkbox' == bp_get_the_profile_field_type() ) : ?>

				<div class="checkbox">
					<span class="label"><?php bp_the_profile_field_name(); ?> <?php if ( bp_get_the_profile_field_is_required() ) : ?><?php _e( '(required)', 'buddypress' ); ?><?php endif; ?></span>

					<?php bp_the_profile_field_options(); ?>
				</div>

			<?php endif; ?>

			<?php if ( 'datebox' == bp_get_the_profile_field_type() ) : ?>

				<div class="datebox">
					<label for="<?php bp_the_profile_field_input_name(); ?>_day"><?php bp_the_profile_field_name(); ?> <?php if ( bp_get_the_profile_field_is_required() ) : ?><?php _e( '(required)', 'buddypress' ); ?><?php endif; ?></label>

					<select name="<?php bp_the_profile_field_input_name(); ?>_day" id="<?php bp_the_profile_field_input_name(); ?>_day" <?php if ( bp_get_the_profile_field_is_required() ) : ?>aria-required="true"<?php endif; ?>>

						<?php bp_the_profile_field_options( 'type=day' ); ?>

					</select>

					<select name="<?php bp_the_profile_field_input_name(); ?>_month" id="<?php bp_the_profile_field_input_name(); ?>_month" <?php if ( bp_get_the_profile_field_is_required() ) : ?>aria-required="true"<?php endif; ?>>

						<?php bp_the_profile_field_options( 'type=month' ); ?>

					</select>

					<select name="<?php bp_the_profile_field_input_name(); ?>_year" id="<?php bp_the_profile_field_input_name(); ?>_year" <?php if ( bp_get_the_profile_field_is_required() ) : ?>aria-required="true"<?php endif; ?>>

						<?php bp_the_profile_field_options( 'type=year' ); ?>

					</select>
				</div>

			<?php endif; ?>

			<p class="description"><?php bp_the_profile_field_description(); ?></p>

			<?php do_action( 'bp_custom_profile_edit_fields_pre_visibility' ); ?>

			<?php if ( bp_current_user_can( 'bp_xprofile_change_field_visibility' ) ) : ?>
				<p class="field-visibility-settings-toggle" id="field-visibility-settings-toggle-<?php bp_the_profile_field_id() ?>">
					<?php printf( __( 'This field can be seen by: <span class="current-visibility-level">%s</span>', 'buddypress' ), bp_get_the_profile_field_visibility_level_label() ) ?> <a href="#" class="visibility-toggle-link"><?php _e( 'Change', 'buddypress' ); ?></a>
				</p>

				<div class="field-visibility-settings" id="field-visibility-settings-<?php bp_the_profile_field_id() ?>">
					<fieldset>
						<legend><?php _e( 'Who can see this field?', 'buddypress' ) ?></legend>

						<?php bp_profile_visibility_radio_buttons() ?>

					</fieldset>
					<a class="field-visibility-settings-close" href="#"><?php _e( 'Close', 'buddypress' ) ?></a>
				</div>
			<?php else : ?>
				<div class="field-visibility-settings-notoggle" id="field-visibility-settings-toggle-<?php bp_the_profile_field_id() ?>">
					<?php printf( __( 'This field can be seen by: <span class="current-visibility-level">%s</span>', 'buddypress' ), bp_get_the_profile_field_visibility_level_label() ) ?>
				</div>
			<?php endif ?>

			<?php do_action( 'bp_custom_profile_edit_fields' ); ?>
		</div>

	<?php endwhile; ?>
	</div>
	<?php endwhile; ?>

	<?php wp_nonce_field( 'bp_xprofile_edit' ); ?>

	</div><!-- /.cacap-bp-profile-fields -->
<?php endif ?>

<?php do_action( 'cacap_after_bp_profile_fields_edit' ) ?>
