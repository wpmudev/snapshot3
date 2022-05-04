<?php

/**
 * @group model
 * @group storage
 */
class StorageSitemetaTest extends WP_UnitTestCase {

	public function test_namespace() {
		$model = new Snapshot_Model_Storage_Sitemeta();
		$this->assertEquals( Snapshot_Model_Storage::DEFAULT_NAMESPACE, $model->get_namespace() );

		$namespace = 'test_namespace';
		$model->set_namespace($namespace);
		$this->assertEquals(
			Snapshot_Model_Storage::DEFAULT_NAMESPACE . "-{$namespace}",
			$model->get_namespace()
		);

		$model = new Snapshot_Model_Storage_Sitemeta( $namespace );
		$this->assertEquals(
			Snapshot_Model_Storage::DEFAULT_NAMESPACE . "-{$namespace}",
			$model->get_namespace()
		);

	}
}
