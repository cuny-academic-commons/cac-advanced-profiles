<?php

class CACAP_Widget_Positions extends CACAP_Widget {
	public $colleges = array(
		'*Non-CUNY',
		'Baruch College',
		'Borough of Manhattan Community College',
		'Bronx Community College',
		'Brooklyn College',
		'City College',
		'College of Staten Island',
		'CUNY Central',
		'CUNY Graduate Center',
		'CUNY Graduate School of Journalism',
		'CUNY School of Law',
		'Hostos Community College',
		'Hunter College',
		'Medgar Evers College',
		'Guttman Community College',
		'NYC College of Technology',
		'Queens College',
		'Queensborough Community College',
		'School of Professional Studies',
		'Sophie Davis School of Biomedical Education',
		'Teacher Academy',
		'York College',
	);

	public function __construct() {
		static $setup;

		parent::init( array(
			'name' => __( 'Positions', 'cacap' ),
			'slug' => 'positions',
		) );

		$setup = true;
	}

	/**
	 * Saves instance of Positions widget for user
	 *
	 * @since 1.0
	 */
	public function save_instance_for_user( $args = array() ) {
		$r = wp_parse_args( $args, array(
			'key' => '',
			'user_id' => 0,
			'title' => '',
			'content' => '',
		) );

		// @todo better error reporting
		if ( ! $r['user_id'] ) {
			return false;
		}

		$submitted_positions = ! empty( $r['content'] ) ? $r['content'] : array();
		$new_positions = array();

		// Parse the submitted positions and fetch the term ids
		// This will format the $new_positions array, which will be
		// stored as usermeta
		foreach ( $submitted_positions as $submitted_position ) {
			$new_position = array();

			// Discard any incomplete entries along the way
			foreach ( array( 'college', 'department', 'title' ) as $type ) {
				$new_position[ $type ] = $this->get_term_for_position( $submitted_position, $type );

				if ( empty( $new_position[ $type ] ) ) {
					continue 2;
				}
			}

			$new_positions[] = $new_position;
		}

		// Now that we have fetched the term ids, we'll save them to
		// the user objects. $new_term_ids is an array of term ids,
		// re-sorted by college/dept/title, rather than by position.
		// This makes it easier to reset the object terms for each
		$new_term_ids = array(
			'college' => array(),
			'department' => array(),
			'title' => array(),
		);

		foreach ( $new_positions as $new_position ) {
			foreach ( $new_position as $np_type => $np_term_id ) {
				$new_term_ids[ $np_type ][] = $np_term_id;
			}
		}

		foreach ( $new_term_ids as $nti_type => $nti_term_ids ) {
			wp_set_object_terms( $r['user_id'], $nti_term_ids, 'cacap_position_' . $nti_type );
		}

		// Save to usermeta. This is what's used to generate profile
		// output
		bp_update_user_meta( $r['user_id'], 'cacap_positions', $new_positions );

		// @todo Store as flat text. This will make display more
		// efficient, but introduces complications for when a tax term
		// is changed and everything needs to be regenerated
		return CACAP_Widget_Instance::format_instance( array(
			'user_id' => $r['user_id'],
			'key' => 'cacap_positions',
			'widget_type' => $this->slug,
		) );
	}

	/**
	 * Format:
	 *
	 * array(
	 *   array(
	 *     'college' => 'College 1',
	 *     'department' => 'Department at College 1',
	 *     'title' => 'Title 1',
	 *   ),
	 *   array(
	 *     'college' => 'College 2',
	 *     'title' => 'Title 2',
	 *   ),
	 * );
	 */
	public function get_user_positions( $user_id ) {
		$positions = bp_get_user_meta( $user_id, 'cacap_positions', true );

		if ( '' == $positions || ! is_array( $positions ) ) {
			$positions = array();
		}

		// Convert term ids to strings
		$formatted_positions = array();
		foreach ( $positions as $position ) {
			$formatted_positions[] = $this->get_text_for_position( $position );
		}

		return $formatted_positions;
	}

	/**
	 * For a submitted position and type, get a term ID
	 *
	 * Will create one if not found
	 *
	 * @param array $position Should contain 'college', 'deparment',
	 *   and 'title'. Each should have a string value - we'll convert to id
	 * @param string $type 'college', 'department', or 'title'. The
	 *   position term type we're looking for
	 * @return int Term id
	 */
	public function get_term_for_position( $position, $type ) {
		$term_id = null;

		if ( ! empty( $position[ $type ] ) ) {
			$value = $position[ $type];
			$tax   = 'cacap_position_' . $type;

			$term = get_term_by( 'name', $value, $tax );

			// No term found. Create one
			if ( empty( $term ) ) {
				$term = wp_insert_term( $value, $tax );

				if ( ! empty( $term ) ) {
					$term_id = $term['term_id'];
				}
			} else {
				$term_id = $term->term_id;
			}
		}

		return intval( $term_id );
	}

