<!DOCTYPE html PUBLIC "-//W3C//DD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
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

<head>
	<title><?php wp_title( '|', true, 'right' ); ?> CUNY Academic Commons</title>
	<?php wp_head() ?>
</head>

<body <?php body_class() ?>>

	<?php do_action( 'cacap_before_content' ) ?>

	<div id="cacap-content">
		<?php if ( bp_is_user_profile_edit() ) : ?>
			<form action="" method="post" id="cacap-edit-form">
				<div id="cacap-header">
					<?php bp_get_template_part( 'cacap/header-edit' ) ?>
				</div>

				<div id="cacap-edit">
					<?php bp_get_template_part( 'cacap/body-edit' ) ?>
				</div>
			</form>
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
</html>
