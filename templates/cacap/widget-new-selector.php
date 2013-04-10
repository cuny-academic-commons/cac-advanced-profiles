<label for="cacap-new-widget-type"><?php _e( 'Type', 'cacap' ) ?></label>
<select id="cacap-new-widget-type" name="cacap-new-widget-type">
	<?php foreach ( cacap_widget_types() as $widget_type ) : ?>
		<?php echo $widget_type->option_markup() ?>
	<?php endforeach ?>
</select>
