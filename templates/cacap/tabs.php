<div class="cacap-row">
	<ul>
		<?php $url = trailingslashit( bp_displayed_user_domain() . BP_XPROFILE_SLUG ) ?>

		<li<?php if ( empty( $_GET['commons-profile'] ) || '1' !== $_GET['commons-profile'] ) : ?> class="current"<?php endif ?>>
			<a href="<?php echo $url ?>"><?php _e( 'Portfolio (CV)', 'cacap' ) ?></a>
		</li>

		<li<?php if ( ! empty( $_GET['commons-profile'] ) && '1' === $_GET['commons-profile'] ) : ?> class="current"<?php endif ?>>
			<a href="<?php echo add_query_arg( 'commons-profile', '1', $url ) ?>"><?php _e( 'Commons Profile', 'cacap' ) ?></a>
		</li>
	</ul>
</div>
