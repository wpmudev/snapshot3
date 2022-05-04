<?php

/**
 * @group queue
 */
class QueueTest extends WP_UnitTestCase {

	private $_queue = 'test';

	public function test_fileset_create () {
		$queue = new Snapshot_Model_Queue_Fileset($this->_queue);
		$queue->clear();

		$queue->add_source('full');

		$set1 = $queue->get_files();
		$this->assertNotEmpty($set1);
		$this->assertEquals($queue->get_chunk_size(), count($set1));

		$set2 = $queue->get_files();
		$this->assertNotEmpty($set1);
		$this->assertEquals($queue->get_chunk_size(), count($set1));

		$this->assertNotEquals($set1, $set2);
	}

	public function test_threshold () {
		$default = 1024 * 1024 * 1024;
		$this->assertEquals(Snapshot_Model_Queue_Fileset::get_size_threshold(), $default, 'Default threshold should be 1Gb');

		if (!defined('SNAPSHOT_FILESET_LARGE_FILE_SIZE')) {
			$defined = 13 * 1024 * 1024;
			define('SNAPSHOT_FILESET_LARGE_FILE_SIZE', $defined);
			$this->assertEquals(Snapshot_Model_Queue_Fileset::get_size_threshold(), $defined, 'Defined threshold should be respected');
		}

		$filtered = $this->_filter_threshold_size();
		add_filter('snapshot_queue_fileset_filesize_threshold', array($this, '_filter_threshold_size'));
		$this->assertEquals(Snapshot_Model_Queue_Fileset::get_size_threshold(), $filtered, 'Filtered threshold should be respected');
	}

	public function _filter_threshold_size () { return 1024 * 1024 * 5; }

	public function test_fileset_is_done () {
		$queue = new Snapshot_Model_Queue_Fileset($this->_queue);
		$queue->clear();

		$queue->add_source('config');
		$queue->add_source('htaccess');

		$done = $queue->is_done();
		$this->assertFalse($done);

		$set1 = $queue->get_files();
		$this->assertEmpty($set1);

		$done = $queue->is_done();
		$this->assertFalse($done);

		$set2 = $queue->get_files();
		$this->assertEmpty($set2);

		$done = $queue->is_done();
		$this->assertTrue($done);
	}
/*
	public function test_table_create () {
		$queue = new Snapshot_Model_Queue_Tableset($this->_queue);
		$queue->clear();

		global $wpdb;
		$queue->add_source("{$wpdb->prefix}options");

		$queue->set_chunk_size(90); // More than 50% of test site's options count

		$set1 = $queue->get_files();
		$this->assertEmpty($set1);

		$set2 = $queue->get_files();
		$this->assertNotEmpty($set2);
		$this->assertEquals(1, count($set2));

		$this->assertFileExists($set2[0]);

		$queue->clear();
		$this->assertFileNotExists($set2[0]);
	}

	public function test_table_is_done () {
		$queue = new Snapshot_Model_Queue_Tableset($this->_queue);
		$queue->clear();

		global $wpdb;
		$queue->add_source("{$wpdb->prefix}options");
		$queue->add_source("{$wpdb->prefix}posts");

		$queue->set_chunk_size(90); // More than 50% of test site's options count

		$set1 = $queue->get_files();
		$this->assertEmpty($set1);

		$done = $queue->is_done();
		$this->assertFalse($done);

		$set2 = $queue->get_files();
		$this->assertNotEmpty($set2);
		$this->assertEquals(1, count($set2));

		$this->assertFileExists($set2[0]);

		$done = $queue->is_done();
		$this->assertFalse($done);

		$set3 = $queue->get_files();
		$this->assertNotEmpty($set3);
		$this->assertEquals(1, count($set3));

		$this->assertFileExists($set3[0]);

		$done = $queue->is_done();
		$this->assertTrue($done);

		$queue->clear();
		$this->assertFileNotExists($set2[0]);
		$this->assertFileNotExists($set3[0]);
	}
*/
}
