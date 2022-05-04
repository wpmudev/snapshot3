
<?php

/**
 * @group hub
 */
class HubTest extends WP_UnitTestCase {

	public function test_hub_controller () {
		$hub = Snapshot_Controller_Full_Hub::get();

		$this->assertTrue($hub instanceof Snapshot_Controller_Full);
	}

	public function test_known_actions_getting () {
		$hub = Snapshot_Controller_Full_Hub::get();

		$this->assertTrue(is_callable(array($hub, 'get_known_actions')));

		$known = $hub->get_known_actions();
		$this->assertTrue(is_array($known));

		// Check for known actions presence
		$this->assertTrue(in_array(Snapshot_Controller_Full_Hub::ACTION_CLEAR_API_CACHE, $known));
	}

	public function test_endpoint_registration () {
		$key = 'snapshot_' . Snapshot_Controller_Full_Hub::ACTION_CLEAR_API_CACHE;
		$hub = Snapshot_Controller_Full_Hub::get();

		if (!$hub->is_running()) {
			$actions = apply_filters('wdp_register_hub_action', array());
			$this->assertTrue(is_array($actions), "Actions are array");
			$this->assertFalse(array_key_exists($key, $actions), "Actions have API cache clearing registered");

			// Now, run the controller
			Snapshot_Controller_Full_Hub::get()->run();
		}

		$actions = apply_filters('wdp_register_hub_action', array());
		$this->assertTrue(is_array($actions));
		$this->assertTrue(array_key_exists($key, $actions));
	}

	public function test_api_cache_clearing () {
		$hub = Snapshot_Controller_Full_Hub::get();
		$handler = 'json_' . Snapshot_Controller_Full_Hub::ACTION_CLEAR_API_CACHE;
		$method = 'clear_api_cache';
		$this->assertTrue(is_callable(array($hub, $handler)), "We can clear cache");
		$this->assertTrue(is_callable(array($hub, $method)), "We have the clearing method");

		$dev = Mock_Wpmu_Dev_Request::start();
		if (!$dev->is_up()) return false; // No fake DEV instance running, can't proceed

		$status = $hub->$method(array(), Snapshot_Controller_Full_Hub::ACTION_CLEAR_API_CACHE);
		$this->assertTrue($status instanceof WP_Error, 'Returns error without connection mocking');

		$key = Snapshot_Model_Full_Remote_Key::get();
		$key->reset_key(); // We need API key for this, so let's carry on mocking
	}

	public function test_api_backup_start () {
		$hub = Snapshot_Controller_Full_Hub::get();
		$handler = 'json_' . Snapshot_Controller_Full_Hub::ACTION_START_BACKUP;
		$method = 'start_backup';
		$this->assertTrue(is_callable(array($hub, $handler)), "We can start backup");
		$this->assertTrue(is_callable(array($hub, $method)), "We have the backup start method");

		$cron = Snapshot_Controller_Full_Cron::get();
		$model = new Snapshot_Model_Full_Backup;

		$this->assertFalse($cron->is_running(), "Initial state is stopped");

		$dev = Mock_Wpmu_Dev_Request::start();
		if (!$dev->is_up()) return false; // No fake DEV instance running, can't proceed

		add_filter(
			$model->get_filter('has_dashboard'),
			'__return_true'
		);

		$hub->$method(array(), Snapshot_Controller_Full_Hub::ACTION_START_BACKUP);

		//$this->assertTrue($cron->is_running(), "Backup is running");
		$cron->stop();
	}

