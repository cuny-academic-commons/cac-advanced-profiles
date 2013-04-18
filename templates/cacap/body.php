<div id="cacap-user-widgets">
	<ul>
	<?php foreach ( cacap_user_widget_instances() as $widget_instance ) : ?>
		<?php /* temp */ ?>
		<li>
			<div class="cacap-widget-title"><?php echo $widget_instance->display_title() ?></div>
			<div class="cacap-widget-content"><?php echo $widget_instance->display_content() ?></div>
		</li>
	<?php endforeach; ?>
	</ul>
</div>
