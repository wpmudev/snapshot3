<?php

/**
 * @group model
 * @group transfer
 */
class Test_Model_Transfer_Download extends WP_UnitTestCase {

	public function test_exists() {
		$this->assertTrue(
			class_exists( 'Snapshot_Model_Transfer_Download' )
		);
		$model = new Snapshot_Model_Transfer_Download;
		$this->assertTrue(
			$model instanceof Snapshot_Model_Transfer
		);
	}

	public function test_get_type() {
		$model = new Snapshot_Model_Transfer_Download;
		$this->assertEquals(
			Snapshot_Model_Transfer::TYPE_DOWNLOAD,
			$model->get_type()
		);
	}

	public function test_get_increment_default() {
		$model = new Snapshot_Model_Transfer_Download;

		$this->assertEquals(
			50 * 1024 * 1024,
			$model->get_increment(),
			'Default increment is 50Mb'
		);
	}

	public function test_get_set_transfer_id() {
		$model = new Snapshot_Model_Transfer_Download;
		$this->assertEquals( '', $model->get_transfer_id() );

		foreach ( range( 1, 1312, 161 ) as $test ) {
			$tid = rand();
			$this->assertNotEquals( $tid, $model->get_transfer_id() );
			$model->set_transfer_id( $tid );
			$this->assertEquals( $tid, $model->get_transfer_id() );
		}
	}

	public function test_get_transfer_parts() {
		$model = new Snapshot_Model_Transfer_Download;

		$parts = $model->get_transfer_parts( 0 );
		$this->assertTrue( is_array( $parts ) );
		$this->assertTrue( empty( $parts ) );
	}

	public function test_has_next_part() {
		$model = new Snapshot_Model_Transfer_Download;
		$parts = $model->get_transfer_parts(
			13 * $model->get_increment()
		);
		$model->set_parts( $parts );
		$model->save();

		$this->assertFalse( $model->has_completed_parts() );
		$model->complete_part( 0 );
		$this->assertTrue( $model->has_completed_parts() );
	}

	public function test_get_increment_param() {
		$model = new Snapshot_Model_Transfer_Download;

		$this->assertEquals(
			50 * 1024 * 1024,
			$model->get_increment( 'dfsdfasdfadf' ),
			'Invalid param falls back to default increment'
		);
		$this->assertEquals(
			10,
			$model->get_increment( 10 ),
			'Valid param sets increment'
		);
	}

	public function test_get_increment_filtering() {
		$model = new Snapshot_Model_Transfer_Download;

		add_filter(
			'snapshot_model_transfer_part_size_increment',
			array( $this, 'return_number' )
		);
		$this->assertEquals(
			$this->return_number(),
			$model->get_increment(),
			'Default filtered increment is filtered'
		);
		$this->assertEquals(
			$this->return_number(),
			$model->get_increment( 10 ),
			'Param filtered increment is filtered'
		);
		remove_filter(
			'snapshot_model_transfer_part_size_increment',
			array( $this, 'return_number' )
		);
	}

	public function test_transfer_parts_round() {
		$model = new Snapshot_Model_Transfer_Download;
		$parts = $model->get_transfer_parts(
			13 * $model->get_increment()
		);

		$this->assertTrue( is_array( $parts ) );
		$this->assertEquals( 13, count( $parts ) );
		foreach ( $parts as $idx => $part ) {
			$this->assertTrue(
				$part instanceof Snapshot_Model_Transfer_Part
			);
			$this->assertEquals(
				$idx,
				$part->get_index()
			);
		}
	}

	public function test_transfer_parts_odd() {
		$model = new Snapshot_Model_Transfer_Download;
		$parts = $model->get_transfer_parts(
			13 * $model->get_increment() + 12
		);

		$this->assertTrue( is_array( $parts ) );
		$this->assertEquals( 14, count( $parts ) );
		foreach ( $parts as $idx => $part ) {
			$this->assertTrue(
				$part instanceof Snapshot_Model_Transfer_Part
			);
			$this->assertEquals(
				$idx,
				$part->get_index()
			);
		}
	}

