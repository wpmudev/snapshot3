<?php

/**
 * @group snapshot
 * @group destination
 */
class SnapshotItemPathExpasionTest extends WP_UnitTestCase {

	public function test_item_path_expansion () {
		$snapshot = WPMUDEVSnapshot::instance();
		$home_path = apply_filters( 'snapshot_home_path', get_home_path() );

		$data_item = array();
		$root_dir = 'root-test';
		$destinations = array(
			'aws',
			'dropbox',
			'ftp',
			'google-drive',
			'local',
		);
		foreach ($destinations as $destination) {
			$prefix = 'local' === $destination
				? trailingslashit($home_path)
				: ''
			;
			$snapshot->config_data['destinations'][$destination] = array(
				'directory' => $root_dir,
				'type' => $destination,
			);
			$param = array(
				'destination' => $destination,
				'timestamp' => 1,
			);
			$path = $snapshot->snapshot_get_item_destination_path($param, $data_item);
			$this->assertEquals(null, $path, "Non-overridden item path in any of the params is NULL");

			$expected = 'google-drive' === $destination
				? $prefix . $root_dir
				: $prefix . 'test'
			;
			$path = $snapshot->snapshot_get_item_destination_path(array_merge(
				$param,
				array('destination-directory' => 'test')
			),	$data_item);
			$this->assertEquals($expected, $path, "Overridden item path first param without expansion for {$destination} [{$expected}]");

			$expected = 'google-drive' === $destination
				? $prefix . $root_dir
				: $prefix . 'test-item'
			;
			$path = $snapshot->snapshot_get_item_destination_path($param, array_merge(
				$data_item,
				array('destination-directory' => 'test-item')
			));
			$this->assertEquals($expected, $path, "Overridden item path second param without expansion for {$destination}");

			$expected = 'google-drive' === $destination
				? $prefix . $root_dir
				: $prefix . 'test-item'
			;
			$path = $snapshot->snapshot_get_item_destination_path(
				array_merge($param, array('destination-directory' => 'test')),
				array_merge($data_item, array('destination-directory' => 'test-item'))
			);
			$this->assertEquals($expected, $path, "Second param override takes precedence for {$destination}");
		}
	}

	public function test_item_path_macro_expansion () {
		$snapshot = WPMUDEVSnapshot::instance();
		$home_path = apply_filters( 'snapshot_home_path', get_home_path() );

		$root_path = 'root-test';
		$timestamp = '1312';

		$data_item = array();
		$destinations = array(
			'aws',
			'dropbox',
			'ftp',
			'google-drive',
			'local',
		);
		foreach ($destinations as $destination) {
			$prefix = 'local' === $destination
				? trailingslashit($home_path)
				: ''
			;
			$snapshot->config_data['destinations'][$destination] = array(
				'directory' => $root_path,
				'type' => $destination,
			);
			$param = array(
				'destination' => $destination,
				'timestamp' => $timestamp,
			);

			$expected_root_relpath = 'local' === $destination
				? 'wp-content/uploads/snapshots'
				: $root_path
			;
			$path = $snapshot->snapshot_get_item_destination_path($param, array_merge(
				$data_item,
				array('destination-directory' => '[DEST_PATH]')
			));
			$this->assertEquals($prefix . $expected_root_relpath, $path, "Destination path macro properly expands for {$destination}");

			$domain = 'google-drive' !== $destination
				? 'example.org'
				: $root_path
			;
			$path = $snapshot->snapshot_get_item_destination_path($param, array_merge(
				$data_item,
				array('destination-directory' => '[SITE_DOMAIN]')
			));
			$this->assertEquals($prefix . $domain, $path, "Domain path macro properly expands for {$destination}");

			$snapshot_id = 'google-drive' !== $destination
				? $timestamp
				: $root_path
			;
			$path = $snapshot->snapshot_get_item_destination_path($param, array_merge(
				$data_item,
				array('destination-directory' => '[SNAPSHOT_ID]')
			));
			$this->assertEquals($prefix . $snapshot_id, $path, "ID path macro properly expands for {$destination}");
		}

	}
}
