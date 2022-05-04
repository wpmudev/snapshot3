<?php

/**
 * @group snapshot
 * @group helper
 */
class SnapshotHelperUtilityTest extends WP_UnitTestCase {

	public function test_check_server_timeout () {
		$this->assertTrue(defined("Snapshot_Helper_Utility::MIN_VIABLE_EXEC_TIME"));
		$minimum = Snapshot_Helper_Utility::MIN_VIABLE_EXEC_TIME;
		$base_time = (int)($minimum / 12);

		$old = (int)ini_get( 'max_execution_time' );

		ini_set('max_execution_time', $base_time);
		$current = (int)ini_get( 'max_execution_time' );
		if ($base_time === $current && $base_time !== $old) {
			// We were successful in setting max execution time, tests follow.
			$status = Snapshot_Helper_Utility::check_server_timeout();
			$this->assertTrue($status, "We were successful in pushing the exec time forward");
			$actual = (int)ini_get( 'max_execution_time' );
			$this->assertEquals(0, $actual, "Exec time shifted 3x");

			$status = Snapshot_Helper_Utility::check_server_timeout();
			$this->assertTrue($status, "We were successful in pushing the exec time forward second time");
			$actual = (int)ini_get( 'max_execution_time' );
			$this->assertEquals(0, $actual, "Exec time is still zero");

			ini_set('max_execution_time', $old);
		} else {
			// We weren't successful in changing max exec time, tests follow.
			if (0 === $old || $old < Snapshot_Helper_Utility::MIN_VIABLE_EXEC_TIME) {
				$this->assertTrue(Snapshot_Helper_Utility::check_server_timeout(), "No timeout means we're good");
			} else {
				$this->assertFalse(Snapshot_Helper_Utility::check_server_timeout(), "We have timeout and it's less than 30 secs - we're not good");
			}
		}
	}

	public function test_scandir_pushes_server_timeout () {
		$minimum = Snapshot_Helper_Utility::MIN_VIABLE_EXEC_TIME;
		$base_time = (int)($minimum / 9);
		$old = (int)ini_get( 'max_execution_time' );

		ini_set('max_execution_time', $base_time);
		$current = (int)ini_get( 'max_execution_time' );

		$result = Snapshot_Helper_Utility::scandir();
		$actual = (int)ini_get('max_execution_time');
		$this->assertEquals($current, $actual, "Noop doesn't push exec time");

		if ($base_time === $current && $base_time !== $old) {
			$result = Snapshot_Helper_Utility::scandir('.');
			$actual = (int)ini_get('max_execution_time');
			$this->assertEquals(0, $actual, "Scadir pushed time");

			ini_set('max_execution_time', $old);
		}
	}
}
