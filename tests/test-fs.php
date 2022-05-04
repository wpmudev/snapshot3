<?php

/**
 * @group fs
 */
class FsTest extends WP_UnitTestCase {

	function test_get_webroot () {
		$fs = new Snapshot_Helper_Fs;
		$path = $fs->get_webroot();
		$this->assertFalse($path);
	}

	function test_get_uploads () {
		$fs = new Snapshot_Helper_Fs;
		$path = $fs->get_uploads();
		$this->assertNotFalse($path);

		$wp_upload_dir = wp_upload_dir();
		$this->assertEquals($path, wp_normalize_path(realpath($wp_upload_dir['basedir'])));
	}
}
