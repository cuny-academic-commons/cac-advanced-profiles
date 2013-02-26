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

<?php if ( bp_is_user_profile_edit() ) : ?>
	<?php bp_get_template_part( 'cacap/header-edit' ) ?>
	<?php bp_get_template_part( 'cacap/body-edit' ) ?>
<?php else : ?>
	<?php bp_get_template_part( 'cacap/header' ) ?>
	<?php bp_get_template_part( 'cacap/body' ) ?>
<?php endif; ?>


<?php get_footer( 'cacap' ) ?>
