Body Edit
<div id="cacap-header">
	<?php foreach ( cacap_header_fields() as $field ) : ?>

		<?php var_dump( $field ) ?>
	<?php endforeach; ?>
</div>

<div id="cacap-body">
	<?php foreach ( cacap_user_widgets() as $widget ) : ?>
		<?php var_dump( $widget ) ?>
	<?php endforeach; ?>
</div>
