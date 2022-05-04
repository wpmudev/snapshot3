<?php

/**
 * @group snapshot
 * @group destination
 */
class SnapshotDestinationPurgeTest extends WP_UnitTestCase {

	public function test_has_destination_factory () {
		$this->assertTrue(
			class_exists('Snapshot_Model_Destination_Factory')
		);
	}

	public function test_has_destinations () {
		$expected = array('google-drive', 'aws', 'dropbox', 'ftp');
		$actual = array_keys(WPMUDEVSnapshot::instance()->get_setting('destinationClasses'));
		foreach ($expected as $key) {
			if ('dropbox' === $key && version_compare(PHP_VERSION, '5.4.0') < 0) continue;
			if ('aws' === $key && version_compare(PHP_VERSION, '5.5.0') < 0) continue;
			$this->assertTrue(in_array($key, $actual));
		}
	}

	public function test_destination_spawn () {
		$destinations = array('google-drive', 'aws', 'dropbox', 'ftp');
		$snapshot = WPMUDEVSnapshot::instance();
		foreach ($destinations as $key) {
			if ('dropbox' === $key && version_compare(PHP_VERSION, '5.4.0') < 0) continue;
			$destination = Snapshot_Model_Destination_Factory::get_destination($key, false);
			$this->assertFalse($destination, "Non-activated destination {$key} getting is always fake");

			if ('aws' === $key && version_compare(PHP_VERSION, '5.5.0') < 0) continue;
			$snapshot->config_data['destinations'][$key] = array('type' => $key);
			$destination = Snapshot_Model_Destination_Factory::get_destination($key, false);
			$this->assertTrue(
				$destination instanceof Snapshot_Model_Destination,
				"Activated destination {$key} getting returns valid destination instance"
			);

			$this->check_destination($destination);
			$snapshot->config_data['destinations'][$key] = false; // Clean up
		}
	}

	public function test_destination_spawn_from_item () {
		$destinations = array('google-drive', 'aws', 'dropbox', 'ftp');
		$snapshot = WPMUDEVSnapshot::instance();
		foreach ($destinations as $key) {
			if ('dropbox' === $key && version_compare(PHP_VERSION, '5.4.0') < 0) continue;
			$item = array(
				'destination' => $key
			);
			$destination = Snapshot_Model_Destination_Factory::from_item($item, false);
			$this->assertFalse($destination, "Non-activated destination {$key} getting is always fake");

			if ('aws' === $key && version_compare(PHP_VERSION, '5.5.0') < 0) continue;
			$snapshot->config_data['destinations'][$key] = array('type' => $key);
			$destination = Snapshot_Model_Destination_Factory::from_item($item, false);
			$this->assertTrue(
				$destination instanceof Snapshot_Model_Destination,
				"Activated destination {$key} getting returns valid destination instance"
			);

			$this->check_destination($destination);
			$snapshot->config_data['destinations'][$key] = false; // Clean up
		}
	}

	public function check_destination ($destination) {
		$this->assertTrue(
			is_callable(array($destination, 'handle_exception')),
			"Destination is able to handle exceptions uniformly"
		);
		$this->assertTrue(
			is_callable(array($destination, 'set_up_destination')),
			"Destination is able to be set up uniformly"
		);
		$this->assertTrue(
			is_callable(array($destination, 'purge_remote_items')),
			"Destination is able to purge remote items"
		);
		$this->assertTrue(
			is_callable(array($destination, 'list_remote_items')),
			"Destination is able to fetch remote items list"
		);
		$this->assertTrue(
			is_callable(array($destination, 'get_prepared_items')),
			"Destination is able to get a list of internally represented remote items"
		);
		$this->assertTrue(
			is_callable(array($destination, 'remove_file')),
			"Destination is able to remove remote files"
		);
	}
}

