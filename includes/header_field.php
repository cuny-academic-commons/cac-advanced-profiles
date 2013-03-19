<?php

class CACAP_Header_Field {
	protected $user_id = 0;
	protected $field_id = 0;
	protected $value;

	public function init( $args = array() ) {
		$r = wp_parse_args( $args, array(
			'user_id' => bp_displayed_user_id(),
		) );

		if ( isset( $r['user_id'] ) ) {
			$this->user_id = intval( $r['user_id'] );
		}

		if ( isset( $r['field_id'] ) ) {
			$this->field_id = intval( $r['field_id'] );
		}

		$this->schema = new CACAP_Profile_Data_Schema();
	}

	public function create() {
		return $this->save();
	}

	public function save() {
		return xprofile_set_field_data( $this->field_id, $this->user_id, $this->value );
	}

	public function get() {
		return xprofile_get_field_data( $this->field_id, $this->user_id );
	}

	public function delete() {
		return xprofile_delete_field_data( $this->field_id, $this->user_id );
	}

	public function set_value( $value ) {
		$this->value = $value;
	}
}
