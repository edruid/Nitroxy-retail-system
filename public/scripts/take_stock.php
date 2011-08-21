<?php
require "../../includes.php";
if(empty($_SESSION['login'])) {
	kick('login?kickback='.htmlspecialchars(kickback_url()));
}
$db->autoCommit(false);
$products = ClientData::post('product_id');
$counts = ClientData::post('product_count');
$money_diff = 0;
$delivery = new Delivery();
$delivery->description = "Inventering";
$delivery->user = $_SESSION['login'];
$delivery->commit();
foreach($products as $i => $product_id) {
	// Create purchase
	$product = Product::from_id($product_id);
	$diff = $counts[$i] - $product->count;
	$money_diff += $diff * $product->value;
	$product->count = $counts[$i];
	$product->commit();
	$contents = new DeliveryContent();
	$contents->cost = 0;
	$contents->delivery_id = $delivery->id;
	$contents->product_id = $product_id;
	$contents->count = $diff;
	var_dump($contents->count);
	$contents->commit();
}
if($money_diff != 0) {
	$from_account = Account::from_code_name('stock_diff');
	$to_account = Account::from_code_name('stock');
	$transaction = new AccountTransaction();
	$transaction->description="inventering: {$delivery->id}";
	$transaction->user = $_SESSION['login'];
	$from = new AccountTransactionContent();
	$from->amount = $money_diff;
	$from->account_id = $from_account->id;
	$to = new AccountTransactionContent();
	$to->amount = -$money_diff;
	$to->account_id = $to_account->id;
	$transaction->commit();
	$from->account_transaction_id = $transaction->id;
	$to->account_transaction_id = $transaction->id;
	$from->commit();
	$to->commit();
}
$db->commit();
kick('/view_delivery/'.$delivery->id);

?>
