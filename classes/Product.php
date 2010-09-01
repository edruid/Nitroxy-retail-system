<?php

class Product extends BasicObject {

	/**
	 * Used by BasicObject to determine the table name.
	 * @returns the table name for the database relation.
	 */
	protected static function table_name() {
		return 'products';
	}

	public static function from_ean($ean) {
		return self::from_field('ean', $ean);
	}

	public function __toString() {
		return $this->name;
	}
}
?>
