<?php

class Product extends BasicObject {
	private $_old_price;
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
		switch($key) {
			case 'price':
				if($this->_exists && isset($this->_old_price)) {
					$this->_old_price = $this->price;
				}
		}
		if($value === '') {
			$value = null;
		}
		return parent::__set($key, $value);
	}

	public function commit() {
		global $db;
		$ac = $db->is_autocommit();
		if($ac) {
			$db->autocommit(false);
		}
		if(isset($this->_old_price) && $this->_old_price != $this->price) {
			$log = new ProductLog();
			$log->user_id = $_SESSION['login'];
			$log->old_price = $this->_old_price;
			$log->new_price = $this->price;
			$log->product_id = $this->id;
			$log->commit();
		}
		parent::commit();
		if($ac) {
			$db->commit();
			$db->autocommit(true);
		}
	}
}
?>