	/**
	 * Convert a position array of term IDs to array of strings
	 *
	 * @param array $position Term ids for college, dept, title
	 * @param array $formatted_position Term names
	 */
	public function get_text_for_position( $position ) {
		$formatted_position = array();
		foreach ( (array) $position as $type => $term_id ) {
			$term = get_term( $term_id, 'cacap_position_' . $type );
			if ( isset( $term->name ) ) {
				$formatted_position[ $type ] = $term->name;
			}
		}

		return $formatted_position;
	}

	public function get_instance_for_user( $args = array() ) {
		$r = wp_parse_args( $args, array(
			'user_id' => 0,
		) );

		return $this->get_user_positions( $r['user_id'] );
	}

	public function get_display_value_from_value( $value ) {
		$html  = '<ul class="cacap-positions-list">';

		foreach ( (array) $value as $position ) {
			foreach ( array( 'title', 'department', 'college' ) as $type ) {
				$$type = isset( $position[ $type ] ) ? $position[ $type ] : '';
			}
			$html .= '<li>';
			$html .=   '<span class="cacap-positions-title">' . $title . '</span>';
			$html .=   '<span class="cacap-positions-department">' . $department . '</span>';
			$html .=   '<span class="cacap-positions-college">' . $college . '</span>';
			$html .= '</li>';
		}

		$html .= '</ul>';

		return $html;
	}

	/**
	 * Display the content markup
	 *
	 * Overriding the parent because we have already done the necessary
	 * escaping, and want to avoid esc_html()
	 *
	 * @param string $value
	 */
	public function display_content_markup( $value ) {
		return $value;
	}

	public function edit_content_markup( $value, $key ) {
		$markup = '';

		// First, show existing fields
		$markup .= '<div class="cacap-position-add-new-title hide-if-no-js"><a class="cacap-add-position" href="#">' . __( '&#43; Add New', 'cacap' ) . '</a></div>';

		if ( ! empty( $value ) && is_array( $value ) ) {
			$counter = 0;
			foreach ( $value as $position ) {
				$counter++;
				$current_college = isset( $position['college'] ) ? $position['college'] : '';
				$current_department = isset( $position['department'] ) ? $position['department'] : '';
				$current_title = isset( $position['title'] ) ? $position['title'] : '';

				$markup .= '<a href="#" class="hide-if-no-js cacap-delete-position confirm" id="cacap-delete-position-' . $counter . '">' . 'x' . '</a>';
				$markup .= '<ul id="cacap-position-' . $counter . '">';

				$markup .=   '<li>';
				$markup .=     '<label for="' . esc_attr( $key ) . '_college">' . __( 'College', 'cacap' ) . '</label>';
				$markup .=     '<select name="' . esc_attr( $key ) . '[content][' . $counter . '][college]" id="' . esc_attr( $key ) . '_college" class="cacap-position-field-college">';

				foreach ( $this->colleges as $college ) {
					$markup .= '<option value="' . esc_attr( $college ) . '" ' . selected( $college, $current_college, false ) . '>' . esc_attr( $college ) . '</option>';
				}

				$markup .=     '</select>';
				$markup .=   '</li>';

				$markup .=   '<li>';
				$markup .=     '<label for="' . esc_attr( $key ) . '_department">' . __( 'Department', 'cacap' ) . '</label>';
				$markup .=     '<input class="cacap-edit-input cacap-position-field-department" name="' . esc_attr( $key ) . '[content][' . $counter . '][department]" id="' . esc_attr( $key ) . '_department" value="' . esc_attr( $current_department ) . '" />';
				$markup .=   '</li>';

				$markup .=   '<li>';
				$markup .=     '<label for="' . esc_attr( $key ) . '_title">' . __( 'Title', 'cacap' ) . '</label>';
				$markup .=     '<input class="cacap-edit-input cacap-position-field-title" name="' . esc_attr( $key ) . '[content][' . $counter . '][title]" id="' . esc_attr( $key ) . '_title" value="' . esc_attr( $current_title ) . '" />';
				$markup .=   '</li>';
				$markup .= '</ul>';
			}
		}

		// Second, provide a blank set of fields
		// When JS is enabled, this'll be hidden and used to clone new
		// position fields. Otherwise, it'll be used for position entry

		$markup .= '<ul id="cacap-position-new" class="cacap-position-add-new hide-if-js">';
		$markup .= '<a href="#" class="hide-if-no-js cacap-delete-position confirm" id="cacap-delete-position-new">' . 'x' . '</a>';

		$markup .=   '<li>';
		$markup .=     '<label for="cacap-position-new-college">' . __( 'College', 'cacap' ) . '</label>';
		$markup .=     '<select class="cacap-position-field-college" name="newwidgetkey[content][new][college]" id="cacap-position-new-college">';

		foreach ( $this->colleges as $college ) {
			$markup .= '<option value="' . esc_attr( $college ) . '">' . esc_attr( $college ) . '</option>';
		}

		$markup .=     '</select>';
		$markup .=   '</li>';

		$markup .=   '<li>';
		$markup .=     '<label for="cacap-position-new-department">' . __( 'Department', 'cacap' ) . '</label>';
		$markup .=     '<input class="cacap-edit-input cacap-position-field-department" name="newwidgetkey[content][new][department]" id="cacap-position-new-department" val="" />';
		$markup .=   '</li>';

		$markup .=   '<li>';
		$markup .=     '<label for="cacap-position-new-title">' . __( 'Title', 'cacap' ) . '</label>';
		$markup .=     '<input class="cacap-edit-input cacap-position-field-title" name="newwidgetkey[content][new][title]" id="cacap-position-new-title" val="" />';
		$markup .=   '</li>';
		$markup .= '</ul>';

		return $markup;
	}