	public function test_persistence() {
		$model = new Snapshot_Model_Transfer_Download( 'test' );
		$parts = $model->get_transfer_parts(
			13 * $model->get_increment()
		);
		$model->set_parts( $parts );
		$model->save();

		unset( $model );
		$model2 = new Snapshot_Model_Transfer_Download( 'test' );
		foreach ( $model2->get_parts() as $part ) {
			$this->assertTrue(
				$part instanceof Snapshot_Model_Transfer_Part
			);
			$this->assertFalse(
				empty( $parts[ $part->get_index() ] )
			);
			$this->assertEquals(
				$parts[ $part->get_index() ]->get_index(),
				$part->get_index()
			);
			$this->assertEquals(
				$parts[ $part->get_index() ]->is_done(),
				$part->is_done()
			);
		}
	}
	public function test_completion_next() {
		$model = new Snapshot_Model_Transfer_Download( 'test' );
		$parts = $model->get_transfer_parts(
			13 * $model->get_increment()
		);
		$model->set_parts( $parts );

		$first = $model->get_next_part();
		$this->assertTrue(
			$first instanceof Snapshot_Model_Transfer_Part
		);
		$this->assertEquals( 0, $first->get_index() );
		$this->assertFalse( $first->is_done() );

		$model->complete_part( 0 );

		$idx = 1;
		while ( $model->has_next_part() ) {
			$next = $model->get_next_part();
			$this->assertTrue(
				$next instanceof Snapshot_Model_Transfer_Part
			);
			$this->assertEquals( $idx, $next->get_index() );
			$this->assertFalse( $next->is_done() );
			$model->complete_part( $idx );

			$idx++;
		}
	}

	public function test_get_part_file_path() {
		$model = new Snapshot_Model_Transfer_Download;
		$part = new Snapshot_Model_Transfer_Part( 1312 );
		$path = $model->get_part_file_path( $part );

		$this->assertTrue( is_string( $path ) );
		$this->assertTrue( empty( $path ) );

		$model = new Snapshot_Model_Transfer_Download( 'test' );
		foreach ( range( 1, 1312, 161 ) as $idx ) {
			$part = new Snapshot_Model_Transfer_Part( rand() );
			$path = $model->get_part_file_path( $part );

			$this->assertTrue( is_string( $path ) );
			$this->assertFalse( empty( $path ) );

			$this->assertTrue(
				! ! preg_match( '/^' . $part->get_index() . '---/', basename( $path ) )
			);
		}
	}

	public function test_stitch_temporary_files() {
		$tempfile = tempnam( sys_get_temp_dir(), 'stitching' );
		file_put_contents( $tempfile, '' );
		$this->assertTrue( file_exists( $tempfile ) );

		$model = new Snapshot_Model_Transfer_Download( $tempfile );
		$parts = $model->get_transfer_parts( 1312, 10 );
		$model->set_parts( $parts );
		$test_string = 'test';

		$this->assertFalse( $model->stitch_temporary_files() );

		foreach ( $model->get_parts() as $part ) {
			file_put_contents(
				$model->get_part_file_path( $part ),
				$test_string
			);
		}

		$this->assertTrue( is_readable( $tempfile ) );
		$this->assertTrue( $model->stitch_temporary_files() );

		foreach ( $model->get_parts() as $part ) {
			$this->assertFalse(
				file_exists( $model->get_part_file_path( $part ) ),
				'Merged file cleaned up'
			);
		}
		$this->assertTrue( file_exists( $tempfile ) );
		$this->assertTrue( is_readable( $tempfile ) );

		$final_content = file_get_contents( $tempfile );
		$this->assertEquals(
			$final_content,
			str_repeat( $test_string, count( $model->get_parts() ) )
		);

		chmod( $tempfile, 000 );
		if ( ! is_readable( $tempfile ) ) {
			foreach ( $model->get_parts() as $part ) {
				file_put_contents(
					$model->get_part_file_path( $part ),
					$test_string
				);
			}
			$this->assertFalse( is_readable( $tempfile ) );
			$this->assertFalse( $model->stitch_temporary_files() );
			foreach ( $model->get_parts() as $part ) {
				unlink(
					$model->get_part_file_path( $part )
				);
			}
		}

		unlink( $tempfile );
	}

	public function test_complete() {
		$model = new Snapshot_Model_Transfer_Download( 'test' );

		$this->assertFalse( ! ! $model->get_transfer_id() );
		$this->assertFalse( $model->has_next_part() );

		$parts = $model->get_transfer_parts( 1312, 10 );
		$model->set_parts( $parts );
		$this->assertTrue( $model->has_next_part() );

		$model->set_transfer_id( 'test' );
		$this->assertTrue( ! ! $model->get_transfer_id() );

		$this->assertTrue( ! ! $model->get_transfer_id() );
		$this->assertTrue( $model->has_next_part() );

		$model->complete();
		$this->assertFalse( ! ! $model->get_transfer_id() );
		$this->assertFalse( $model->has_next_part() );
	}

	public function test_initialize() {
		$model = new Snapshot_Model_Transfer_Download( 'test' );
		$this->assertFalse( $model->is_initialized() );
		$this->assertEquals( 0, count( $model->get_parts() ) );

		$model->initialize( 50 * $model->get_increment() );
		$this->assertTrue( $model->is_initialized() );
		$this->assertEquals( 50, count( $model->get_parts() ) );
	}

	public function return_number() {
		return 1312;
	}
}
