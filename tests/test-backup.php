<?php

/**
 * @group backup
 */
class BackupTest extends WP_UnitTestCase {

	private $_backup_idx = 123;

	public function test_get_path () {
		$backup = new Snapshot_Helper_Backup;
		$path = $backup->get_path($this->_backup_idx);

		$path_target = preg_quote(Snapshot_Helper_String::conceal($this->_backup_idx), '/');

		$this->assertNotEmpty($path);
		$this->assertRegExp("/wordpress\/wp-content\/uploads\/snapshots\/_backup\/{$path_target}/", $path);
	}

	public function test_create () {
		$backup = new Snapshot_Helper_Backup;

		$status = $backup->create('!!!');
		$this->assertFalse($status, "Invalid path argument not rejected");
		$this->assertEquals(1, count($backup->errors()), "Source test error count mismatch");

		$this->assertFileNotExists($backup->get_path($this->_backup_idx), "Backup path already exists");

		$status = $backup->create($this->_backup_idx);
		$this->assertTrue($status, "Backup not created");
		$this->assertFileExists($backup->get_path($this->_backup_idx), "Directory not created");

		rmdir($backup->get_path($this->_backup_idx));
	}

	public function test_files_queue () {
		$backup = new Snapshot_Helper_Backup;

		$status = $backup->create($this->_backup_idx);
		$root = $backup->get_path($this->_backup_idx);
		$this->assertTrue($status, "Backup not created");
		$this->assertFileExists($root, "Directory not created");

		$files = new Snapshot_Model_Queue_Fileset($this->_backup_idx);
		$files->clear();
		$files->add_source('themes');

		$status = $backup->add_queue($files);
		$this->assertTrue($status, "Queue was not added");

		$done = $backup->is_done();
		$this->assertFalse($done, "Backup was unexpectedly done");

		$status = $backup->process_files();
		$this->assertTrue($status, "Files could not be processed");

//		$done = $backup->is_done();
//		$this->assertTrue($done, "Backup was not done");

		unlink($backup->get_archive_path($this->_backup_idx));
		rmdir($backup->get_path($this->_backup_idx));
	}

}
