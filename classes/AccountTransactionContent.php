<?php

class AccountTransactionContent extends BasicObject {

	/**
	 * Used by BasicObject to determine the table name.
	 * @returns the table name for the database relation.
	 */
	protected static function table_name() {
		return 'account_transaction_contents';
	}

	public static function sum($field, $params=array()) {
		$sum = parent::sum($field, $params);
		if(!isset($params['account_id']) || $params['account_id'] == 1) {
			unset($params['account_id']);
			$sum += TransactionContent::sum($field, $params);
		}
		return $sum;
	}
}
?>

