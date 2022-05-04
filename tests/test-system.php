<?php

/**
 * @group backup
 * @group system
 */
class SystemTest extends WP_UnitTestCase {

	private $_backup_idx = 123;

	public function test_is_available () {
		$this->assertTrue(
			Snapshot_Helper_System::is_available('exec'),
			"Exec should be available for this to run"
		);
		$this->assertFalse(
			Snapshot_Helper_System::is_available('test_this_should_not_be_here'),
			"Fake call fails"
		);
	}

	public function test_system_helper () {
		$this->assertTrue(
			Snapshot_Helper_System::has_command('type'),
			"We have type command"
		);

		$this->assertTrue(
			!!preg_match(
				'/.*type$/',
				Snapshot_Helper_System::get_command('type')
			),
			"We have type path"
		);
	}

	public function test_helper_db_extraction () {
		$connections = array(
			'localhost' => array('host' => 'localhost', 'port' => 3306),
			'localhost:3306' => array('host' => 'localhost', 'port' => 3306),
			'localhost:22' => array('host' => 'localhost', 'port' => 22),
			'127.0.0.1' => array('host' => '127.0.0.1', 'port' => 3306),
			'127.0.0.1:3306' => array('host' => '127.0.0.1', 'port' => 3306),
			'127.0.0.1:161' => array('host' => '127.0.0.1', 'port' => 161),
			'host:name' => array('host' => 'host', 'port' => 3306),
			'host:name:77' => array('host' => 'host:name', 'port' => 77),
		);
		foreach ($connections as $src => $exp) {
			$this->assertSame(
				Snapshot_Helper_System::get_db_host($src),
				$exp['host'],
				"Host {$src} extraction matches expected {$exp['host']}"
			);
			$this->assertSame(
				Snapshot_Helper_System::get_db_port($src),
				$exp['port'],
				"Port {$src} extraction matches expected {$exp['port']}"
			);
		}
	}

	public function test_helper_connection_recognition () {
		$connections = array(
			'localhost' => false,
			'localhost:3306' => false,
			'localhost:22' => false,
			'127.0.0.1' => false,
			'127.0.0.1:3306' => false,
			'127.0.0,1:161' => false,
			'host:name' => false, // Not a path?
			'host:name:161' => false,
			'host:/var/run/mysqld/mysqld.sock' => false,
			':/var/run/mysqld/mysqld.sock' => true,
			'127.0.0.1:/var/run/mysqld/mysqld.sock' => true,
			'localhost:/var/run/mysqld/mysqld.sock' => true,
		);
		foreach ($connections as $con => $expected) {
			$this->assertSame(
				Snapshot_Helper_System::is_socket_connection($con),
				$expected,
				"Connection {$con} matched expectations"
			);
		}
	}

	public function test_supports_system_backup () {
		$idx = 'full';
		$backup = new Snapshot_Helper_Backup;

		return false; // BB pipeline tests do not allow for system backup
		$this->assertTrue(
			$backup->supports_system_backup(),
			"We have system backup supported"
		);

	}

	public function test_process_archive_system () {
		if (!defined('SNAPSHOT_ATTEMPT_SYSTEM_BACKUP')) {
			define('SNAPSHOT_ATTEMPT_SYSTEM_BACKUP', true);
		}
		if (!SNAPSHOT_ATTEMPT_SYSTEM_BACKUP) $this->fail("Can't test system backup");

		$idx = 'full';
		$backup = new Snapshot_Helper_Backup;

		if (!$backup->supports_system_backup()) return false; // Don't bother if we're not testing system (BB pipeline)

		$files = new Snapshot_Model_Queue_Fileset($idx);
		$files->clear();
		$files->add_source($idx);

		// Use the factory method to access all tables
		$tables = Snapshot_Model_Queue_Tableset::all($idx);

		$status = $backup->create($idx);
		$status = $backup->add_queue($files);
		$status = $backup->add_queue($tables);

		$archive = $backup->get_archive_path($idx);

		$this->assertFalse(
			file_exists($archive),
			"Archive not previously here"
		);
		$this->assertTrue(
			$backup->process_files(),
			"Files processing went fine"
		);
		$this->assertTrue(
			file_exists($archive),
			"Archive created"
		);
		$backup->save();

		$files_backup_size = filesize($archive);

		$this->assertTrue(
			$backup->process_files(),
			"Tables processing went fine"
		);
		clearstatcache(true, $archive);
		$this->assertTrue(
			(filesize($archive) > $files_backup_size),
			"Archive filesize increased with tables backup"
		);
		$backup->save();

		$this->assertTrue(
			$backup->is_done(),
			"We're done with the processing!"
		);

		$backup->clear();
		$this->assertTrue(
			$backup->postprocess(),
			"Postprocessing successful!"
		);

		$destination = $backup->get_destination_path();
		$this->assertTrue(
			file_exists($destination),
			"File properly moved"
		);

		@unlink($destination);
	}

}

