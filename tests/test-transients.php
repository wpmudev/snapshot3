<?php

/**
 * @group transient
 */
class TransientTest extends WP_UnitTestCase {

	function test_named_ttls () {
		$this->assertEquals(
			-86400,
			Snapshot_Model_Transient::ttl(Snapshot_Model_Transient::TTL_CLEAR),
			"Not negative day for clears"
		);
		$this->assertEquals(
			60,
			Snapshot_Model_Transient::ttl(Snapshot_Model_Transient::TTL_TICK),
			"Not one minute tick"
		);
		$this->assertEquals(
			600,
			Snapshot_Model_Transient::ttl(Snapshot_Model_Transient::TTL_SHORT),
			"Not ten minutes short"
		);
		$this->assertEquals(
			3600,
			Snapshot_Model_Transient::ttl(Snapshot_Model_Transient::TTL_CACHE),
			"Not one hour cache"
		);
		$this->assertEquals(
			18000,
			Snapshot_Model_Transient::ttl(Snapshot_Model_Transient::TTL_LONG),
			"Not five hours long"
		);
		$this->assertEquals(
			86400,
			Snapshot_Model_Transient::ttl(Snapshot_Model_Transient::TTL_PERMA),
			"Not one day perma"
		);
	}

	function test_set_get_delete () {
		$name = "test_transient";
		$value = "test value";
		
		$result = Snapshot_Model_Transient::set($name, $value);
		$this->assertTrue($result, "Transient not set");

		$result = Snapshot_Model_Transient::get($name, false);
		$this->assertNotFalse($result, "Transient not got");
		$this->assertEquals($value, $result, "Invalid result");

		$result = Snapshot_Model_Transient::delete($name);
		$this->assertTrue($result, "Transient not deleted");

		$result = Snapshot_Model_Transient::get($name, false);
		$this->assertFalse($result, "Transient value got althoug removed");
		$this->assertNotEquals($value, $result, "Transient value deleted but here");
	}

	function test_expiry () {
		$name = "test_transient";
		$value = "test value";
		
		Snapshot_Model_Transient::set($name, $value, Snapshot_Model_Transient::ttl(Snapshot_Model_Transient::TTL_CACHE));

		$result = Snapshot_Model_Transient::is_expired($name);
		$this->assertFalse($result, "Transient expired on set");

		$result = Snapshot_Model_Transient::expire($name);
		$this->assertTrue($result, "Transient expiring failed");

		$result = Snapshot_Model_Transient::is_expired($name);
		$this->assertTrue($result, "Transient not properly expired");

		$result = Snapshot_Model_Transient::get($name, false);
		$this->assertFalse($result, "Still able to get expired transient as current");

		$result = Snapshot_Model_Transient::get_expired($name, false);
		$this->assertEquals($value, $result, "Not able to get expired transient");


		$result = Snapshot_Model_Transient::get_any($name, false);
		$this->assertEquals($value, $result, "Not able to get expired transient with any-type getter");

		Snapshot_Model_Transient::delete($name);
	}
}
