<?php

/**
 * @group hub
 * @group controller
 * @group report
 */
class ReportTest extends WP_UnitTestCase {

	function test_run () {
		$r = Snapshot_Controller_Full_Reporter::get();
		$this->assertTrue($r->is_running(), "Reporter boots by default");
	}

	function test_increment () {
		$r = Snapshot_Controller_Full_Reporter::get();
		$test = array(
			0 => true,
			1 => false,
			2.25 => false,
			5 => true,
			5.5 => true,
			7 => false,
			10.1 => false,
			10.01 => true,
		);
		foreach ($test as $percentage => $expected) {
			$this->assertEquals(
				$r->is_reportable_increment($percentage, 5),
				$expected,
				"Succesfull estimate of {$percentage}% as reportable increment"
			);
		}
	}

	function test_report_sending () {
		$r = Snapshot_Controller_Full_ReporterTest::get();
		$value = 'test value';
		$result = $r->send_report(array('test' => $value));

		$this->assertTrue(isset($result['test']));
		$this->assertTrue(isset($result['domain']));
		$this->assertSame($result['test'], $value);
	}

	function test_backup_start_report () {
		$r = Snapshot_Controller_Full_ReporterTest::get();
		$result = $r->send_backup_start_report(true);

		$this->assertTrue(isset($result['status']));
		$this->assertSame($result['status'], Snapshot_Controller_Full_Reporter::STATUS_START);

		$this->assertTrue(isset($result['info']));
		$this->assertSame($result['info'], 0);
	}

	function test_backup_upload_report () {
		$r = Snapshot_Controller_Full_ReporterTest::get();
		$result = $r->send_backup_uploading_report(true);

		$this->assertTrue(isset($result['status']));
		$this->assertSame($result['status'], Snapshot_Controller_Full_Reporter::STATUS_UPLOAD);

		$this->assertTrue(isset($result['info']));
		$this->assertSame($result['info'], 100);
	}

	function test_backup_finish_report () {
		$r = Snapshot_Controller_Full_ReporterTest::get();
		$result = $r->send_backup_finished_report(true);

		$this->assertTrue(isset($result['status']));
		$this->assertSame($result['status'], Snapshot_Controller_Full_Reporter::STATUS_FINISH);

		$this->assertTrue(isset($result['info']));
		$this->assertSame($result['info'], 100);
	}

	function test_backup_error_report () {
		$r = Snapshot_Controller_Full_ReporterTest::get();
		$message = 'User-friendly text message';
		$result = $r->send_backup_creation_error_report('test', 'which', $message);

		$this->assertTrue(isset($result['status']));
		$this->assertSame($result['status'], Snapshot_Controller_Full_Reporter::STATUS_ERROR);

		$this->assertTrue(isset($result['info']));
		$this->assertSame($result['info'], $message);

		$result = $r->send_backup_creation_error_report('test', 'which', false);
		$this->assertFalse(empty($result['info']));
		$this->assertFalse($result['info'] === $message);

		$this->assertTrue(!!preg_match('/test/', $result['info']));
		$this->assertTrue(!!preg_match('/which/', $result['info']));
	}
}


class Snapshot_Controller_Full_ReporterTest extends Snapshot_Controller_Full_Reporter {
	private static $_instance;

	public static function get () {
		if (empty(self::$_instance)) {
			self::$_instance = new self;
		}
		return self::$_instance;
	}

	protected function _get_report_response ($report, $remote) {
		return $report;
	}
}
