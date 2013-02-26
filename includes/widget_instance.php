<?php

class CACAP_Widget_Instance {
	protected $id;
	protected $data;
	protected $widget;

	public function __construct( $id ) {
		$this->id = intval( $id );

		$container = new CACAP_Widget_Instance();
		$this->schema = $container->get_widget_instance_schema();
		$this->get_data();
	}

	public function get_data() {
		$this->data = $this->schema->get_data_by_id( $this->id );
		return $this->data;
	}
}
