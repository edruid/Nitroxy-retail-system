<?php

class AccountTransaction extends BasicObject {

	/**
	 * Used by BasicObject to determine the table name.
	 * @returns the table name for the database relation.
	 */
	protected static function table_name() {
		return 'account_transaction';
	}

	public function __toString() {
		return $this->timestamp;
	}

	public function add_contents($contents) {
		if(array_sum($contents) != 0) {
			throw new Exception("Account contents do not balance");
		}
		foreach($contents as $account => $amount) {
			$content = new AccountTransactionContent();
			$content->account_transaction_id = $this->id;
			$content->account_id = Account::from_code_name($account);
			if($content->account_id === null) {
				throw new Exception("No such account $account");
			}
			$content->amount = $amount;
			$content->commit();
		}
	}
}
?>
