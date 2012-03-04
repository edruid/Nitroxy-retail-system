<?php

class Category extends BasicObject {

	/**
	 * Used by BasicObject to determine the table name.
	 * @returns the table name for the database relation.
	 */
	protected static function table_name() {
		return 'categories';
	}

	public function __toString() {
		return (string)$this->name;
	}

	public function revenue($from = null, $to = null) {
		global $db;
		if($from == null) {
			$from = '1970-01-01';
		}
		if($to == null) {
			$to = date('Y-m-d h:i:s');
		}
		$revenue = array();
		$db->prepare_fetch("
			SELECT
				SUM(`transaction_contents`.`amount` -
					`transaction_contents`.`count` * `products`.`value`
				) as revenue
			FROM
				`transaction_contents` JOIN
				`products` ON (`products`.`product_id` = `transaction_contents`.`product_id`) JOIN
				`transactions` ON (`transaction_contents`.`transaction_id` = `transactions`.`transaction_id`)
			WHERE
				`transactions`.`timestamp` > ? AND
				`transactions`.`timestamp` < ? AND
				`products`.`category_id` = ?",
			$revenue, 'ssi', $from, $to, $this->id);
		return $revenue['revenue'];
	}
}
?>
