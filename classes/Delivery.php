<?php

class Delivery extends BasicObject {

	/**
	 * Used by BasicObject to determine the table name.
	 * @returns the table name for the database relation.
	 */
	protected static function table_name() {
		return 'deliveries';
	}

	public function add_contents($product, $count, $price) {
		global $db;
		$stmt = $db->prepare("
			INSERT INTO `delivery_contents`
			SET
				`delivery_id` = ?,
				`product_id` = ?,
				`count` = ?,
				`cost` = ?");
		$product_id = $product->id;
		$delivery_id = $this->id;
		$stmt->bind_param('iiid', $delivery_id, $product_id, $count, $price);
		$stmt->execute();
		$stmt->close();
	}
}
?>

