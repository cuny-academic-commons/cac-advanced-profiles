<?php

class CACAP_Widget_Instance {
	protected $id;
	protected $data;
	protected $widget;

	public function __construct( $id ) {
		$this->id = intval( $id );

		if ( ! class_exists( 'CACAP_Profile_Data_Schema' ) ) {
			require( $this->includes_dir . 'profile_data_schema.php' );
		}

		$this->schema = new CACAP_Profile_Data_Schema();

		$this->get_data();
	}

	public function get_data() {
		$this->data = $this->schema->get_data_by_id( $this->id );
		return $this->data;
	}
}
