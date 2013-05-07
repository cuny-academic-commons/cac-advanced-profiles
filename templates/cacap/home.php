<?php
/**
 * The top-level template for CAC Advanced Profiles
 *
 * We use a top-level template because CACAP does not use the standard WP
 * header/sidebar/footer. However, in the future, we might refactor the
 * plugin so that the header/sidebar/footer are dynamically removed from an
 * existing top-level template
 */
?>

<?php /* @todo Call a real header template? Which? */ ?>

<?php wp_head() ?>

<body <?php body_class() ?>>
	<div id="cacap-content">
		<?php if ( bp_is_user_profile_edit() ) : ?>
			<div id="cacap-header">
				<?php bp_get_template_part( 'cacap/header-edit' ) ?>
			</div>

			<div id="cacap-edit">
				<?php bp_get_template_part( 'cacap/body-edit' ) ?>
			</div>
		<?php else : ?>
			<div id="cacap-header">
				<?php bp_get_template_part( 'cacap/header' ) ?>
			</div>

			<div id="cacap-tabs">
				<?php bp_get_template_part( 'cacap/tabs' ) ?>
			</div>

			<div id="cacap-body">
				<?php bp_get_template_part( 'cacap/body' ) ?>
			</div>
		<?php endif ?>
	</div>

	<?php get_footer( 'cacap' ) ?>
</body>
