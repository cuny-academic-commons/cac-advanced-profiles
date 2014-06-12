<?php

class CACAP_Admin {
	var $self_url;

	public static function init() {
		static $instance;
		if ( empty( $instance ) ) {
			$instance = new self();
		}
		return $instance;
	}

	private function __construct() {
		$this->self_url = self_admin_url( 'users.php?page=cacap-admin' );
		$this->add_menus();
		$this->configure_settings_sections();
		add_action( 'admin_menu', array( $this, 'add_menus' ) );
	}

	public function add_menus() {
		if ( ! empty( $_POST['cacap-saved-values-header-public'] ) ) {
			$this->process_save_header_public();
		}

		if ( ! empty( $_POST['cacap-saved-values-header-edit-left'] ) && ! empty( $_POST['cacap-saved-values-header-edit-right'] ) ) {
			$this->process_save_header_edit();
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
			__( 'Profile Header (Public)', 'cacap' ),
			array( $this, 'settings_section_profile_header_public' ),
			'cacap-admin'
		);

		add_settings_section(
			'cacap-profile-header-edit',
			__( 'Profile Header (Edit Mode)', 'cacap' ),
			array( $this, 'settings_section_profile_header_edit' ),
			'cacap-admin'
		);

		/*
		add_settings_section(
			'cacap-widgets',
			__( 'Widgets', 'cacap' ),
			array( $this, 'settings_section_widgets' ),
			'cacap-admin'
		);
		*/
	}

