<?php

/**
 * @group model
 * @group filesystem
 */
class BflisterTest extends WP_UnitTestCase {

	public function test_initial_state() {
		$model = new Snapshot_Model_Bflister( new Snapshot_Model_Storage_Sitemeta( 'test' ) );
		$this->assertTrue(
			$model->get_current_step() >= 1
		);
		$this->assertTrue(
			$model->get_total_steps() >= 1
		);
	}

	public function test_is_done_initial() {
		$model = new Snapshot_Model_Bflister( new Snapshot_Model_Storage_Sitemeta( uniqid( 'test' ) ) );
		$model->reset();

		$this->assertFalse( $model->is_done() );

		return $model;
	}

	/**
	 * @depends test_is_done_initial
	 */
	public function test_one_path_list( $model ) {
		add_filter( 'snapshot_model_bflister_paths_limit', array( $this, 'return_one' ) );

		$files = $model->get_files();
		$this->assertTrue( is_array( $files ) );
		$this->assertFalse( empty( $files ) );

		$this->assertEquals( 1, $model->get_current_step() );
		$this->assertFalse( $model->is_done() );

		remove_filter( 'snapshot_model_bflister_paths_limit', array( $this, 'return_one' ) );
		return $model;
	}

	/**
	 * @depends test_one_path_list
	 */
	public function test_second_pass( $model ) {
		$step = 1;
		$files = $model->get_files();
		$total = count( $files );

		while ( ! $model->is_done() ) {
			$step++;

			$files = $model->process_files();
			$this->assertTrue( is_array( $files ) );
			$this->assertFalse( empty( $files ) );

			$this->assertEquals( $step, $model->get_current_step() );

			$total += count( $files );
		}

		$this->assertTrue( $total > 3000 );

		return $model;
	}

	/**
	 * @depends test_second_pass
	 */
	public function test_final_done( $model ) {
		$this->assertTrue($model->is_done());
		$this->assertTrue($model->get_total_steps() > 1);
	}

	public function return_one() {
		return 1;
	}
}
