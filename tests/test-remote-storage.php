<?php

/**
 * @group remote
 * @group storage
 */
class Test_Snapshot_Model_Full_Remote_Storage extends WP_UnitTestCase {

	public function test_purge_backups_cache() {
		$model = Snapshot_Model_Full_Remote_Storage::get();
		$test = 'whatever';

		Snapshot_Model_Transient::set(
			$model->get_filter( 'backups' ),
			$test
		);
		$result = Snapshot_Model_Transient::get( $model->get_filter( 'backups' ) );
		$this->assertFalse( empty( $result ) );
		$this->assertEquals( $test, $result );

		$model->purge_backups_cache();
		$result = Snapshot_Model_Transient::get( $model->get_filter( 'backups' ) );
		$this->assertTrue( empty( $result ) );
		$this->assertNotEquals( $test, $result );
	}

	public function test_should_rotate_on_upload() {
		$model = Snapshot_Model_Full_Remote_Storage::get();
		$this->assertFalse( $model->should_rotate_on_upload() );

		$fake_backups = 0;
		Snapshot_Model_Transient::set(
			$model->get_filter( 'backups' ),
			$fake_backups
		);
		$this->assertFalse( $model->should_rotate_on_upload() );
		$model->purge_backups_cache();

		$fake_backups = 'Whatever!';
		Snapshot_Model_Transient::set(
			$model->get_filter( 'backups' ),
			$fake_backups
		);
		$this->assertFalse( $model->should_rotate_on_upload() );
		$model->purge_backups_cache();

		$fake_backups = array(
			array( 'name' => 'full1' ),
			array( 'name' => 'full2' ),
			array( 'name' => 'full3' ),
			array( 'name' => 'full4' ),
		);
		Snapshot_Model_Transient::set(
			$model->get_filter( 'backups' ),
			$fake_backups
		);
		$this->assertTrue( $model->should_rotate_on_upload() );
		$model->purge_backups_cache();

		$fake_backups = array(
			array( 'name' => 'full_backup-1312-automated-1.zip' ),
			array( 'name' => 'full_backup-1312-automated-2.zip' ),
			array( 'name' => 'full_backup-1312-automated-3.zip' ),
			array( 'name' => 'full_backup-1312-automated-4.zip' ),
		);
		Snapshot_Model_Transient::set(
			$model->get_filter( 'backups' ),
			$fake_backups
		);
		$this->assertTrue( $model->should_rotate_on_upload() );
		$model->purge_backups_cache();
	}

	public function test_has_enough_space_for() {
		$model = Snapshot_Model_Full_Remote_Storage::get();
		$this->assertFalse( $model->has_enough_space_for( false ) );

		$this->assertFalse( $model->has_enough_space_for( __FILE__ ) );

		add_filter( $model->get_filter( 'api_space_free' ), '__return_true' );
		$this->assertFalse( $model->has_enough_space_for( __FILE__ ) );
		remove_filter( $model->get_filter( 'api_space_free' ), '__return_true' );
	}

	public function test_get_initialized_download() {
		$model = Snapshot_Model_Full_Remote_Storage::get();
		$transfer = $model->get_initialized_download( 'test' );

		$this->assertTrue( is_object( $transfer ) );
		$this->assertTrue(
			$transfer instanceof Snapshot_Model_Transfer_Download
		);
	}

	public function test_get_initialized_upload() {
		$model = Snapshot_Model_Full_Remote_Storage::get();
		$transfer = $model->get_initialized_upload( 'test' );

		$this->assertTrue( is_object( $transfer ) );
		$this->assertTrue(
			$transfer instanceof Snapshot_Model_Transfer_Upload
		);
	}
}
