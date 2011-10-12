<?php

class Account extends BasicObject {

	/**
	 * Used by BasicObject to determine the table name.
	 * @returns the table name for the database relation.
	 */
	protected static function table_name() {
		return 'account';
	}

	public function __toString() {
		return (string)$this->name;
	}

	public static function from_code_name($name) {
		return parent::from_field('code_name', $name);
	}

	public function __get($key) {
		switch($key) {
			case 'balance':
				return AccountTransactionContent::sum('amount', array(
					'account_id' => $this->id
				));
			default:
				return parent::__get($key);
		}
	}
}
?>
