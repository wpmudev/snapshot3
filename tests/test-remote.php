<?php

/**
 * @group remote
 */
class RemoteTest extends WP_UnitTestCase {

	private $_remote;

	public function setUp () {
		if (!defined('WPMUDEV_CUSTOM_API_SERVER')) define('WPMUDEV_CUSTOM_API_SERVER', 'https://premium.wpmudev.dev');

		$this->_remote = new Snapshot_Model_Full_Remote;
		add_filter($this->_remote->get_filter('api_key'), array($this, 'get_api_key'));
		add_filter($this->_remote->get_filter('domain'), array($this, 'get_domain'));

		$this->_now = time();
	}

	public function tearDown () {
		remove_filter($this->_remote->get_filter('api_key'), array($this, 'get_api_key'));
		remove_filter($this->_remote->get_filter('domain'), array($this, 'get_domain'));
		$this->_remote->reset_api();
	}

	public function get_api_key () {
		return 'ffffffffffffffffffffffffffffffffffffffff'; // Substitute with a test API key of your own
	}

	public function get_domain () {
		return 'http://localhost/snapshot'; // Substitute with a test API domain of your own
	}

	public function test_rotation_strategy_scenario_zero () {
		if ( version_compare(PHP_VERSION, '5.5', '<') ) {
			// Do not execute on old PHPs.
			return true;
		}
		// No remote backups
		$drop = $this->_remote->get_backup_rotation_list('');
		$this->assertInternalType('array', $drop);
		$this->assertEmpty($drop);
	}
/*
	public function test_rotation_strategy_scenario_zero_a () {
		$current_cb = array($this, 'return_10_mb');
		$free_cb = array($this, 'return_100_mb');

		// Less than 3 remote backups, and room for one more
		add_filter($this->_remote->get_filter('current-backup-filesize'), $current_cb);
		add_filter($this->_remote->get_filter('api-space-free'), $free_cb);

		$drop = $this->_remote->get_backup_rotation_list('');
		$this->assertInternalType('array', $drop);
		$this->assertEmpty($drop);

		remove_filter($this->_remote->get_filter('current-backup-filesize'), $current_cb);
		remove_filter($this->_remote->get_filter('api-space-free'), $free_cb);
	}

	public function test_rotation_strategy_scenario_one () {
		$current_cb = array($this, 'return_10_mb');
		$backups_cb = array($this, 'return_raw_items_1');
		$total_cb = array($this, 'return_15_mb');

		add_filter($this->_remote->get_filter('current-backup-filesize'), $current_cb);
		add_filter($this->_remote->get_filter('backups-get'), $backups_cb);
		add_filter($this->_remote->get_filter('api-space-total'), $total_cb);

		// Scenario one - we have room for just one
		$drop = $this->_remote->get_backup_rotation_list('');
		$expected = $this->_raw_item();
		$this->assertInternalType('array', $drop);
		$this->assertNotEmpty($drop);
		$this->assertEquals($drop[0], $expected['name']);

		remove_filter($this->_remote->get_filter('current-backup-filesize'), $current_cb);
		remove_filter($this->_remote->get_filter('backups-get'), $backups_cb);
		remove_filter($this->_remote->get_filter('api-space-total'), $total_cb);
	}

	public function test_rotation_strategy_scenario_two_a () {
		$current_cb = array($this, 'return_10_mb');
		$backups_cb = array($this, 'return_raw_items_two_a');
		$total_cb = array($this, 'return_20_mb');

		add_filter($this->_remote->get_filter('current-backup-filesize'), $current_cb);
		add_filter($this->_remote->get_filter('backups-get'), $backups_cb);
		add_filter($this->_remote->get_filter('api-space-total'), $total_cb);

		// Scenario two - there's room for exactly two backups, with today's item being also remote
		$drop = $this->_remote->get_backup_rotation_list('');
		$expected = $this->_raw_item();
		$this->assertInternalType('array', $drop);
		$this->assertNotEmpty($drop);
		$this->assertEquals($drop[0], $expected['name']);

		remove_filter($this->_remote->get_filter('current-backup-filesize'), $current_cb);
		remove_filter($this->_remote->get_filter('backups-get'), $backups_cb);
		remove_filter($this->_remote->get_filter('api-space-total'), $total_cb);
	}

	public function test_rotation_strategy_scenario_two_b () {
		$current_cb = array($this, 'return_10_mb');
		$backups_cb = array($this, 'return_raw_items_two_b');
		$total_cb = array($this, 'return_30_mb');

		add_filter($this->_remote->get_filter('current-backup-filesize'), $current_cb);
		add_filter($this->_remote->get_filter('backups-get'), $backups_cb);
		add_filter($this->_remote->get_filter('api-space-total'), $total_cb);

		// Scenario two - there's room for exactly two backups, with last week item being remote
		$drop = $this->_remote->get_backup_rotation_list('');
		$this->assertInternalType('array', $drop);
		$this->assertEmpty($drop);

		remove_filter($this->_remote->get_filter('current-backup-filesize'), $current_cb);
		remove_filter($this->_remote->get_filter('backups-get'), $backups_cb);
		remove_filter($this->_remote->get_filter('api-space-total'), $total_cb);
	}

	public function test_rotation_strategy_scenario_two_c () {
		$current_cb = array($this, 'return_10_mb');
		$backups_cb = array($this, 'return_raw_items_two_c');
		$total_cb = array($this, 'return_20_mb');

		add_filter($this->_remote->get_filter('current-backup-filesize'), $current_cb);
		add_filter($this->_remote->get_filter('backups-get'), $backups_cb);
		add_filter($this->_remote->get_filter('api-space-total'), $total_cb);

		// Scenario two - there's room for exactly two backups, with remote item over 1 month old
		$drop = $this->_remote->get_backup_rotation_list('');
		$expected = $this->_raw_item(strtotime("-40 days", $this->_now));
		$this->assertInternalType('array', $drop);
		$this->assertNotEmpty($drop);
		$this->assertEquals($drop[0], $expected['name']);

		remove_filter($this->_remote->get_filter('current-backup-filesize'), $current_cb);
		remove_filter($this->_remote->get_filter('backups-get'), $backups_cb);
		remove_filter($this->_remote->get_filter('api-space-total'), $total_cb);
	}

	public function test_rotation_strategy_scenario_three_a () {
		$current_cb = array($this, 'return_10_mb');
		$backups_cb = array($this, 'return_raw_items_two_a');
		$total_cb = array($this, 'return_30_mb');

		add_filter($this->_remote->get_filter('current-backup-filesize'), $current_cb);
		add_filter($this->_remote->get_filter('backups-get'), $backups_cb);
		add_filter($this->_remote->get_filter('api-space-total'), $total_cb);

		// Scenario three - there's room for exactly two backups, with today's and last week item being remote
		$drop = $this->_remote->get_backup_rotation_list('');
		$expected = $this->_raw_item();
		$this->assertInternalType('array', $drop);
		$this->assertNotEmpty($drop);
		$this->assertEquals($drop[0], $expected['name']);

		remove_filter($this->_remote->get_filter('current-backup-filesize'), $current_cb);
		remove_filter($this->_remote->get_filter('backups-get'), $backups_cb);
		remove_filter($this->_remote->get_filter('api-space-total'), $total_cb);
	}

	public function test_rotation_strategy_scenario_three_b () {
		$current_cb = array($this, 'return_10_mb');
		$backups_cb = array($this, 'return_raw_items_two_b');
		$total_cb = array($this, 'return_30_mb');

		add_filter($this->_remote->get_filter('current-backup-filesize'), $current_cb);
		add_filter($this->_remote->get_filter('backups-get'), $backups_cb);
		add_filter($this->_remote->get_filter('api-space-total'), $total_cb);

		// Scenario three - there's room for exactly two backups, with both items within parameters
		$drop = $this->_remote->get_backup_rotation_list('');
		$this->assertInternalType('array', $drop);
		$this->assertEmpty($drop);

		remove_filter($this->_remote->get_filter('current-backup-filesize'), $current_cb);
		remove_filter($this->_remote->get_filter('backups-get'), $backups_cb);
		remove_filter($this->_remote->get_filter('api-space-total'), $total_cb);
	}

	public function test_rotation_strategy_scenario_three_c () {
		$current_cb = array($this, 'return_10_mb');
		$backups_cb = array($this, 'return_raw_items_two_c');
		$total_cb = array($this, 'return_30_mb');

		add_filter($this->_remote->get_filter('current-backup-filesize'), $current_cb);
		add_filter($this->_remote->get_filter('backups-get'), $backups_cb);
		add_filter($this->_remote->get_filter('api-space-total'), $total_cb);

		// Scenario three - there's room for exactly two backups, with one item within params, other too old
		$drop = $this->_remote->get_backup_rotation_list('');
		$expected = $this->_raw_item(strtotime("-40 days", $this->_now));
		$this->assertInternalType('array', $drop);
		$this->assertNotEmpty($drop);
		$this->assertEquals($drop[0], $expected['name']);

		remove_filter($this->_remote->get_filter('current-backup-filesize'), $current_cb);
		remove_filter($this->_remote->get_filter('backups-get'), $backups_cb);
		remove_filter($this->_remote->get_filter('api-space-total'), $total_cb);
	}

	public function test_rotation_strategy_scenario_three_d () {
		$current_cb = array($this, 'return_10_mb');
		$backups_cb = array($this, 'return_raw_items_three_d');
		$total_cb = array($this, 'return_30_mb');

		add_filter($this->_remote->get_filter('current-backup-filesize'), $current_cb);
		add_filter($this->_remote->get_filter('backups-get'), $backups_cb);
		add_filter($this->_remote->get_filter('api-space-total'), $total_cb);

		// Scenario three - three remote items, all within params, drop oldest
		$drop = $this->_remote->get_backup_rotation_list('');
		$expected = $this->_raw_item(strtotime("-28 days", $this->_now));
		$this->assertInternalType('array', $drop);
		$this->assertNotEmpty($drop);
		$this->assertEquals($drop[0], $expected['name']);

		remove_filter($this->_remote->get_filter('current-backup-filesize'), $current_cb);
		remove_filter($this->_remote->get_filter('backups-get'), $backups_cb);
		remove_filter($this->_remote->get_filter('api-space-total'), $total_cb);
	}

	public function test_rotation_strategy_scenario_three_e () {
		$current_cb = array($this, 'return_10_mb');
		$backups_cb = array($this, 'return_raw_items_three_e');
		$total_cb = array($this, 'return_30_mb');

		add_filter($this->_remote->get_filter('current-backup-filesize'), $current_cb);
		add_filter($this->_remote->get_filter('backups-get'), $backups_cb);
		add_filter($this->_remote->get_filter('api-space-total'), $total_cb);

		// Scenario three - three remote items, drop all outside params
		$drop = $this->_remote->get_backup_rotation_list('');
		$expected1 = $this->_raw_item();
		$expected2 = $this->_raw_item(strtotime("-40 days", $this->_now));
		$this->assertInternalType('array', $drop);
		$this->assertNotEmpty($drop);
		$this->assertEquals($drop[0], $expected1['name']);
		$this->assertEquals($drop[1], $expected2['name']);

		remove_filter($this->_remote->get_filter('current-backup-filesize'), $current_cb);
		remove_filter($this->_remote->get_filter('backups-get'), $backups_cb);
		remove_filter($this->_remote->get_filter('api-space-total'), $total_cb);
	}
*/

