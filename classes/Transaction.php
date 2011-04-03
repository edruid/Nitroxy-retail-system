<?php

class Transaction extends BasicObject {

	/**
	 * Used by BasicObject to determine the table name.
	 * @returns the table name for the database relation.
	 */
	protected static function table_name() {
		return 'transactions';
	}

	public static function last() {
		$transaction = static::selection(array(
			'@order' => 'transaction_id:desc',
			'@limit' => 1,
		));
		if(count($transaction) == 0) {
			return null;
		}
		return $transaction[0];
	}
}
?>

