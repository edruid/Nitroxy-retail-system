<pre>
<?php
require "../../includes.php";
var_dump($_POST);
$sum=0;
$recieved=ClientData::post("recieved");
$product_prices = ClientData::post("product_price");
$product_counts = ClientData::post("product_count");

$db->autoCommit(false);

$transaction = new Transaction();
$transaction->amount = 0;
$transaction->commit();
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
	$transaction_content->transaction_id = $transaction->id;
	$transaction_content->product_id = $product->id;
	$transaction_content->count = $count;
	$transaction_content->amount = $amount;
	$transaction_content->commit();
}
$sum = $transaction->amount;
$diff = abs(round($sum) - $sum);
if($diff != 0) {
	$transaction_content = new TransactionContent();
	$transaction_content->transaction_id = $transaction->id;
	$transaction_content->product_id = 0;
	$transaction_content->count = 1;
	$transaction_content->amount = $diff;
	$transaction_content->commit();
	$transaction->amount+=$diff;
}

if($transaction->amount < $recieved) {
	die("Det är för lite betalt.");
}
$transaction->commit();

$db->commit();
kick("retail?last_recieved=$recieved");

?>