	public function test_api_schedule_params_validation () {
		$hub = Snapshot_Controller_Full_Hub::get();
		$handler = 'json_' . Snapshot_Controller_Full_Hub::ACTION_SCHEDULE_BACKUPS;
		$method = 'validate_schedule_params';
		$this->assertTrue(is_callable(array($hub, $handler)), "We can reschedule");
		$this->assertTrue(is_callable(array($hub, $method)), "We have the rescheduling validation method");

		// Invalid
		$params = (object)array(
			'active' => true,
			'frequency' => 2, // Invalid
			'offset' => 0,
			'time' => 100,
			'limit' => 3,
		);
		$resp = $hub->$method($params);
		$this->assertTrue(is_wp_error($resp), "Invalid frequency param fails validation");
		$this->assertSame($resp->get_error_code(), Snapshot_Controller_Full_Hub::ACTION_SCHEDULE_BACKUPS);
		$this->assertRegExp('/frequency/', $resp->get_error_message());

		$params->frequency = 'daily';
		$params->time = 'cvzvzxcvzcxv'; // Invalid.
		$resp = $hub->$method($params);
		$this->assertTrue(is_wp_error($resp), "Invalid time param fails validation");
		$this->assertSame($resp->get_error_code(), Snapshot_Controller_Full_Hub::ACTION_SCHEDULE_BACKUPS);
		$this->assertRegExp('/time/', $resp->get_error_message());

		$params->time = 100;
		$params->limit = 'cvzvzxcvzcxv'; // Invalid.
		$resp = $hub->$method($params);
		$this->assertTrue(is_wp_error($resp), "Invalid limit param fails validation");
		$this->assertSame($resp->get_error_code(), Snapshot_Controller_Full_Hub::ACTION_SCHEDULE_BACKUPS);
		$this->assertRegExp('/limit/', $resp->get_error_message());

		$params->limit = 3;
		$params->offset = 'cvzvzxcvzcxv'; // Invalid.
		$resp = $hub->$method($params);
		$this->assertTrue(is_wp_error($resp), "Invalid offset param fails validation");
		$this->assertSame($resp->get_error_code(), Snapshot_Controller_Full_Hub::ACTION_SCHEDULE_BACKUPS);
		$this->assertRegExp('/offset/', $resp->get_error_message());

		$params->offset = 0;
		$this->assertTrue($hub->$method($params), "Valid params pass validation");
	}

	public function test_api_schedule_application () {
		$hub = Snapshot_Controller_Full_Hub::get();
		$method = 'apply_schedule_change';
		$this->assertTrue(is_callable(array($hub, $method)), "We have the rescheduling apply method");

		$params = (object)array(
			'active' => false,
			'frequency' => 'daily',
			'time' => 100,
			'limit' => 3,
		);
		$this->assertFalse($hub->$method($params), "Reschedule applying fails without connection");

		return false;
		$dev = Mock_Wpmu_Dev_Request::start();
		if (!$dev->is_up()) return false; // No fake DEV instance running, can't proceed
		$this->assertTrue($hub->$method($params), "Valid params get properly applied");

		$model = new Snapshot_Model_Full_Backup;
		$this->assertTrue(
			$model->get_config('disable_cron'),
			"Cron deactivated"
		);
		$this->assertFalse(
			$model->get_config('frequency'),
			"Deactivated cron has no frequency"
		);
		$this->assertFalse(
			$model->get_config('schedule_time'),
			"Deactivated cron has no schedule time"
		);

		$params->active = true;
		$this->assertTrue($hub->$method($params), "Valid params get properly applied");

		$this->assertFalse(
			$model->get_config('disable_cron'),
			"Cron enabled"
		);
		$this->assertTrue(
			$model->get_config('frequency') == $params->frequency,
			"Frequency set for active cron"
		);
		$this->assertTrue(
			$model->get_config('schedule_time') == $params->time,
			"Schedule time set for active cron"
		);
	}

