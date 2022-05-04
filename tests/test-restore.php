<?php

/**
 * @group restore
 */
class RestoreTest extends WP_UnitTestCase {

	private $_backup_idx = 'themes';
	private $_backup;

	public function setUp () {
		$idx = $this->_backup_idx;
		$backup = new Snapshot_Helper_Backup;
		$files = new Snapshot_Model_Queue_Fileset($idx);
		$files->clear();
		$files->add_source($idx);

		$tables = new Snapshot_Model_Queue_Tableset($idx);
		$tables->clear();
		$all_tables = Snapshot_Helper_Utility::get_database_tables();

		if (!empty($all_tables['wp'])) foreach ($all_tables['wp'] as $tbl) {
			$tables->add_source($tbl);
		}

		$backup->create($idx);
		$backup->add_queue($files);
		$backup->add_queue($tables);

		while(!$backup->is_done()) {
			$status = $backup->process_files();
		}
		$backup->clear();

		$this->_backup = $backup;
	}

	public function tearDown () {
		unlink($this->_backup->get_archive_path($this->_backup_idx));
		rmdir($this->_backup->get_path($this->_backup_idx));
	}

	public function test_load () {
		$idx = $this->_backup_idx;
		$path = $this->_backup->get_archive_path($idx);

		$restore = Snapshot_Helper_Restore::from($path);
		$this->assertInstanceOf('Snapshot_Helper_Restore', $restore);
		$this->assertEquals(wp_normalize_path(realpath($path)), $restore->get_archive_path());

		$m1 = $this->_backup->get_manifest();
		$this->assertInstanceOf('Snapshot_Model_Manifest', $m1);

		$m2 = $restore->get_manifest();
		$this->assertInstanceOf('Snapshot_Model_Manifest', $m2);
		$this->assertEquals($m1->get_all(), $m2->get_all());
	}

	public function test_spawn_queues () {
		$idx = $this->_backup_idx;
		$path = $this->_backup->get_archive_path($idx);

		$restore = Snapshot_Helper_Restore::from($path);
		$queues = $restore->get_queues();

		$this->assertEquals(2, count($queues));
		foreach ($queues as $queue) {
			$this->assertInstanceOf('Snapshot_Model_Queue', $queue);
		}

	}

	public function test_restore_files () {
		$idx = $this->_backup_idx;
		$archive_path = $this->_backup->get_archive_path($idx);
		$restore_path = trailingslashit(wp_normalize_path(sys_get_temp_dir())) . 'restore';

		if (file_exists($restore_path)) Snapshot_Helper_Utility::recursive_rmdir($restore_path);
		mkdir($restore_path);

		$restore = Snapshot_Helper_Restore::from($archive_path);
		$restore->to($restore_path);
		
		while (!$restore->is_done()) {
			$result = $restore->process_files();
			$this->assertTrue($result['status']);
		}

		$restore->clear();
		$this->assertFileExists($restore_path);

		Snapshot_Helper_Utility::recursive_rmdir($restore_path);
	}

	public function test_restore_tables () {
		$idx = $this->_backup_idx;
		$archive_path = $this->_backup->get_archive_path($idx);
		$restore_path = trailingslashit(wp_normalize_path(sys_get_temp_dir())) . 'restore';

		if (file_exists($restore_path)) Snapshot_Helper_Utility::recursive_rmdir($restore_path);
		mkdir($restore_path);

		$restore = Snapshot_Helper_Restore::from($archive_path);
		$restore->to($restore_path);

		$db_test_key = 'test_test_test';
		$db_test_value = 'DB TEST VALUE!!';
		
		update_option($db_test_key, $db_test_value, false);
		$option = get_option($db_test_key);
		$this->assertEquals(0, strcmp($option, $db_test_value));

		wp_cache_delete($db_test_key, 'options');
		
		while (!$restore->is_done()) {
			$result = $restore->process_files();
			$this->assertTrue($result['status']);
		}

		$restore->clear();
		$this->assertFileExists($restore_path);

		$option = get_option($db_test_key);
		$this->assertNotEquals(0, strcmp($option, $db_test_value));

		Snapshot_Helper_Utility::recursive_rmdir($restore_path);
	}

}