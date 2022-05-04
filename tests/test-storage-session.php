<?php

/**
 * @group model
 * @group storage
 */
class SessionSitemetaTest extends WP_UnitTestCase {

	public function test_load() {
		$model = new Snapshot_Model_Storage_Session( 'test-namespace' );
		$this->assertTrue( $model->load() );
	}

	public function test_save() {
		$model = new Snapshot_Model_Storage_Session( 'test-namespace' );

		$key = 'test key';
		$val = 'test value';

		$model->set_value( $key, $val );
		$this->assertTrue( $model->save() );

		$model->clear();
		$this->assertFalse( $model->get_value( $key ) );
		$this->assertTrue( $model->load() );
		$this->assertEquals( $val, $model->get_value( $key ) );
	}
}
