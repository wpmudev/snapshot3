<?php

/**
 * @group model
 * @group transfer
 */
class Test_Model_Transfer_Upload extends WP_UnitTestCase {

	public function test_exists() {
		$this->assertTrue(
			class_exists( 'Snapshot_Model_Transfer_Upload' )
		);
		$model = new Snapshot_Model_Transfer_Upload;
		$this->assertTrue(
			$model instanceof Snapshot_Model_Transfer
		);
	}

	public function test_get_payload_invalid() {
		$part = new Snapshot_Model_Transfer_Part( 0 );
		$part->set_length( 161 );

		$model = new Snapshot_Model_Transfer_Upload;
		$body = $model->get_payload( $part );

		$this->assertTrue( is_string( $body ) );
		$this->assertTrue( empty( $body ) );
	}

	public function test_get_payload_valid() {
		$file = tempnam( sys_get_temp_dir(), 'snapshot-payload' );
		$length = 1312;
		$content = str_repeat( 'a', $length );
		file_put_contents( $file, $content );

		$part = new Snapshot_Model_Transfer_Part( 0 );
		$part->set_length( $length );

		$model = new Snapshot_Model_Transfer_Upload( $file );
		$body = $model->get_payload( $part );

		$this->assertTrue( is_string( $body ) );
		$this->assertFalse( empty( $body ) );

		$this->assertEquals(
			$content, $body
		);

		foreach ( range( 1, $length - 1, 10 ) as $idx ) {
			$cnt = substr( $content, $idx );
			$part->set_seek( $idx );
			$part->set_length( $length - $idx );

			$body = $model->get_payload( $part );
			$this->assertEquals(
				$cnt, $body
			);
		}

		unlink( $file );
	}

	public function test_initialize_done() {
		$model = new Snapshot_Model_Transfer_Upload;

		$this->assertFalse( $model->is_initialized() );
		$this->assertTrue( $model->is_done() );

		$model->initialize( 'test' );
		$this->assertFalse( $model->is_initialized() );
		$this->assertTrue( $model->is_done() );

		$parts = $model->get_transfer_parts( 100, 10 );
		$model->set_parts( $parts );
		$this->assertFalse( $model->is_done() );
		$this->assertTrue( $model->is_initialized() );

		foreach( $parts as $idx => $part ) {
			$model->complete_part( $idx );

			if ( $idx < count( $parts ) - 1 ) {
				$this->assertFalse( $model->is_done() );
			}
		}
		$this->assertTrue( $model->is_done() );
	}
}