	public function test_api_schedule_response () {
		$hub = Snapshot_Controller_Full_Hub::get();
		$method = 'construct_schedule_response';
		$this->assertTrue(is_callable(array($hub, $method)), "We have the reschedule response construction method");

		$params = (object)array(
			'active' => true,
			'frequency' => 'daily',
			'time' => 100,
			'limit' => 1,
		);
		$response = $hub->$method($params);

		$this->assertTrue(!empty($response), "Successfully built response array");
		$this->assertTrue(
			array_key_exists('domain', $response),
			"Domain set properly"
		);
		$this->assertTrue(
			array_key_exists('backup_freq', $response),
			"Frequency set properly"
		);
		$this->assertTrue(
			array_key_exists('backup_time', $response),
			"Time set properly"
		);
		$this->assertTrue(
			array_key_exists('backup_limit', $response),
			"Limit set properly"
		);
		$this->assertTrue(
			array_key_exists('local_full_backups', $response),
			"Local backups array set properly"
		);

	}

	public function test_key_api_token_validation () {
		$hub = Snapshot_Controller_Full_Hub::get();
		$method = 'get_valid_key_token';
		$this->assertTrue(
			is_callable(array($hub, $method)),
			"We have key token validation method"
		);

		$this->assertFalse(
			$hub->$method(123),
			"Invalid args fail validation"
		);
		$this->assertFalse(
			$hub->$method(array('test' => 123)),
			"Invalid args fail validation"
		);

		$token = 'test token string';
		$this->assertEquals(
			$token, $hub->$method(array('token' => $token)),
			"Array token extraction works"
		);
		$this->assertEquals(
			$token, $hub->$method((object)array('token' => $token)),
			"Object token extraction works"
		);
	}

	public function test_set_key_from_token () {
		$hub = Snapshot_Controller_Full_Hub::get();
		$method = 'set_key_from_params_token';
		$this->assertTrue(
			is_callable(array($hub, $method)),
			"We have token => key exchanging handler"
		);

		$resp = $hub->$method(123);
		$this->assertTrue(
			is_wp_error($resp),
			"Invalid params fail"
		);
		$this->assertEquals(
			$resp->get_error_code(), Snapshot_Controller_Full_Hub::ACTION_SET_KEY,
			"Proper action parameter"
		);

		/*
		 * This won't work with new/snapshot-from-hub anymore, valid token is needed
		// Let's fake DEV request for test purposes
		$dev = Mock_Wpmu_Dev_Request::start();

		if ($dev->is_up()) {
			$rmt = Snapshot_Model_Full_Remote_Key::get();
			$rmt->drop_key();
			$tkn = 'test token string';
			$this->assertTrue(
				$hub->$method(array('token' => $tkn)),
				"Valid params amount to success"
			);
		}
		 */
	}

	public function test_api_delete_backup () {
		$hub = Snapshot_Controller_Full_Hub::get();
		$handler = 'json_' . Snapshot_Controller_Full_Hub::ACTION_DELETE_BACKUP;
		$method = 'get_valid_backup_id';
		$this->assertTrue(is_callable(array($hub, $handler)), "We can delete");
		$this->assertTrue(is_callable(array($hub, $method)), "We have the deletion validation method");
	}

	public function test_api_get_valid_backup_id () {
		$hub = Snapshot_Controller_Full_Hub::get();
		$backup_id = 'full_backup-1511591096-full-0ab0a55a.zip';

		// Not an object
		$this->assertTrue(is_wp_error($hub->get_valid_backup_id(false)));

		// Parameter not present
		$backup = $hub->get_valid_backup_id((object)array('test' => 'test'));
		$this->assertTrue(is_wp_error($backup));

		// Parameter empty
		$backup = $hub->get_valid_backup_id((object)array('backup' => ''));
		$this->assertTrue(is_wp_error($backup));

		// Not a valid backup
		$backup = $hub->get_valid_backup_id((object)array('backup' => 'test'));
		$this->assertTrue(is_wp_error($backup));

		$backup = $hub->get_valid_backup_id((object)array('backup' => $backup_id));
		$this->assertFalse(is_wp_error($backup));
		$this->assertFalse(empty($backup));

		$this->assertSame(1511591096, $backup);
	}
}
