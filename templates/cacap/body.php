<?php if ( ! bp_is_user_profile() || bp_is_user_change_avatar() || ( ! empty( $_GET['commons-profile'] ) && 1 === (int) $_GET['commons-profile'] ) ) : ?>
	<?php bp_locate_template( 'cacap/commons-profile.php', true ) ?>
<?php else : ?>
	<div id="cacap-user-widgets">
		<?php foreach ( cacap_user_widget_instances() as $widget_instance ) : ?>
			<div class="cacap-row cacap-widget">
				<div class="cacap-widget-title"><?php echo $widget_instance->display_title() ?></div>
				<div class="cacap-widget-content"><?php echo $widget_instance->display_content() ?></div>
			</div>
		<?php endforeach; ?>
		</ul>
	</div>
<?php endif ?>
