<?php

/**
 * @group time
 */
class TimeTest extends WP_UnitTestCase {

	public function setUp () {
		$this->_time = Snapshot_Model_Time::get();
	}

	function test_get_utc_offset () {
		$offset = $this->_time->get_utc_offset();
		$this->assertEquals(0, $offset);

		update_option('timezone_string', 'Pacific/Honolulu');
		$offset = $this->_time->get_utc_offset();
		$this->assertEquals(-10, $offset);
		update_option('timezone_string', '');

		update_option('gmt_offset', '2');
		$offset = $this->_time->get_utc_offset();
		$this->assertEquals(2, $offset);
		update_option('gmt_offset', '');
	}

	function test_get_next_monday () {
		$now = time();
		$monday = strtotime('last Monday', $now);
		$this->assertTrue($now > $monday);

		$next = $this->_time->get_next_monday($now);
		$this->assertTrue($next > $monday, "Next Monday comes after last Monday");
		$this->assertTrue($next > $now, "Next Monday is in the future");
		$this->assertEquals((int)date('N', $monday), (int)date('N', $next), "Mondays are same days");
		$this->assertEquals(1, (int)date('N', $next), "Next Monday is actually Monday");
	}

	function test_get_offsets () {
		$bkp = new Snapshot_Model_Full_Backup;
		$weekly = $bkp->get_offsets('weekly');
		$this->assertEquals(7, count($weekly), "There are 7 distinct offsets in a week");

		$bkp->set_config('frequency', 'weekly');
		$w2 = $bkp->get_offsets();
		$this->assertSame($weekly, $w2, "Contextual offsets getting matches forced one");
		$bkp->set_config('frequency', '');

		$monthly = $bkp->get_offsets('monthly');
		$this->assertEquals(30, count($monthly), "There are 31 distinct offset in a month");

		$bkp->set_config('frequency', 'monthly');
		$m2 = $bkp->get_offsets();
		$this->assertSame($monthly, $m2, "Contextual offsets getting matches forced one - monhtly");
		$bkp->set_config('frequency', '');
	}

	function test_get_offset_base () {
		$bkp = new Snapshot_Model_Full_Backup;

		$offset = $bkp->get_offset_base();
		$this->assertEquals(0, $offset, "Default offset is 0");

		$bkp->set_config('schedule_offset', 21);
		$offset = $bkp->get_offset_base();
		$this->assertEquals(0, $offset, "Default offset base is weekly and truncates");
		$offset = $bkp->get_offset_base('weekly');
		$this->assertEquals(0, $offset, "Default offset base is weekly");
		$offset = $bkp->get_offset_base('monthly');
		$this->assertEquals(21, $offset, "Monthly frequency allows larger offset base");

		$bkp->set_config('schedule_offset', 'zxcvzxcvzxcv');
		$this->assertEquals(0, $bkp->get_offset_base(), "Garbage offsets are truncated");
		$bkp->set_config('schedule_offset', 0);
	}

	function test_get_schedule () {
		$bkp = new Snapshot_Model_Full_Backup;
		$now = time();
		$monday = strtotime('last Monday', $now);

		$sched = $bkp->get_offset($now, 'weekly');
		$this->assertTrue($sched > $now, "Schedule is in the future");
		$this->assertEquals((int)date('N', $monday), (int)date('N', $sched), "Default weekly offset schedule value is Monday");

		$bkp->set_config('schedule_offset', 21);
		$sched2 = $bkp->get_offset($now, 'weekly');
		$this->assertTrue($sched2 > $now, "Schedule is in the future");
		$this->assertEquals($sched, $sched2, "Offsets base with out of context range get truncated");

		$sched3 = $bkp->get_offset($now, 'monthly');
		$this->assertTrue($sched3 > $now, "Schedule is in the future");
		$this->assertEquals(21, (int)date('d', $sched3), "Montly offset proper date");

		$bkp->set_config('schedule_offset', 0);
	}
}

