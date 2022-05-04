<?php

/**
 * @group fileset
 */
class SourceTest extends WP_UnitTestCase {

	function test_sources () {
		$failure = 'any';
		$success = 'full';
		$data = Snapshot_Model_Fileset::sources();

		$this->assertNotContains($failure, $data);
		
		$this->assertContains($success, $data);
	}

	function test_is_source () {
		$failure = 'any';
		$sources = array('full', 'media', 'themes', 'plugins', 'mu-plugins', 'config', 'htaccess');

		$this->assertNotTrue(Snapshot_Model_Fileset::is_source($failure));
		
		foreach ($sources as $src) {
			$this->assertTrue(Snapshot_Model_Fileset::is_source($src), "Not a source: {$src}");
		}
	}

	function test_class_name () {
		$failure = 'any';
		$success = 'full';

		$this->assertNotTrue(Snapshot_Model_Fileset::to_class_name($failure));

		$this->assertEquals('Snapshot_Model_Fileset_Full', Snapshot_Model_Fileset::to_class_name($success));
	}

	function test_get_source () {
		$failure = 'any';
		$sources = Snapshot_Model_Fileset::sources();

		$this->assertNotInstanceOf('Snapshot_Model_Fileset', Snapshot_Model_Fileset::get_source($failure));
		$this->assertNotTrue(Snapshot_Model_Fileset::get_source($failure));
		
		foreach ($sources as $src) {
			$this->assertInstanceOf('Snapshot_Model_Fileset', Snapshot_Model_Fileset::get_source($src), "Invalid source instance: {$src}");		
		}
	}

	function test_get_base_full () {
		$src = 'full';
		$source = Snapshot_Model_Fileset::get_source($src);
		$base = $source->get_base();

		$this->assertInternalType('string', $base);
		$this->assertEmpty($base);

	}

	function test_get_base_media () {
		$src = 'media';
		$source = Snapshot_Model_Fileset::get_source($src);
		$base = $source->get_base();

		$this->assertInternalType('string', $base);
		$this->assertEquals('wp-content/uploads', $base);
	}

	function test_get_base_themes () {
		$src = 'themes';
		$source = Snapshot_Model_Fileset::get_source($src);
		$base = $source->get_base();

		$this->assertInternalType('string', $base);
		$this->assertEquals('wp-content/themes', $base);
	}

	function test_get_base_plugins () {
		$src = 'plugins';
		$source = Snapshot_Model_Fileset::get_source($src);
		$base = $source->get_base();

		$this->assertInternalType('string', $base);
		$this->assertEquals('wp-content/plugins', $base);
	}

	function test_get_base_muplugins () {
		$src = 'mu-plugins';
		$source = Snapshot_Model_Fileset::get_source($src);
		$base = $source->get_base();

		$this->assertInternalType('string', $base);
		$this->assertEquals('wp-content/mu-plugins', $base);
	}

	function test_get_base_config () {
		$src = 'config';
		$source = Snapshot_Model_Fileset::get_source($src);
		$base = $source->get_base();

		$this->assertInternalType('string', $base);
		$this->assertEmpty($base);
	}

	function test_get_base_htaccess () {
		$src = 'htaccess';
		$source = Snapshot_Model_Fileset::get_source($src);
		$base = $source->get_base();

		$this->assertInternalType('string', $base);
		$this->assertEmpty($base);
	}

	function test_get_files_full () {
		$src = 'full';
		$source = Snapshot_Model_Fileset::get_source($src);
		$files = $source->get_files();

		$this->assertInternalType('array', $files);
		$this->assertNotEmpty($files);
	}

	function test_get_files_media () {
		$src = 'media';
		$source = Snapshot_Model_Fileset::get_source($src);
		$files = $source->get_files();

		$this->assertInternalType('array', $files);
		$this->assertEmpty($files);
	}

	function test_get_files_themes () {
		$src = 'themes';
		$source = Snapshot_Model_Fileset::get_source($src);
		$files = $source->get_files();

		$this->assertInternalType('array', $files);
		$this->assertNotEmpty($files);
	}

	function test_get_files_plugins () {
		$src = 'plugins';
		$source = Snapshot_Model_Fileset::get_source($src);
		$files = $source->get_files();

		$this->assertInternalType('array', $files);
		$this->assertNotEmpty($files);
	}

	function test_get_files_muplugins () {
		$src = 'mu-plugins';
		$source = Snapshot_Model_Fileset::get_source($src);
		$files = $source->get_files();

		$this->assertInternalType('array', $files);
		$this->assertEmpty($files);
	}

	function test_get_files_config () {
		$src = 'config';
		$source = Snapshot_Model_Fileset::get_source($src);
		$files = $source->get_files();

		$this->assertInternalType('array', $files);
		$this->assertEmpty($files); // Test environment doesn't have a wp-config.php
	}

	function test_get_files_htaccess () {
		$src = 'htaccess';
		$source = Snapshot_Model_Fileset::get_source($src);
		$files = $source->get_files();

		$this->assertInternalType('array', $files);
		$this->assertEmpty($files); // Test environment doesn't have htaccess
	}
}

