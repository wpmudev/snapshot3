<?php

$_tests_dir = getenv( 'WP_TESTS_DIR' );
if ( ! $_tests_dir ) {
	$_tests_dir = '/tmp/wordpress-tests-lib';
}

require_once dirname( __FILE__ ) . '/stubs/functions.php';

require_once $_tests_dir . '/includes/functions.php';

function _manually_load_plugin() {
	require dirname( dirname( __FILE__ ) ) . '/snapshot.php';
}
tests_add_filter( 'muplugins_loaded', '_manually_load_plugin' );

require $_tests_dir . '/includes/bootstrap.php';

if (!function_exists('localhost_dbg')) {
	function localhost_dbg () {
		die(var_export(func_get_args()));
	}
}

if (!class_exists('Mock_Wpmu_Dev_Request')) {
	class Mock_Wpmu_Dev_Request {

		public static function start () {
			$me = new self;

			$me->_fake_dev_request();

			return $me;
		}

		public function stop () {
			$remote = new Snapshot_Model_Full_Remote;
			remove_filter($remote->get_filter('api_key'), array($this, 'get_api_key'));
			remove_filter($remote->get_filter('domain'), array($this, 'get_api_domain'));
		}

		private function _fake_dev_request () {
			if (!defined('WPMUDEV_CUSTOM_API_SERVER')) {
				define('WPMUDEV_CUSTOM_API_SERVER', 'https://premium.wpmudev.dev');
			}

			$remote = new Snapshot_Model_Full_Remote;
			add_filter($remote->get_filter('api_key'), array($this, 'get_api_key'), 999);
			add_filter($remote->get_filter('domain'), array($this, 'get_api_domain'), 999);
		}

		public function get_api_key () {
			return '74baf8f7c0f14b243f8dc6dc0339b9d0d58751f9';
		}

		public function get_api_domain () {
			return 'http://53.dev/ms1/';
		}

		public function is_up () {
			if (!defined('WPMUDEV_CUSTOM_API_SERVER')) return false;
			$resp = wp_remote_head(WPMUDEV_CUSTOM_API_SERVER, array('sslverify' => false));
			return !is_wp_error($resp);
		}
	}
}
