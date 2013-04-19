<?php $h = cacap_html_gen() ?>

<div class="cacap-row">
	<form action="" method="post">
		<ul>
		<?php foreach ( cacap_header_fields() as $field ) : ?>
			<li>
				<?php /* @todo abstract this stuff */ ?>
				<?php $id = 'cacap-edit-' . $field->get_field_id(); ?>
				<?php $h->label( $id, $field->get_field_name() ) ?>

				<?php if ( 'textarea' == $field->get_field_type() ) : ?>
					<?php $h->textarea( $id, array( 'id' => $id ), $field->get_value() ) ?>
					<?php $h->textarea_close() ?>
				<?php else : ?>
					<?php $h->input( $field->get_field_type(), 'cacap-edit-' . $field->get_field_id(), $field->get_value(), array( 'id' => $id ) ) ?>
				<?php endif; ?>
			</li>
		<?php endforeach; ?>
		</ul>
	</form>
</div>
