<?php

/**
 * @group api
 * @group model
 */

class ApiTest extends WP_UnitTestCase {

	public function test_exists() {
		$this->assertTrue(
			class_exists( 'Snapshot_Model_Full_Remote_Api' )
		);
	}

	public function setUp() {
		$this->_model = Snapshot_Model_Full_Remote_Api::get();
	}

	public function test_get_dashboard_api_key() {
		$key = $this->_model->get_dashboard_api_key();
		$this->assertTrue( empty( $key ), 'Empty value on vanilla install' );

		$option_key = 'test 1';
		update_site_option( 'wpmudev_apikey', $option_key );
		$key = $this->_model->get_dashboard_api_key();
		$this->assertFalse( empty( $key ), 'API key in options is set' );
		if ( ! defined( 'WPMUDEV_APIKEY' ) ) {
			$this->assertEquals( $option_key, $key, 'API key gets taken from options' );
		}

		$define_key = defined( 'WPMUDEV_APIKEY' ) ? WPMUDEV_APIKEY : 'test 2';
		if ( ! defined( 'WPMUDEV_APIKEY' ) ) {
			define( 'WPMUDEV_APIKEY', $define_key );
		}
		$key = $this->_model->get_dashboard_api_key();
		$this->assertFalse( empty( $key ), 'API key in define is set' );
		$this->assertEquals( $define_key, $key, 'API key gets taken from options' );

		add_filter( $this->_model->get_filter( 'api_key' ), array( $this, 'get_test_key_string' ) );
		$filter_key = $this->get_test_key_string();
		$key = $this->_model->get_dashboard_api_key();
		$this->assertFalse( empty( $key ), 'API key in filter is set' );
		$this->assertEquals( $filter_key, $key, 'API key gets taken from filter' );
		remove_filter( $this->_model->get_filter( 'api_key' ), array( $this, 'get_test_key_string' ) );

		delete_site_option( 'wpmudev_apikey', $option_key );
	}

	public function get_test_key_string() {
		return 'test dash api key';
	}
}
