<?php

/**
 * Item testing parent class override
 */
class WPMUDEVSnapshot_NoAddUpdateConfigItem extends WPMUDEVSnapshot {

	private static $_instance;

	/**
	 * Holds current item reference
	 *
	 * @var array
	 *
	 */
	private $_current_item;

	public static function instance () {
		if (empty(self::$_instance)) {
			self::$_instance = new self;
		}
		return self::$_instance;
	}

	/**
	 * Overrides the actual class method to *not* write the item
	 *
	 * It'll actually return the item instead and set internal reference
	 * so it can be used in testing.
	 *
	 * @param int $item_id Item ID (UNIX timestamp), discarded/insignificant.
	 * @param array $item Actual item.
	 *
	 * @return array
	 */
	public function add_update_config_item ($item_id, $item) {
		$this->_current_item = $item;
	}

	/**
	 * Returns internal item reference
	 *
	 * As set via `add_update_config_item`.
	 *
	 * @return array|null
	 */
	public function get_item () {
		return $this->_current_item;
	}

}
