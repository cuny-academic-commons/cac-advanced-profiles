<?php

class CACAP_Header_Field {
	protected $user_id = 0;
	protected $field_id = 0;
	protected $field_type = 'text';
	protected $field_name;
	protected $value;

	/**
	 * @todo Should we be auto-creating fields when not found?
	 */
	public function init( $args = array() ) {
		$r = wp_parse_args( $args, array(
			'user_id' => bp_displayed_user_id(),
		) );

		if ( isset( $r['user_id'] ) ) {
			$this->user_id = intval( $r['user_id'] );
		}

		if ( isset( $r['field_id'] ) ) {
			$this->field_id = intval( $r['field_id'] );
		} else if ( isset( $r['field_name'] ) ) {
			$this->field_name = $r['field_name'];
			$this->field_id = xprofile_get_field_id_from_name( $this->field_name );
		}

		// @todo sanitize?
		if ( isset( $r['field_type'] ) ) {
			$this->field_type = $r['field_type'];
		}

		// @todo This is overkill
		$this->schema = new CACAP_Profile_Data_Schema();
	}

	/**
	 * Semantics? This is for inserting profile data
	 */
	public function insert() {
		return $this->save();
	}

	public function save() {
		return xprofile_set_field_data( $this->field_id, $this->user_id, $this->value );
	}

	public function delete() {
		return xprofile_delete_field_data( $this->field_id, $this->user_id );
	}

	public function get_value() {
		if ( is_null( $this->value ) ) {
			$this->value = xprofile_get_field_data( $this->field_id, $this->user_id );
		}

		return $this->value;
	}

	public function get_field_id() {
		return $this->field_id;
	}

	public function get_field_name() {
		return $this->field_name;
	}

	public function get_field_type() {
		return $this->field_type;
	}

	public function set_value( $value ) {
		$this->value = $value;
	}
}