	public static function taxonomy_setup() {

	}
}

function cacap_positions_register_taxonomies() {
	register_taxonomy( 'cacap_position_college', 'user', array(
		'hierarchical' => false,
		'show_ui' => true,
	) );
	register_taxonomy( 'cacap_position_department', 'user', array(
		'hierarchical' => false,
		'show_ui' => true,
	) );
	register_taxonomy( 'cacap_position_title', 'user', array(
		'hierarchical' => false,
		'show_ui' => true,
	) );
}
add_action( 'init', 'cacap_positions_register_taxonomies', 100 );

function cacap_positions_suggest_cb() {
	$field = isset( $_GET['field'] ) ? $_GET['field'] : '';
	$value = isset( $_GET['term'] ) ? urldecode( $_GET['term'] ) : '';

	$retval = array();

	if ( ! taxonomy_exists( 'cacap_position_department' ) ) {
		cacap_positions_register_taxonomies();
	}

	if ( $field && $value ) {
		switch ( $field ) {
			case 'department' :
				$terms = get_terms( 'cacap_position_department', array(
					'name__like' => $value,
				) );

				foreach ( $terms as $term ) {
					$retval[] = array(
						'id' => $term->name,
						'label' => $term->name,
						'value' => $term->name,
					);
				}

				break;
		}
	}

	die( json_encode( $retval ) );
}
add_action( 'wp_ajax_cacap_position_suggest', 'cacap_positions_suggest_cb' );

/**
 * When a search term is found, also search in position terms
 *
 * NOTE!! This will only work with the legacy queries
 */
function cacap_filter_member_search_by_position( $s, $sql ) {
	if ( ! empty( $sql['where_searchterms'] ) ) {
		preg_match( '/%%(.*?)%%/', $sql['where_searchterms'], $matches );
		if ( ! empty( $matches[1] ) ) {
			$search_term = $matches[1];
			$taxes = array(
				'cacap_position_college',
				'cacap_position_department',
				'cacap_position_title',
			);
			$terms = get_terms(
				$taxes,
				array(
					'name__like' => $search_term,
				)
			);
			$term_ids = wp_list_pluck( $terms, 'term_id' );

			$user_ids = get_objects_in_term( $term_ids, $taxes );
			if ( ! empty( $user_ids ) ) {
				// Royal pain. No way to ensure distinct except this
				global $wpdb;
				$interim_s = preg_replace( '/u.ID.*?FROM/', 'u.ID FROM', $s );
				$interim_s = preg_replace( '/LIMIT.*$/', '', $interim_s );

				$interim_members = $wpdb->get_col( $interim_s );

				$user_ids = array_unique( array_merge( $interim_members, $user_ids ) );

				$s = preg_replace( '/WHERE (.*?) ORDER BY/', 'WHERE \1 AND u.ID in (' . implode( ',', wp_parse_id_list( $user_ids ) ) . ') ORDER BY', $s );
				// The manual search takes the place of the spd stuff
				$s = str_replace( $sql['join_profiledata_search'], '', $s );
				$s = str_replace( $sql['where_searchterms'], '', $s );
			}
		}
	}

	return $s;
}
add_filter( 'bp_core_get_paged_users_sql', 'cacap_filter_member_search_by_position', 100, 2 );
add_filter( 'bp_core_get_total_users_sql', 'cacap_filter_member_search_by_position', 100, 2 );

