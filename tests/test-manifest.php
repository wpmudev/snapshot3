<?php

/**
 * @group manifest
 */
class ManifestTest extends WP_UnitTestCase {

	public function test_empty () {
		$backup = new Snapshot_Helper_Backup;
		$manifest = Snapshot_Model_Manifest::create($backup);

		$this->assertInstanceOf('Snapshot_Model_Manifest', $manifest);
		$this->assertNotEmpty($manifest->get_headers());

		$fail = $manifest->get('IMAGINARY HEADER');
		$this->assertEquals($fail, $manifest->get_fallback_value());
	}

	public function test_getting_all () {
		$backup = new Snapshot_Helper_Backup;
		$manifest = Snapshot_Model_Manifest::create($backup);

		$this->assertInstanceOf('Snapshot_Model_Manifest', $manifest);
		$this->assertNotEmpty($manifest->get_headers());

		$headers = $manifest->get_headers();
		$all = $manifest->get_all();

		$this->assertEquals($headers, array_keys($all));
	}

	public function test_getters () {
		$backup = new Snapshot_Helper_Backup;
		$manifest = Snapshot_Model_Manifest::create($backup);

		$ver = $manifest->get('SNAPSHOT_VERSION');
		$this->assertEquals($ver, '3.0');

		$blog_id = $manifest->get('WP_BLOG_ID');
		$this->assertEquals($backup->get_blog_id(), $blog_id);
	}

	public function test_consume () {
		$backup = new Snapshot_Helper_Backup;
		$m1 = Snapshot_Model_Manifest::create($backup);

		$file = tempnam('/tmp', 'manifest');
		file_put_contents($file, $m1->get_flat());

		$m2 = Snapshot_Model_Manifest::consume($file);

		$this->assertEquals($m1->get_all(), $m2->get_all());
	}

}