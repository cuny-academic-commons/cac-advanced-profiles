<?php

class CACAP_Widget_Instance_Schema {
	public function get_data_by_id( $id = 0 ) {
		global $wpdb, $bp;
		$value = $wpdb->get_var( $wpdb->prepare( "SELECT value FROM {$bp->xprofile->table_name_data} WHERE id = %d", intval( $id ) ) );
		return maybe_unserialize( $value );
	}
}