	public function admin_menu() {
		$current_section = 'cacap-profile-header-public';
		if ( isset( $_GET['section'] ) ) {
			// @todo whitelist
			$current_section = stripslashes( $_GET['section'] );
		}

		?>
		<div class="wrap cacap-admin">
			<h2><?php esc_html_e( 'CAC Advanced Profiles', 'cacap' ) ?></h2>

			<?php if ( ! empty( $_GET['updated'] ) ) : ?>
				<div id="message" class="updated below-h2"><p><?php esc_attr_e( 'Settings updated!', 'cacap' ) ?></p></div>
			<?php endif; ?>

			<?php $this->admin_tabs( $current_section ) ?>

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

	protected function field_markup( $field_id ) {
		$field_object = new BP_XProfile_Field( $field_id );
		$field_markup = sprintf(
			'<li data-field-id="%s" id="%s">%s</li>',
			intval( $field_object->id ),
			'vital-field-' . intval( $field_object->id ),
			esc_html( $field_object->name )
		);

		return $field_markup;
	}

	public function settings_section_profile_header_public() {
		$fields = cacap_get_header_fields();

		$bd_field = ! empty( $fields['brief_descriptor'] ) ? intval( $fields['brief_descriptor'] ) : 0;
		$ay_field = ! empty( $fields['about_you'] ) ? intval( $fields['about_you'] ) : 0;
		$vital_fields = ! empty( $fields['vitals'] ) ? $fields['vitals'] : array();

		$bd_class = $ay_class = $vitals_class = 'empty';
		$bd_field_markup = $ay_field_markup = $vital_fields_markup = '';

		if ( ! empty( $bd_field ) ) {
			$bd_class = 'not-empty';
			$bd_field_markup = $this->field_markup( $bd_field );
		}

		if ( ! empty( $ay_field ) ) {
			$ay_class = 'not-empty';
			$ay_field_markup = $this->field_markup( $ay_field );
		}

		if ( ! empty( $vital_fields ) ) {
			$vital_class = 'not-empty';
			foreach ( $vital_fields as $vf ) {
				$vital_fields_markup .= $this->field_markup( $vf );
			}
		}

		?>

		<p><?php esc_html_e( 'The Header section below represents the layout of the header of user profiles, when viewed in public (non-edit) mode. To add profile fields to the header, drag them from the Available Fields section to the appropriate area in the Header section.', 'cacap' ) ?></p>

		<h4 class="cacap-section-header"><?php esc_html_e( 'Header', 'cacap' ) ?></h4>
		<div class="cacap-header">
			<div class="cacap-row">
				<div class="cacap-hero">
					<h1><?php esc_html_e( 'User Name', 'cacap' ) ?></h1>

					<p class="cacap-instructions"><?php esc_html_e( 'The "Brief Descriptor" field is a one-sentence heading that appears directly below the user&#8217;s name. (One field only.)', 'cacap' ) ?></p>
					<h4 id="cacap-brief-descriptor" class="cacap-droppable cacap-sortable cacap-single <?php echo $bd_class ?>">
						<p class="cacap-inner-label"><?php esc_html_e( 'Brief Descriptor', 'cacap' ) ?></p>
						<?php echo $bd_field_markup; ?>
					</h4>

					<p class="cacap-instructions"><?php esc_html_e( 'The "About You" field is a summary (300 characters or less) of a user&#8217;s work and interests. (One field only.)', 'cacap' ) ?></p>
					<div id="cacap-about-you" class="cacap-droppable cacap-sortable cacap-single <?php echo $ay_class ?>">
						<p class="cacap-inner-label"><?php esc_html_e( 'About You', 'cacap' ) ?></p>
						<?php echo $ay_field_markup; ?>
					</div>
				</div>

				<div class="cacap-avatar">
					<img src="<?php echo apply_filters( 'bp_core_default_avatar_user', bp_core_avatar_default( 'local' ) ) ?>" />
				</div>
			</div>

			<div style="clear:both;"></div>

			<div class="cacap-row cacap-row-vitals">
				<p class="cacap-instructions"><?php esc_html_e( 'Fields in the "Vitals" area will be displayed in individual rows in the bottom half of the profile header. (Supports multiple fields.)', 'cacap' ) ?></p>
				<ul id="cacap-vitals" class="cacap-droppable cacap-sortable <?php echo $vital_class; ?>">
					<p class="cacap-inner-label"><?php esc_html_e( 'Vitals', 'cacap' ) ?></p>
					<?php echo $vital_fields_markup; ?>
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
		$fields = cacap_get_header_fields( 'edit' );

		?>

		<p><?php esc_html_e( 'When editing their profiles, users will see two columns of editable fields in their header area. Below, you can modify the arrangement of fields in these two columns.', 'cacap' ) ?></p>

		<p><?php printf( __( 'The fields displayed below are those that have been selected for display on the <a href="%s">Profile Header (Public)</a> tab. To add or remove fields, visit that tab.', 'cacap' ), bp_get_admin_url( add_query_arg( array( 'page' => 'cacap-admin', 'section' => 'cacap-profile-header-public', ) ) ) ) ?></p>

		<div class="cacap-profile-edit-columns">
			<div id="cacap-profile-edit-column-left">
				<ul>
				<?php foreach ( $fields['left'] as $field_id ) : ?>
					<?php echo $this->field_markup( $field_id ) ?>
				<?php endforeach; ?>
				</ul>
			</div>

			<div id="cacap-profile-edit-column-right">
				<ul>
				<?php foreach ( $fields['right'] as $field_id ) : ?>
					<?php echo $this->field_markup( $field_id ) ?>
				<?php endforeach; ?>
				</ul>
			</div>
		</div>

		<div style="clear:both;"></div>

		<?php
	}

	public function settings_section_widgets() {
		echo 'foo widgets';
	}

	public function available_fields_markup() {
		// Exclude items that are storage for widgets
		// Note: This is going to be fragile. Need to store this info
		// somewhere, maybe in xprofilemeta
		global $wpdb, $bp;
		$widget_types = cacap_widget_types();
		$names = wp_list_pluck( $widget_types, 'name' );
		foreach ( $names as &$name ) {
			$name = $wpdb->prepare( '%s', $name );
		}
		$exclude_fields = $wpdb->get_col( "SELECT id FROM {$bp->profile->table_name_fields} WHERE name IN (" . implode( ',', $names ) . ")" );

		if ( empty( $exclude_fields ) ) {
			$exclude_fields = array( 0 );
		}

		$groups = BP_XProfile_Group::get( array(
			'hide_empty_groups' => true,
			'hide_empty_fields' => false,
			'fetch_fields' => true,
			'fetch_field_data' => false,
			'exclude_fields' => $exclude_fields,
		) );

		// Put into a flat list
		$fields = array();
		foreach ( $groups as $group ) {
			$fields = array_merge( $group->fields, $fields );
		}

		$in_use = cacap_get_header_fields();
		$in_use_flat = array_merge(
			array( $in_use['brief_descriptor'] ),
			array( $in_use['about_you'] ),
			$in_use['vitals'],
			array( 1 )
		);

		$field_lis = array();
		foreach ( $fields as $field ) {
			if ( in_array( $field->id, $in_use_flat ) ) {
				continue;
			}

			$field_lis[] = sprintf(
				'<li data-field-id="%s" id="%s">%s</li>',
				intval( $field->id ),
				'available-field-' . intval( $field->id ),
				esc_html( $field->name )
			);
		}

		echo '<ul id="available-fields" class="cacap-sortable">';
		echo '<p>' . __( 'Drag fields here to disable.', 'cacap' ) . '</p>';

		if ( empty( $field_lis ) ) {
			echo '<p class="cacap-inner-label">' . __( 'All fields are in use.', 'cacap' ) . '</p>';
		} else {
			echo implode( '', $field_lis );
		}

		echo '</ul>';
		echo '<div style="clear:both;"></div>';
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
			$is_current = $active_tab === ( substr( $tab_data['href'], 0 - ( strlen( $active_tab ) ) ) );
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

		wp_redirect( add_query_arg( array(
			'section' => 'cacap-profile-header-public',
			'updated' => 1,
		), $this->self_url ) );
		die();
	}

	public function process_save_header_edit() {
		check_admin_referer( 'cacap-admin-options' );

		$left_values = json_decode( stripslashes( $_POST['cacap-saved-values-header-edit-left'] ) );
		$right_values = json_decode( stripslashes( $_POST['cacap-saved-values-header-edit-right'] ) );

		$values = array(
			'left' => $left_values,
			'right' => $right_values,
		);

		bp_update_option( 'cacap_header_fields_edit', $values );

		wp_redirect( add_query_arg( array(
			'section' => 'cacap-profile-header-edit',
			'updated' => 1,
		), $this->self_url ) );
		die();
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

		wp_localize_script( 'cacap-admin', 'CACAP_Admin', array(
			'warn_on_leave' => __( 'You have made changes to the page.', 'cacap' ),
		) );
	}
}
add_action( bp_core_admin_hook(), array( 'CACAP_Admin', 'init' ) );
