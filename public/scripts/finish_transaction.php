<?php
require "../../includes.php";
$sum=0;
$recieved=ClientData::post("recieved");
$product_prices = ClientData::post("product_price");
$product_counts = ClientData::post("product_count");

$db->autoCommit(false);

$transaction = new Transaction();
$transaction->amount = 0;
$contents = array();
foreach(ClientData::post("product_id") as $i => $product_id) {
	$product = Product::from_id($product_id);
	if(!$product) {
		die("Produkten med id {$product_id} finns inte");
	}
	if($product->price != $product_prices[$i]) {
		die("{$product->name} har ändrat pris sen formuläret laddades. Backa och försök igen. Gammalt pris: {$product->price} kr nytt pris {$product_prices[$i]}");
	}
	$count = $product_counts[$i];
	$amount=$product->price * $count;
	$transaction->amount += $amount;
	$product->sell($count);
	$transaction_content = new TransactionContent();
	$transaction_content->product_id = $product->id;
	$transaction_content->count = $count;
	$transaction_content->amount = $amount;
	$transaction_content->stock_usage = $count * $product->value;
	$contents[] = $transaction_content;
}
$sum = $transaction->amount;
$diff = abs(round($sum) - $sum);
if($diff != 0) {
	$transaction_content = new TransactionContent();
	$transaction_content->product_id = 0;
	$transaction_content->count = 1;
	$transaction_content->amount = $diff;
	$contents[] = $transaction_content;
	$transaction->amount+=$diff;
}

if($transaction->amount > $recieved) {
	die("Det är för lite betalt. $transaction->amount < $recieved");
}
$transaction->commit();
foreach($contents as $content) {
	$content->transaction_id = $transaction->id;
	$content->commit();
}
if(isset($_SESSION['random']) && $_SESSION['random'] == ClientData::post('random')) {
	die('Form was already submitted');
}
$_SESSION['random'] = ClientData::post('random');
$db->commit();
kick("retail?last_recieved=$recieved");

?>
