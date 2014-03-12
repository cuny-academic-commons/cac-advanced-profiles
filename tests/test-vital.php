<?php

/**
 * @group vital
 */
class CACAP_Tests_Vital extends BP_UnitTestCase {
	/**
	 * @group css_class
	 */
	public function test_build_css_class() {
		// not provided
		$v = new CACAP_Vital( array(
			'id' => 'foo',
		) );

		$this->assertSame( 'cacap-vitals-foo', $v->css_class );

		// provided
		$v2 = new CACAP_Vital( array(
			'id' => 'foo',
			'css_class' => 'bar',
		) );

		$this->assertSame( 'bar', $v2->css_class );
	}

	/**
	 * @group title
	 */
	public function test_build_title() {
		// not provided
		$v = new CACAP_Vital( array(
			'id' => 'foo',
		) );

		$this->assertSame( '', $v->title );

		// provided
		$v2 = new CACAP_Vital( array(
			'id' => 'foo',
			'title' => 'Bar',
		) );

		$this->assertSame( 'Bar', $v2->title );
	}

	/**
	 * @group content
	 */
	public function test_build_content() {
		// not provided
		$v = new CACAP_Vital( array(
			'id' => 'foo',
		) );

		$this->assertSame( '', $v->content );

		// provided
		$v2 = new CACAP_Vital( array(
			'id' => 'foo',
			'content' => 'Bar',
		) );

		$this->assertSame( 'Bar', $v2->content );
	}

	/**
	 * @group position
	 */
	public function test_build_position() {
		// not provided
		$v = new CACAP_Vital( array(
			'id' => 'foo',
		) );

		$this->assertSame( 50, $v->position );

		// provided
		$v2 = new CACAP_Vital( array(
			'id' => 'foo',
			'position' => 35,
		) );

		$this->assertSame( 35, $v2->position );

		// provided - string to int
		$v3 = new CACAP_Vital( array(
			'id' => 'foo',
			'position' => '30',
		) );

		$this->assertSame( 30, $v3->position );

		// provided - string to garbage
		$v3 = new CACAP_Vital( array(
			'id' => 'foo',
			'position' => 'bar',
		) );

		$this->assertSame( 50, $v3->position );
	}
}
