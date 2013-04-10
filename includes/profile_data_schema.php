<?php

class CACAP_Profile_Data_Schema {
	public function get_data_by_id( $id = 0 ) {
		global $wpdb, $bp;
		$value = $wpdb->get_var( $wpdb->prepare( "SELECT value FROM {$bp->xprofile->table_name_data} WHERE id = %d", intval( $id ) ) );
		return maybe_unserialize( $value );
	}

	public function save_flat_data_for_user( $field_id, $user_id, $value ) {
		return xprofile_set_field_data( $field_id, $user_id, $value );
	}
}
