<?php

class LocalBackupActionsTest extends WP_UnitTestCase {
	static $time = 0;
	/**
	 * @var Snapshot_Local_Backups
	 */
	static $test_class;

	public static function setUpBeforeClass() {
		parent::setUpBeforeClass();

		self::$test_class = Snapshot_Local_Backups::get();
	}

	public function setUp() {
		parent::setUp();

		delete_option( Snapshot_Local_Backups::OPTION_NOTICE_DISMISSED );
		delete_option( Snapshot_Local_Backups::OPTION_SHOW_NOTICE );
		self::$time = time();
		Snapshot_Test_Functions::clear();
	}

	public function test_serve() {
		Snapshot_Local_Backups::serve();
		$instance = Snapshot_Local_Backups::get();

		$this->assertTrue( (boolean) has_action( 'admin_notices', array(
			$instance,
			'display_local_backups_warning',
		) ) );
		$this->assertTrue( (boolean) has_action( 'wp_ajax_dismiss_local_backups_notice', array(
			$instance,
			'snapshot_ajax_dismiss_local_backups_notice',
		) ) );
		$this->assertTrue( (boolean) has_action( 'admin_footer', array(
			$instance,
			'print_dismiss_notice_script',
		) ) );

		$this->assertTrue( (boolean) wp_next_scheduled( Snapshot_Local_Backups::HOURLY_SCHEDULED_EVENT ) );
		$this->assertTrue( (boolean) has_action( Snapshot_Local_Backups::HOURLY_SCHEDULED_EVENT, array(
			$instance,
			'check_for_local_backups',
		) ) );
	}

	public function test_stop() {
		Snapshot_Local_Backups::serve();
		Snapshot_Local_Backups::stop();
		$instance = Snapshot_Local_Backups::get();

		$this->assertFalse( (boolean) has_action( 'admin_notices', array(
			$instance,
			'display_local_backups_warning',
		) ) );
		$this->assertFalse( (boolean) has_action( 'wp_ajax_dismiss_local_backups_notice', array(
			$instance,
			'snapshot_ajax_dismiss_local_backups_notice',
		) ) );
		$this->assertFalse( (boolean) has_action( 'admin_footer', array(
			$instance,
			'print_dismiss_notice_script',
		) ) );

		$this->assertFalse( (boolean) wp_next_scheduled( Snapshot_Local_Backups::HOURLY_SCHEDULED_EVENT ) );
		$this->assertFalse( (boolean) has_action( Snapshot_Local_Backups::HOURLY_SCHEDULED_EVENT, array(
			$instance,
			'check_for_local_backups',
		) ) );
	}

	public function test_user_dismisses_notice() {
		$this->given_we_have_dummy_local_backups();
		// Array containing dismissed notices is empty so notice is displayed
		$this->given_dismissed_notice_timestamps( false );
		self::$test_class->check_for_local_backups();
		$this->then_notice_is_displayed( true );

		// The user dismisses through ajax call and then no notice is displayed
		self::$test_class->dismiss_local_backups_notice();
		$this->then_notice_is_displayed( false );

		// The next check does not make any difference
		self::$test_class->check_for_local_backups();
		$this->then_notice_is_displayed( false );

		// But then an additional local backup becomes available. Notice is displayed again
		$this->given_we_have_another_dummy_local_backup();
		self::$test_class->check_for_local_backups();
		$this->then_notice_is_displayed( true );

		// The user dismisses again, and so on ...
		self::$test_class->dismiss_local_backups_notice();
		$this->then_notice_is_displayed( false );
	}

	function test_no_notices_dismissed() {
		$this->given_we_have_dummy_local_backups();
		$this->given_dismissed_notice_timestamps( false );
		self::$test_class->check_for_local_backups();
		$this->then_notice_is_displayed( true );
	}

	function test_some_old_notices_dismissed() {
		$this->given_we_have_dummy_local_backups();
		$this->given_dismissed_notice_timestamps( array(
			$this->time_offset( - 100 ),
		) );
		self::$test_class->check_for_local_backups();
		$this->then_notice_is_displayed( true );
	}

	public function test_all_old_notices_dismissed() {
		$this->given_we_have_dummy_local_backups();
		$this->given_dismissed_notice_timestamps( array(
			$this->time_offset( - 100 ),
			$this->time_offset( 0 ),
		) );
		self::$test_class->check_for_local_backups();
		$this->then_notice_is_displayed( false );
	}

	public function dummy_local_backups() {
		return array(
			array(
				'name'      => 'full_backup-1544790163-full-ef08084c.zip',
				'size'      => 8780403,
				'timestamp' => $this->time_offset( - 100 ),
				'local'     => true,
			),
			array(
				'name'      => 'full_backup-1544794904-full-7f7793fc.zip',
				'size'      => 8779211,
				'timestamp' => $this->time_offset( 0 ),
				'local'     => true,
			),
			array(
				'name'      => 'full_backup-1544793964-full-0855ab4c.zip',
				'size'      => 8779212,
				'timestamp' => $this->time_offset( + 100 ),
				'local'     => true,
			),
		);
	}

	public function another_dummy_local_backup( $backups ) {
		$backups[] = array(
			'name'      => 'full_backup-1544793964-full-0855ab4d.zip',
			'size'      => 8771234,
			'timestamp' => $this->time_offset( - 200 ),
			'local'     => true,
		);

		return $backups;
	}

	private function time_offset( $offset ) {
		$twelve_hours_in_secs = 43200;
		return self::$time - $twelve_hours_in_secs + $offset;
	}

	private function then_notice_is_displayed( $is_displayed ) {
		$actual = get_option( Snapshot_Local_Backups::OPTION_SHOW_NOTICE );
		$actual_pretty = var_export( $actual, true );
		$expected_pretty = var_export( $is_displayed, true );
		$this->assertTrue(
			$is_displayed === $actual,
			"Asserting that actual value [$actual_pretty] is equal to expected [$expected_pretty]"
		);
	}

	/**
	 * @param $dismissed
	 */
	private function given_dismissed_notice_timestamps( $dismissed ) {
		update_option( Snapshot_Local_Backups::OPTION_NOTICE_DISMISSED, $dismissed );
	}

	private function given_we_have_dummy_local_backups() {
		add_action( 'snapshot-model-full-local-get_backups', array( $this, 'dummy_local_backups' ) );
	}

	private function given_we_have_another_dummy_local_backup() {
		add_action( 'snapshot-model-full-local-get_backups', array( $this, 'another_dummy_local_backup' ) );
	}
}
