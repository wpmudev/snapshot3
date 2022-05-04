<?php

/**
 * @group model
 * @group transfer
 */
class Test_Model_Transfer_Part extends WP_UnitTestCase {

	public function test_exists() {
		$this->assertTrue(
			class_exists( 'Snapshot_Model_Transfer_Part' )
		);
	}

	public function test_get_index() {
		foreach ( range( 1, 1312, 161 ) as $idx ) {
			$part = new Snapshot_Model_Transfer_Part( $idx );
			$index = $part->get_index();

			$this->assertTrue( is_numeric( $index ) );
			$this->assertEquals(
				$idx, (int) $index
			);
		}
	}

	public function test_get_part_number() {
		foreach ( range( 1, 1312, 161 ) as $idx ) {
			$part = new Snapshot_Model_Transfer_Part( $idx );
			$pn = $part->get_part_number();

			$this->assertTrue( is_numeric( $pn ) );
			$this->assertEquals(
				$idx + 1, (int) $pn
			);
		}
	}

	public function test_get_set_seek() {
		$model = new Snapshot_Model_Transfer_Part( 0 );

		$this->assertEquals(
			0,
			$model->get_seek()
		);

		foreach ( range( 1, 1312, 161 ) as $idx ) {
			$model->set_seek( $idx );

			$this->assertEquals(
				$idx,
				$model->get_seek()
			);
		}
	}

	public function test_get_set_length() {
		$model = new Snapshot_Model_Transfer_Part( 0 );

		$this->assertEquals(
			0,
			$model->get_length()
		);

		foreach ( range( 1, 1312, 161 ) as $idx ) {
			$model->set_length( $idx );

			$this->assertEquals(
				$idx,
				$model->get_length()
			);
		}
	}

	public function test_is_done() {
		$model = new Snapshot_Model_Transfer_Part( 0 );
		$this->assertFalse(
			$model->is_done(),
			'Part is not done by default'
		);

		$model = new Snapshot_Model_Transfer_Part( 0, true );
		$this->assertTrue(
			$model->is_done(),
			'Part is done'
		);
	}

	public function test_complete() {
		$model = new Snapshot_Model_Transfer_Part( 0 );
		$this->assertFalse(
			$model->is_done(),
			'Part is not done by default'
		);

		$model->complete();
		$this->assertTrue(
			$model->is_done(),
			'Completed part is done'
		);
	}

	public function test_from_data() {
		$model = new Snapshot_Model_Transfer_Part( 161, true );
		$data = $model->get_data();

		$model2 = Snapshot_Model_Transfer_Part::from_data( $data );

		$this->assertTrue(
			$model2 instanceof Snapshot_Model_Transfer_Part
		);
		$this->assertEquals(
			$model->get_index(), $model2->get_index()
		);
		$this->assertEquals(
			$model->get_seek(), $model2->get_seek()
		);
		$this->assertEquals(
			$model->get_length(), $model2->get_length()
		);
		$this->assertEquals(
			$model->is_done(), $model2->is_done()
		);
	}

	public function test_get_data() {
		$model = new Snapshot_Model_Transfer_Part( 161, true );
		$data = $model->get_data();

		$this->assertTrue( is_array( $data ) );

		$this->assertTrue( array_key_exists( 'index', $data ) );
		$this->assertEquals( 161, $data['index'] );

		$this->assertTrue( array_key_exists( 'is_done', $data ) );
		$this->assertEquals( true, $data['is_done'] );
	}
}
