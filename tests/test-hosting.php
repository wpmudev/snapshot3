<?php

/**
 * @group hosting
 * @group model
 */

class HostingTest extends WP_UnitTestCase {

	public function test_exists() {
		$this->assertTrue(
			class_exists( 'Snapshot_Model_Hosting' )
		);
	}

	public function setUp() {
		$this->_model = new Snapshot_Model_Hosting();
	}

	public function test_is_on_wpmudev_hosting() {
		$is_wpmu_hosting = $this->_model->is_wpmu_hosting();
		$this->assertFalse( $is_wpmu_hosting, 'Not on WPMUDEV hosting' );

		add_filter( $this->_model->get_filter( 'is_wpmu_hosting' ), array( $this, 'mock_wpmudev_hosting' ) );
		$mocked_wpmu_hosting = $this->mock_wpmudev_hosting();
		$filter_mocked_wpmu_hosting = $this->_model->is_wpmu_hosting();
		$this->assertFalse( empty( $filter_mocked_wpmu_hosting ), 'Being on WPMUDEV hosting is mocked' );
		$this->assertEquals( $mocked_wpmu_hosting, $filter_mocked_wpmu_hosting, 'Being on WPMUDEV hosting gets taken from filter' );
		remove_filter( $this->_model->get_filter( 'is_wpmu_hosting' ), array( $this, 'mock_wpmudev_hosting' ) );
	}

	public function mock_wpmudev_hosting() {
		return true;
	}
}
