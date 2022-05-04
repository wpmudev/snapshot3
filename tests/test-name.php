<?php

/**
 * @group name
 */
class NameTest extends WP_UnitTestCase {

	function test_empty_name () {
		$this->assertFalse(
			Snapshot_Helper_String::conceal(false)
		);
		$this->assertFalse(
			Snapshot_Helper_String::conceal('')
		);
		$this->assertNotFalse(
			Snapshot_Helper_String::conceal('random')
		);
	}

	function test_encode_string () {
		$test = "test name here more more more how about this nanana";
		
		$encoded = Snapshot_Helper_String::conceal($test);
		$this->assertNotFalse($encoded);
		$this->assertNotEquals($encoded, $test);
	}

	function test_decode_string () {
		$test = "test name here more more more how about this nanana";
		
		$encoded = Snapshot_Helper_String::conceal($test);
		$decoded = Snapshot_Helper_String::reveal($encoded);
		
		$this->assertNotFalse($decoded);
		$this->assertNotEquals($decoded, $encoded);
		$this->assertEquals($decoded, $test);
	}

	function test_encode_filename () {
		$test = "archive.zip";
		
		$encoded = Snapshot_Helper_String::conceal($test);
		$this->assertNotFalse($encoded);
		$this->assertNotEquals($encoded, $test);

		$testinfo = pathinfo($test, PATHINFO_EXTENSION);
		$encinfo = pathinfo($encoded, PATHINFO_EXTENSION);
		$this->assertEquals($testinfo, $encinfo);
	}

	function test_decode_filename () {
		$test = "archive.zip";
		
		$encoded = Snapshot_Helper_String::conceal($test);
		
		$testinfo = pathinfo($test, PATHINFO_EXTENSION);
		$encinfo = pathinfo($encoded, PATHINFO_EXTENSION);
		$this->assertEquals($testinfo, $encinfo);

		$decoded = Snapshot_Helper_String::reveal($encoded);
		
		$this->assertNotFalse($decoded);
		$this->assertNotEquals($decoded, $encoded);
		$this->assertEquals($decoded, $test);
	}

}