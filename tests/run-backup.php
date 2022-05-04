<?php
/**
 * @group backup-run
 */
class BackupRunTest  extends WP_UnitTestCase {

	public function test_create_themes_backup () { $this->_process_file('themes'); }
	public function test_create_full_backup () { $this->_process_file('full'); }
	public function test_create_plugins_backup () { $this->_process_file('plugins'); }
	public function test_create_muplugins_backup () { $this->_process_file('mu-plugins'); }
	public function test_create_config_backup () { $this->_process_file('config'); }
	public function test_create_htaccess_backup () { $this->_process_file('htaccess'); }
	public function test_create_themes_exclusion_backup () {
		$config = WPMUDEVSnapshot::instance()->config_data['config'];
		$exclusion = !empty($config['filesIgnore']) ? $config['filesIgnore'] : array();
		$exclusion[] = 'twentythirteen';
		WPMUDEVSnapshot::instance()->config_data['config']['filesIgnore'] = $exclusion;

		$this->_process_file('themes');
	}

	protected function _process_file ($idx) {
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

		$status = $backup->create($idx);
		$this->assertTrue($status);

		$status = $backup->add_queue($files);
		$this->assertTrue($status);

		$status = $backup->add_queue($tables);
		$this->assertTrue($status);

		$this->_out("\n----------\nCreate {$idx} backup");
		
		$count = 0;		
		while(!$backup->is_done()) {
			$status = $backup->process_files();
			$this->_out('Processing files, batch ' . $count . ': ' . ($status ? 'OK' : 'Error'));
			$count++;
		}
		$backup->clear();
		
		$this->_out("Backup {$idx} done!\n----------");

		$this->assertFileExists($backup->get_archive_path($idx));
		$this->assertTrue($status);

		unlink($backup->get_archive_path($idx));

		rmdir($backup->get_path($idx));
	}

	private function _out ($msg) {
		echo "{$msg}\n";
		ob_flush();
	}


}