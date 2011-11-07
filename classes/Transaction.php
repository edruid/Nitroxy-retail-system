<?php

class Transaction extends BasicObject {

	private $contents = array();
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

	public function add_content($product_id, $price, $count) {
		$product = Product::from_id($product_id);
		if(!$product) {
			throw new Exception("Produkten med id {$product_id} finns inte");
		}
		if($product->price != $price && $product_id != 0) {
			throw new Exception("{$product->name} har ändrat pris sen formuläret laddades. Försök igen! Gammalt pris: {$product->price} kr nytt pris {$product_prices[$i]}");
		}
		$product->sell($count);
		$content = new TransactionContent();
		$content->product_id = $product->id;
		$content->count = $count;
		$content->amount = $price * $count;
		$content->stock_usage = $count * $product->value;
		$this->amount += $content->amount;
		$this->contents[] = $content;
	}

	public function commit(){
		parent::commit();
		foreach($this->contents as $content) {
			$content->transaction_id = $this->id;
			$content->commit();
		}
	}
}
?>

