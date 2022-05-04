<?php

if (!class_exists('Snapshot_Item_UnitTestCase')) {
	require_once(__DIR__ . '/stubs/class_snapshot_item_unittestcase.php');
}

/**
 * @group snapshot
 * @group snapshot-item
 */
class SnapshotItemCleanRemoteTest extends Snapshot_Item_UnitTestCase {

	function test_clean_remotes_add () {
		$post = array();
		$prop = 'clean-remote';

		$this->item_prop_add_update($post, $prop, false, 'default');

		$post['snapshot-clean-remote'] = 1;
		$this->item_prop_add_update($post, $prop, false, 'empty post');

		$_POST = array('stub' => true);

		$post['snapshot-clean-remote'] = 0;
		$this->item_prop_add_update($post, $prop, false, 'faked post, empty prop');

		$post['snapshot-clean-remote'] = 1;
		$this->item_prop_add_update($post, $prop, true, 'faked post');
	}
}
