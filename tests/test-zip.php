<?php

/**
 * @group zip
 */
class ZipTest extends WP_UnitTestCase {

	function test_class () {
		$zip = Snapshot_Helper_Zip::get_object(Snapshot_Helper_Zip::TYPE_ARCHIVE);
		$this->assertInstanceOf('Snapshot_Helper_Zip_Archive', $zip);

		$zip = Snapshot_Helper_Zip::get_object(Snapshot_Helper_Zip::TYPE_PCLZIP);
		$this->assertInstanceOf('Snapshot_Helper_Zip_Pclzip', $zip);

		$zip = Snapshot_Helper_Zip::get_object();
		if ('PclZip' === WPMUDEVSnapshot::instance()->config_data['config']['zipLibrary']) {
			$this->assertInstanceOf('Snapshot_Helper_Zip_Pclzip', $zip);
		} else {
			$this->assertInstanceOf('Snapshot_Helper_Zip_Archive', $zip);
		}
	}

	function test_forced_variation () {
		if (!defined('SNAPSHOT_FORCE_ZIP_LIBRARY')) {
			define('SNAPSHOT_FORCE_ZIP_LIBRARY', Snapshot_Helper_Zip::TYPE_ARCHIVE);

			$zip = Snapshot_Helper_Zip::get_object(Snapshot_Helper_Zip::TYPE_PCLZIP);

			$this->assertInstanceOf('Snapshot_Helper_Zip_Archive', $zip);
		}
	}

	function test_add_archive () {
		$backup = WPMUDEVSnapshot::instance()->get_setting('backupBaseFolderFull');
		$path = trailingslashit($backup) . 'archive_test.zip';
		$test_path = trailingslashit(wp_normalize_path(apply_filters('snapshot_home_path', get_home_path()))) . 'license.txt';

		if (file_exists($path)) unlink($path);
		$this->assertFileNotExists($path);

		$zip = Snapshot_Helper_Zip::get($path, Snapshot_Helper_Zip::TYPE_ARCHIVE);
		$status = $zip->add($test_path);

		$this->assertTrue($status);
		$this->assertFileExists($path);

		unlink($path);
	}

	function test_has_archive () {
		$backup = WPMUDEVSnapshot::instance()->get_setting('backupBaseFolderFull');
		$path = trailingslashit($backup) . 'archive_test.zip';
		$test_path = trailingslashit(wp_normalize_path(apply_filters('snapshot_home_path', get_home_path()))) . 'license.txt';

		if (file_exists($path)) unlink($path);
		$this->assertFileNotExists($path);

		$zip = Snapshot_Helper_Zip::get($path, Snapshot_Helper_Zip::TYPE_ARCHIVE);
		$status = $zip->add($test_path);

		$this->assertTrue($status);
		$this->assertFileExists($path);

		$status = $zip->has($test_path);
		$this->assertTrue($status);

		$status = $zip->has(basename($test_path));
		$this->assertTrue($status);

		unlink($path);
	}

	function test_extract_archive () {
		$backup = WPMUDEVSnapshot::instance()->get_setting('backupBaseFolderFull');
		$path = trailingslashit($backup) . 'archive_test.zip';
		$test_path = trailingslashit(wp_normalize_path(apply_filters('snapshot_home_path', get_home_path()))) . 'license.txt';

		if (file_exists($path)) unlink($path);
		$this->assertFileNotExists($path);

		$zip = Snapshot_Helper_Zip::get($path, Snapshot_Helper_Zip::TYPE_ARCHIVE);
		$status = $zip->add($test_path);

		$this->assertTrue($status);
		$this->assertFileExists($path);

		$tmp = sys_get_temp_dir();
		$destination = trailingslashit(wp_normalize_path($tmp)) . basename($test_path);

		$this->assertFileNotExists($destination);

		$status = $zip->extract($tmp);

		$this->assertTrue($status);
		$this->assertFileExists($destination);

		unlink($path);
		unlink($destination);
	}

	function test_add_pclzip () {
		$backup = WPMUDEVSnapshot::instance()->get_setting('backupBaseFolderFull');
		$path = trailingslashit($backup) . 'pclzip_test.zip';
		$test_path = trailingslashit(wp_normalize_path(apply_filters('snapshot_home_path', get_home_path()))) . 'license.txt';

		if (file_exists($path)) unlink($path);
		$this->assertFileNotExists($path);

		$zip = Snapshot_Helper_Zip::get($path, Snapshot_Helper_Zip::TYPE_PCLZIP);
		$status = $zip->add($test_path);

		$this->assertTrue($status);
		$this->assertFileExists($path);

		unlink($path);
	}

	function test_has_pclzip () {
		$backup = WPMUDEVSnapshot::instance()->get_setting('backupBaseFolderFull');
		$path = trailingslashit($backup) . 'pclzip_test.zip';
		$test_path = trailingslashit(wp_normalize_path(apply_filters('snapshot_home_path', get_home_path()))) . 'license.txt';

		$this->assertFileExists($test_path);

		if (file_exists($path)) unlink($path);
		$this->assertFileNotExists($path);

		$zip = Snapshot_Helper_Zip::get($path, Snapshot_Helper_Zip::TYPE_PCLZIP);
		$status = $zip->add($test_path);

		$this->assertTrue($status);
		$this->assertFileExists($path);

		$status = $zip->has($test_path);
		$this->assertTrue($status);

		$status = $zip->has(basename($test_path));
		$this->assertTrue($status);

		unlink($path);
	}

	function test_extract_pclzip () {
		$backup = WPMUDEVSnapshot::instance()->get_setting('backupBaseFolderFull');
		$path = trailingslashit($backup) . 'pclzip_test.zip';
		$test_path = trailingslashit(wp_normalize_path(apply_filters('snapshot_home_path', get_home_path()))) . 'license.txt';

		$this->assertFileExists($test_path);

		if (file_exists($path)) unlink($path);
		$this->assertFileNotExists($path);

		$zip = Snapshot_Helper_Zip::get($path, Snapshot_Helper_Zip::TYPE_PCLZIP);
		$status = $zip->add($test_path);

		$this->assertTrue($status);
		$this->assertFileExists($path);

		$tmp = sys_get_temp_dir();
		$destination = trailingslashit(wp_normalize_path($tmp)) . basename($test_path);

		$this->assertFileNotExists($destination);

		$status = $zip->extract($tmp);

		$this->assertTrue($status);
		$this->assertFileExists($destination);

		unlink($path);
		unlink($destination);
	}
}
