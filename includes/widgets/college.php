<?php

/**
 * The College widget is read-only, for legacy data
 *
 * It'll only be shown for users who do not have Positions filled in
 */
class CACAP_Widget_College extends CACAP_Widget {
	public function __construct() {
		parent::init( array(
			'name' => __( 'College', 'cacap' ),
			'slug' => 'college',
			'allow_new' => false,
			'allow_edit' => true,
		) );
	}

	/**
	 * Save widget instance for a given user
	 *
	 * Overriding the parent method because we need to save in a field
	 * named differently, to avoid name clashes and validation issues in BP
	 *
	 * @param array $args
	 * @return array See CACAP_Widget_Instance::format_instance() for format
	 */
	public function save_instance_for_user( $args = array() ) {
		$r = wp_parse_args( $args, array(
			'key' => '',
			'user_id' => 0,
			'title' => $this->name,
			'content' => '',
		) );

		if ( ! $r['user_id'] ) {
			return false;
		}

		if ( ! $r['title'] ) {
			$r['title'] = $this->name;
		}

		// Lame - autocreate field if it doesn't exist
		$field_id = xprofile_get_field_id_from_name( 'College Widget' );
		if ( ! $field_id ) {
			$field_id = xprofile_insert_field( array(
				'field_group_id' => 1,
				'type' => 'textbox',
				'name' => 'College Widget',
			) );
		}

		if ( xprofile_set_field_data( $field_id, absint( $r['user_id'] ), $r['content'] ) ) {
			return CACAP_Widget_Instance::format_instance( array(
				'user_id' => $r['user_id'],
				'key' => $r['title'],
				'value' => $r['content'],
				'widget_type' => $this->slug,
			) );
		} else {
			// phooey
		}
	}

	public function get_instance_for_user( $args = array() ) {
		$r = wp_parse_args( $args, array(
			'user_id' => 0,
			'key' => null,
		) );

		$college = xprofile_get_field_data( 'College Widget', absint( $r['user_id'] ) );
		return $college;
	}

	public function edit_content_markup( $value, $key ) {
		if ( is_array( $value ) ) {
			$value = implode( ', ', $value );
		}
		$field = '<input disabled="disabled" class="cacap-edit-input" name="college-dummy" value="' . esc_attr( $value ) . '" />';
		$field .= '<input type="hidden" name="' . esc_attr( $key ) . '[content]" value="' . esc_attr( $value ) . '" />';
		$field .= '<p class="description deprecated-para">' . $this->deprecated_para() . '</p>';
		return $field;
	}

	protected function deprecated_para() {
		return __( 'We are phasing out the <strong>College</strong> field in favor of <strong>Positions</strong>. Once you have created a Positions widget (see "Add New Section" above), your College widget will disappear. For this reason, College cannot be edited.', 'cacap' );
	}
}
