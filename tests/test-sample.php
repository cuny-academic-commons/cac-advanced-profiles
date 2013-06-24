<?php

class CACAP_Test_Widget_Positions extends BP_UnitTestCase {

	/**
	 * @group get_term_for_position
	 */
	function test_get_term_for_position_term_exists() {
		$cwp = new CACAP_Widget_Positions;
		$term = wp_insert_term( 'foo', 'cacap_position_college' );
		$term_for_position = $cwp->get_term_for_position( array(
			'college' => 'foo',
			'department' => 'bar',
			'title' => 'baz',
		), 'college' );

		$this->assertEquals( $term['term_id'], $term_for_position );
	}

	/**
	 * @group save_instance_for_user
	 */
	function test_save_instance_for_user_no_existing_fields() {
		$u = $this->create_user();
		$args = array(
			'user_id' => $u,
			'content' => array(
				array(
					'college' => 'Foo University',
					'department' => 'Philosophy',
					'title' => 'King',
				),
				array(
					'college' => 'Bar University',
					'department' => 'English',
					'title' => 'Professor',
				),
			),
		);

		$cacap_widget_positions = new CACAP_Widget_Positions();
		$cacap_widget_positions->save_instance_for_user( $args );

		$stored_positions = $cacap_widget_positions->get_user_positions( $u );

		$this->assertEquals( $args['content'], $stored_positions );
	}
}

