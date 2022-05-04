<?php

abstract class Snapshot_Item_UnitTestCase extends WP_UnitTestCase {

	function setUp () {
		if (!class_exists('WPMUDEVSnapshot_NoAddUpdateConfigItem')) {
			require_once('class_wpmudev_snapshot_noaddupdateconfigitem.php');
		}
		// This is so it doesn't try to redirect
		if (!defined('DOING_AJAX')) {
			define('DOING_AJAX', true);
		}
	}

	function item_prop_add_update ($post, $prop, $expected, $name) {
		$s = WPMUDEVSnapshot_NoAddUpdateConfigItem::instance();
		$actual_timestamp = time();
		$actions = array('add', 'update');
		foreach ($actions as $action) {
			$post['snapshot-action'] = $action;
			$post['snapshot-item'] = $actual_timestamp;

			if ('update' === $action) {
				$s->config_data['items'][$actual_timestamp] = array(
					'blog-id' => 0,
					'timestamp' => $actual_timestamp,
				); // So it doesn't die.
			}

			$expected_timestamp = $s->snapshot_add_update_action_proc($post);
			$this->assertEquals($expected_timestamp, $actual_timestamp);

			$item = $s->get_item();
			$this->item_prop_check($item, $prop, $expected, $name);
		}
	}

	function item_prop_check ($item, $prop, $expected, $name='') {
		$this->assertTrue(
			isset($item[$prop]),
			"Item {$name} has required property {$prop}"
		);
		$this->assertEquals(
			$item[$prop], $expected,
			"Item {$name} property {$prop} value checks out"
		);
	}
}

