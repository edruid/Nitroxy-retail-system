<?php

class AccountTransactionContent extends BasicObject {

	/**
	 * Used by BasicObject to determine the table name.
	 * @returns the table name for the database relation.
	 */
	protected static function table_name() {
		return 'account_transaction_contents';
	}

	public function __set($key, $value) {
		switch($key) {
			case 'account_id':
				if(!is_numeric($value) || $value <= 0) {
					throw new Exception("Du måste ange ett giltigt konto ('$value' är inte giltigt).");
				}
				break;
		}
		parent::__set($key, $value);
	}
}
?>
