<?php

/**
 * @group view
 */
class FullBackupTest extends WP_UnitTestCase {

	function test_role_getting () {
		$view = Snapshot_View_Full_Backup::get();
		$role = $view->get_page_role();

		$expected = is_multisite()
			? 'export'
			: 'manage_snapshots_items'
		;

		$this->assertEquals(0, strcmp($role, $expected));
	}

	function test_run () {
		$view = Snapshot_View_Full_Backup::get();
		$view->run();

		$this->assertTrue((bool)has_action('admin_menu', array($view, 'add_pages')));
	}

	function test_add_pages () {
		$view = Snapshot_View_Full_Backup::get();
		$idx = $view->add_pages();

		$this->assertFalse($idx); // Because admin hasn't been triggered yet

		$this->assertTrue((bool)has_action("load-{$idx}", array($view, 'add_dependencies')));
	}

	function test_filter_getting () {
		$view = Snapshot_View_Full_Backup::get();

		$this->assertFalse($view->get_filter());
		$this->assertFalse($view->get_filter(111));

		$this->assertEquals(0, strcmp('snapshot-views-full_backup-test', $view->get_filter('test')));
	}

	function test_add_dependencies () {
		$view = Snapshot_View_Full_Backup::get();

		$view->add_dependencies();
		$this->assertTrue(wp_style_is('snapshot-admin'));
		$this->assertTrue(wp_style_is('snapshot-full_backup-admin'));
	}
}