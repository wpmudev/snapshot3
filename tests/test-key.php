<?php

/**
 * @group key
 */
class KeyTest extends WP_UnitTestCase {

	public function test_key_drop () {
		$key = Snapshot_Model_Full_Remote_Key::get();
		$this->assertTrue($key instanceof Snapshot_Model_Full_Remote_Key);

		$this->assertTrue(method_exists($key, 'drop_key'));
		$this->assertTrue($key->drop_key());
	}

	public function test_local_key_set_drop_get () {
		$key = Snapshot_Model_Full_Remote_Key::get();
		$this->assertTrue($key instanceof Snapshot_Model_Full_Remote_Key);

		$test_key = '123abc';

		$this->assertTrue(method_exists($key, 'set_key'));
		$this->assertTrue($key->set_key($test_key));

		$this->assertTrue(method_exists($key, 'get_key'));
		$this->assertEquals($test_key, $key->get_key());

		$this->assertTrue($key->drop_key());
		$this->assertFalse($key->get_key());
	}

	public function test_remote_key_get () {
		$key = Snapshot_Model_Full_Remote_Key::get();
		$this->assertTrue($key instanceof Snapshot_Model_Full_Remote_Key);

		$api = Snapshot_Model_Full_Remote_Api::get();
		$this->assertTrue($api instanceof Snapshot_Model_Full_Remote_Api);

		// Let's fake DEV request for test purposes
		$dev = Mock_Wpmu_Dev_Request::start();
		if (!$dev->is_up()) return false; // No fake DEV instance running, can't proceed

		$this->assertTrue(method_exists($key, 'get_remote_key'));

		$remote_key = $key->get_remote_key();
		$this->assertNotFalse($remote_key);
	}

	public function test_reset_key () {
		$key = Snapshot_Model_Full_Remote_Key::get();
		$this->assertTrue($key instanceof Snapshot_Model_Full_Remote_Key);

		$this->assertTrue(method_exists($key, 'reset_key'));

		// Fails without DEV request faking
		$this->assertFalse($key->reset_key());

		$dev = Mock_Wpmu_Dev_Request::start();
		if (!$dev->is_up()) return false; // No fake DEV instance running, can't proceed
		$this->assertTrue($key->reset_key());
	}

}

