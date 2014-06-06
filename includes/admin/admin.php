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
		if ( ! empty( $_POST['cacap-saved-values-header-public'] ) ) {
			$this->process_save_header_public();
		}

		$page = add_users_page(
			__( 'CAC Advanced Profiles', 'cacap' ),
			__( 'CAC Advanced Profiles', 'cacap' ),
			'bp_moderate',
			'cacap-admin',
			array( $this, 'admin_menu' )
		);

		add_action( 'admin_print_scripts-' . $page, array( $this, 'enqueue_assets' ) );
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

			<form id="cacap-form-<?php echo esc_attr( $current_section ) ?>" method="post" action="">
				<?php settings_fields( 'cacap-admin' ) ?>
				<?php $this->do_settings_section( 'cacap-admin', $current_section ) ?>
				<?php submit_button( null, 'primary', 'submit', true, array(
					'id' => 'cacap-header-submit',
				) ) ?>
			</form>
		</div>
		<?php
	}

	public function settings_section_profile_header_public() {
		?>

		<p><?php esc_html_e( 'Drag items from Available Fields to the Header section below to arrange the profile header.', 'cacap' ) ?></p>

		<h4 class="cacap-section-header"><?php esc_html_e( 'Header', 'cacap' ) ?></h4>
		<div class="cacap-header">
			<div class="cacap-row">
				<div class="cacap-hero">
					<h1><?php esc_html_e( 'User Name', 'cacap' ) ?></h1>

					<p class="cacap-instructions"><?php esc_html_e( 'The "Brief Descriptor" field is a one-sentence heading that appears directly below the user&#8217;s name.', 'cacap' ) ?></p>
					<h4 id="cacap-brief-descriptor" class="cacap-droppable">
						<p class="cacap-inner-label"><?php esc_html_e( 'Brief Descriptor', 'cacap' ) ?></p>
					</h4>

					<p class="cacap-instructions"><?php esc_html_e( 'The "About You" field is a summary (300 characters or less) of a user&#8217;s work and interests.', 'cacap' ) ?></p>
					<div id="cacap-about-you" class="cacap-droppable">
						<p class="cacap-inner-label"><?php esc_html_e( 'About You', 'cacap' ) ?></p>
					</div>
				</div>

				<div class="cacap-avatar">
					<img src="<?php echo apply_filters( 'bp_core_default_avatar_user', bp_core_avatar_default( 'local' ) ) ?>" />
				</div>
			</div>

			<div style="clear:both;"></div>

			<div class="cacap-row cacap-row-vitals">
				<p class="cacap-instructions"><?php esc_html_e( 'Fields in the "Vitals" area will be displayed in individual rows in the bottom half of the profile header.', 'cacap' ) ?></p>
				<ul id="cacap-vitals" class="cacap-droppable">
					<p class="cacap-inner-label"><?php esc_html_e( 'Vitals', 'cacap' ) ?></p>
				</ul>
			</div>

		</div>

		<h4 class="cacap-section-header"><?php esc_html_e( 'Available Fields', 'cacap' ) ?></h4>
		<div class="cacap-available-fields">
			<?php $this->available_fields_markup(); ?>
		</div>


		<?php
	}

	public function settings_section_profile_header_edit() {
		echo 'foo edit';
	}

	public function settings_section_widgets() {
		echo 'foo widgets';
	}

	public function available_fields_markup() {
		$groups = BP_XProfile_Group::get( array(
			'hide_empty_groups' => true,
			'hide_empty_fields' => false,
			'fetch_fields' => true,
			'fetch_field_data' => false,
		) );

		// Put into a flat list
		$fields = array();
		foreach ( $groups as $group ) {
			$fields = array_merge( $group->fields, $fields );
		}

		$in_use = array( 1 );

		$field_lis = array();
		foreach ( $fields as $field ) {
			$in_use_class = in_array( $field->id, $in_use ) ? 'in-use' : '';

			$field_lis[] = sprintf(
				'<li data-field-id="%s" class="%s" id="%s">%s</li>',
				intval( $field->id ),
				$in_use_class,
				'available-field-' . intval( $field->id ),
				esc_html( $field->name )
			);
		}

		echo '<ul id="available-fields">';
		echo implode( '', $field_lis );
		echo '</ul>';
		echo '<div style="clear:both;"></div>';
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

	public function process_save_header_public() {
		check_admin_referer( 'cacap-admin-options' );

		$saved_values = json_decode( stripslashes( $_POST['cacap-saved-values-header-public'] ) );

		$values = array(
			'brief_descriptor' => $saved_values->brief_descriptor,
			'about_you' => $saved_values->about_you,
			'vitals' => $saved_values->vitals,
		);

		bp_update_option( 'cacap_header_fields', $values );
	}

	/**
	 * Enqueue JS and CSS assets.
	 */
	public function enqueue_assets() {
		wp_enqueue_style( 'cacap-admin', CACAP_PLUGIN_URL . '/assets/css/admin.css' );

		wp_register_script( 'cacap-columnizer', CACAP_PLUGIN_URL . '/lib/jquery.columnizer/jquery.columnizer.js', array( 'jquery' ) );

		wp_enqueue_script( 'cacap-admin', CACAP_PLUGIN_URL . '/assets/js/admin.js', array(
			'jquery-ui-droppable',
			'jquery-ui-draggable',
			'jquery-ui-sortable',
			'cacap-columnizer',
		) );
	}
}
add_action( bp_core_admin_hook(), array( 'CACAP_Admin', 'init' ) );
