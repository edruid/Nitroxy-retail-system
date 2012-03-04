<?php

class DeliveryContent extends BasicObject {

	/**
	 * Used by BasicObject to determine the table name.
	 * @returns the table name for the database relation.
	 */
	protected static function table_name() {
		return 'delivery_contents';
	}

	public function __set($key, $value) {
		if($value === '') {
			throw new Exception("Missing value for $key");
		}
		return parent::__set($key, $value);
	}
}
?>
