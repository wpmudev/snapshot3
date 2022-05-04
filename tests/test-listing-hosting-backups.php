<?php
/**
 * @group hosting
 */

class Listing_Hosting_Backups extends WP_UnitTestCase {

	const SITE_ID = 'mocked_wpmudev-hosted';
	const RANDOM_SUFFIX = 'RndSfX';

	private $_hosting_controller;

	public function setUp () {
		if ( ! defined( 'WPMUDEV_HOSTING_SITE_ID' ) ) {
			define( 'WPMUDEV_HOSTING_SITE_ID', 'leo1111' );
		}
		$this->_hosting_controller = Snapshot_Controller_Hosting::get();
	}

	public function test_exists() {
		$this->assertTrue(
			class_exists( 'Snapshot_Controller_Hosting' )
		);
	}

	public function test_deal_with_listing_backups() {
		$raw_backups = $this->get_mocked_backups(1);

		$backups_listing = $this->_hosting_controller->deal_with_listing_backups( $raw_backups, false);
		foreach ( $backups_listing as $backup_listing ) {
			$this->assertInternalType( 'array', $backup_listing );
			$this->assertNotEmpty( $backup_listing );
			
			$this->assertArrayHasKey( 'Key', $backup_listing );
			$this->assertArrayHasKey( 'creation_time', $backup_listing );
			$this->assertArrayHasKey( 'domain', $backup_listing );
			$this->assertArrayHasKey( 'id', $backup_listing );
			$this->assertArrayHasKey( 'link', $backup_listing );
			$this->assertArrayHasKey( 'context', $backup_listing );
			$this->assertArrayHasKey( 'type', $backup_listing );
			$this->assertArrayHasKey( 'icon', $backup_listing );
			$this->assertArrayHasKey( 'tooltip', $backup_listing );

			$this->assertEquals( 'Daily, @ 2:00am', $backup_listing['context'] );
			$this->assertEquals( 'Hosting', $backup_listing['type'] );
			$this->assertEquals( 'i-cloud-hosting', $backup_listing['icon'] );
			$this->assertEmpty( $backup_listing['tooltip'] );
		}
	}

	public function test_deal_with_listing_backups_dashboard() {
		$raw_backups = $this->get_mocked_backups(1);

		$backups_listing = $this->_hosting_controller->deal_with_listing_backups( $raw_backups, true);
		foreach ( $backups_listing as $backup_listing ) {
			$this->assertInternalType( 'array', $backup_listing );
			$this->assertNotEmpty( $backup_listing );
			
			$this->assertArrayHasKey( 'Key', $backup_listing );
			$this->assertArrayHasKey( 'creation_time', $backup_listing );
			$this->assertArrayHasKey( 'domain', $backup_listing );
			$this->assertArrayHasKey( 'id', $backup_listing );
			$this->assertArrayHasKey( 'link', $backup_listing );
			$this->assertArrayHasKey( 'context', $backup_listing );
			$this->assertArrayHasKey( 'type', $backup_listing );
			$this->assertArrayHasKey( 'icon', $backup_listing );
			$this->assertArrayHasKey( 'tooltip', $backup_listing );
			$this->assertArrayNotHasKey( 'menu', $backup_listing );

			$this->assertEquals( 'Daily, @ 2:00am', $backup_listing['context'] );
			$this->assertEquals( 'Hosting', $backup_listing['type'] );
			$this->assertEquals( 'i-cloud-hosting', $backup_listing['icon'] );
			$this->assertEmpty( $backup_listing['tooltip'] );
		}
	}

	function get_mocked_backups ( $backup_number ) {
		$counter = 0;
		$backups = array();
		while ( $counter < $backup_number ) {
			$backups[] = array(
				'Key' => self::SITE_ID . '/wpmudev/mysql/site@prod_incremental_' . date( 'YmdHis' ) . self::RANDOM_SUFFIX,
				'creation_time' => date( 'c' ),
				'domain' => self::SITE_ID . 'wpmudev.host',
				'context' => 'Nightly',
			);
			$counter++;
		}

		return $backups;
	}
}
