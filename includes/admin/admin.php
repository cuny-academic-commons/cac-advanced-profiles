<?php

class CACAP_Admin {
	public static function init() {
		static $instance;
		if ( empty( $instance ) ) {
			$instance = new self();
		}
		return $instance;
	}

	private function __construct() {
		$this->add_menus();
		$this->configure_settings_sections();
		add_action( 'admin_menu', array( $this, 'add_menus' ) );
	}

	public function add_menus() {
		add_users_page(
			__( 'CAC Advanced Profiles', 'cacap' ),
			__( 'CAC Advanced Profiles', 'cacap' ),
			'bp_moderate',
			'cacap-admin',
			array( $this, 'admin_menu' )
		);
	}

	public function configure_settings_sections() {
		add_settings_section(
			'cacap-profile-header-public',
			__( 'Profile Header - Public', 'cacap' ),
			array( $this, 'settings_section_profile_header_public' ),
			'cacap-admin'
		);

		add_settings_section(
			'cacap-profile-header-edit',
			__( 'Profile Header - Edit', 'cacap' ),
			array( $this, 'settings_section_profile_header_edit' ),
			'cacap-admin'
		);

		add_settings_section(
			'cacap-widgets',
			__( 'Widgets', 'cacap' ),
			array( $this, 'settings_section_widgets' ),
			'cacap-admin'
		);
	}

	public function admin_menu() {
		$current_section = 'cacap-profile-header-public';
		if ( isset( $_GET['section'] ) ) {
			// @todo whitelist
			$current_section = stripslashes( $_GET['section'] );
		}

		?>
		<div class="wrap">
			<h2><?php esc_html_e( 'CAC Advanced Profiles', 'cacap' ) ?></h2>

			<?php $this->admin_tabs() ?>

			<?php settings_fields( 'cacap-admin' ) ?>
			<?php $this->do_settings_section( 'cacap-admin', $current_section ) ?>
			<?php submit_button() ?>
		</div>
		<?php
	}

	public function settings_section_profile_header_public() {
		add_filter( 'cacap_is_commons_profile', '__return_false' );
		$this->set_up_displayed_user( get_current_user_id() );
		?>
		<?php include_once( buddypress()->cacap->path . 'templates/cacap/header.php' ) ?>
		<?php
		$this->tear_down_displayed_user();
		remove_filter( 'cacap_is_commons_profile', '__return_false' );
	}

	public function settings_section_profile_header_edit() {
		echo 'foo edit';
	}

	public function settings_section_widgets() {
		echo 'foo widgets';
	}

	protected function set_up_displayed_user( $user_id ) {
		buddypress()->displayed_user->id = $user_id;
		buddypress()->displayed_user->domain = bp_core_get_user_domain( bp_displayed_user_id() );
		buddypress()->displayed_user->userdata = bp_core_get_core_userdata( bp_displayed_user_id() );
		buddypress()->displayed_user->fullname = bp_core_get_user_displayname( bp_displayed_user_id() );
	}

	protected function tear_down_displayed_user() {
		buddypress()->displayed_user->id = 0;
		unset( buddypress()->displayed_user->domain );
		unset( buddypress()->displayed_user->userdata );
		unset( buddypress()->displayed_user->fullname );
	}

	/**
	 * Output the tabs in the admin area
	 *
	 * @param string $active_tab Name of the tab that is active
	 */
	function admin_tabs( $active_tab = '' ) {
		global $wp_settings_sections;

		// Declare local variables
		$tabs_html    = '';
		$idle_class   = 'nav-tab';
		$active_class = 'nav-tab nav-tab-active';

		foreach ( $wp_settings_sections['cacap-admin'] as $s ) {
			$tabs[] = array(
				'href' => bp_get_admin_url( add_query_arg( array(
					'page' => 'cacap-admin',
					'section' => $s['id'],
				), 'users.php' ) ),
				'name' => $s['title'],
			);
		}

		// Loop through tabs and build navigation
		foreach ( array_values( $tabs ) as $tab_data ) {
			$is_current = (bool) ( $tab_data['name'] == $active_tab );
			$tab_class  = $is_current ? $active_class : $idle_class;
			$tabs_html .= '<a href="' . esc_url( $tab_data['href'] ) . '" class="' . esc_attr( $tab_class ) . '">' . esc_html( $tab_data['name'] ) . '</a>';
		}

		// Output the tabs
		echo $tabs_html;

		// Do other fun things
		do_action( 'bp_admin_tabs' );
	}

	function do_settings_section( $page, $section ) {
		global $wp_settings_sections, $wp_settings_fields;

		if ( ! isset( $wp_settings_sections[ $page ][ $section ] ) )
			return;

		$s = $wp_settings_sections[ $page ][ $section ];
		if ( $s['title'] ) {
			echo "<h3>{$s['title']}</h3>\n";
		}

		if ( $s['callback'] ) {
			call_user_func( $s['callback'], $s );
		}

		if ( ! isset( $wp_settings_fields ) || !isset( $wp_settings_fields[$page] ) || !isset( $wp_settings_fields[$page][$s['id']] ) ) {
			return;
		}

		echo '<table class="form-table">';
		do_settings_fields( $page, $s['id'] );
		echo '</table>';
	}
}
add_action( bp_core_admin_hook(), array( 'CACAP_Admin', 'init' ) );