	public function return_10_mb () { return $this->_mb_to_bytes(10); }
	public function return_15_mb () { return $this->_mb_to_bytes(15); }
	public function return_20_mb () { return $this->_mb_to_bytes(20); }
	public function return_30_mb () { return $this->_mb_to_bytes(30); }
	public function return_100_mb () { return $this->_mb_to_bytes(100); }

	private function _mb_to_bytes ($mb) {
		return $mb * 1024 * 1024;
	}

	public function return_raw_items_1 () {
		return array($this->_raw_item());
	}

	public function return_raw_items_two_a () {
		return array(
			$this->_raw_item(strtotime("-8 days", $this->_now)),
			$this->_raw_item(),
		);
	}

	public function return_raw_items_two_b () {
		return array(
			$this->_raw_item(strtotime("-8 days", $this->_now)),
			$this->_raw_item(strtotime("-21 days", $this->_now)),
		);
	}

	public function return_raw_items_two_c () {
		return array(
			$this->_raw_item(strtotime("-40 days", $this->_now)),
			$this->_raw_item(strtotime("-8 days", $this->_now)),
		);
	}

	public function return_raw_items_three_d () {
		return array(
			$this->_raw_item(strtotime("-12 days", $this->_now)),
			$this->_raw_item(strtotime("-8 days", $this->_now)),
			$this->_raw_item(strtotime("-28 days", $this->_now)),
		);
	}

	public function return_raw_items_three_e () {
		return array(
			$this->_raw_item(),
			$this->_raw_item(strtotime("-8 days", $this->_now)),
			$this->_raw_item(strtotime("-40 days", $this->_now)),
		);
	}

	private function _raw_item ($time=false, $size=false) {
		$time = !empty($time) ? $time : $this->_now;
		$size = !empty($size) ? $size : 10;
		return array(
			'name' => Snapshot_Helper_Backup::FINAL_PREFIX . '-' . $time . '-some_crc.zip',
			'size' => $this->_mb_to_bytes($size),
		);
	}

}
