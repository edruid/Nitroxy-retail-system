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

	public function sell($count) {
		$this->count -= $count;
		foreach(ProductPackage::selection(array('package' => $this->id)) as $package) {
			$product = $package->Product();
			$product->sell($count * $package->count);
		}
		$this->commit();
	}

	public function __toString() {
		return $this->name;
	}

	public function __set($key, $value) {
		if($value === '') {
			$value = null;
		}
		return parent::__set($key, $value);
	}
}
?>
