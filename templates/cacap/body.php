<div class="cacap-row" id="cacap-subnav">

</div>

<div id="cacap-user-widgets">
	<?php foreach ( cacap_user_widget_instances() as $widget_instance ) : ?>
		<div class="cacap-row cacap-widget">
			<div class="cacap-widget-title"><?php echo $widget_instance->display_title() ?></div>
			<div class="cacap-widget-content"><?php echo $widget_instance->display_content() ?></div>
		</div>
	<?php endforeach; ?>
	</ul>
</div>
