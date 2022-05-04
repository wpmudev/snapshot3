
<?php

/**
 * @group utility
 */
class UtilityTest extends WP_UnitTestCase {


	public function test_size_unformat () {
		$sizes = array(
			'b' => 1,
			'k' => 1024,
			'm' => 1024 * 1024,
			'g' => 1024 * 1024 * 1024,
		);
		foreach ($sizes as $unit => $base) {
			foreach (range(1, 5, 0.2) as $increment) {
				$expected_size = round($increment * $base, 2);
				$val = "{$increment}{$unit}";
				$actual_size = round(Snapshot_Helper_Utility::size_unformat($val), 2);
				$this->assertEquals(
					$expected_size, 
					$actual_size, 
					"Equal for {$unit} {$increment}"
				);
			}
		}
	}
}
