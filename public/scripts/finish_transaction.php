<?php
require "../../includes.php";

$sum=request_get("sum");
$recieved=request_get("recieved");
$change=request_get("change");
$diff=request_get("diff");

if($change != $recieved-$sum) {
	die("Växel är felaktig");
}
if($recieved < $sum) {
	die("För lite betalt");
}

$contents=request_get("contents");

$contents=trim($contents);

$contents=explode("\n",$contents);

$basket=array();
foreach($contents as $row) {
	$row=trim($row);
	$parts=explode(":",$row);
	if(count($parts)!=2) {
		echo "Invalid row \"$row\"\n";
		die();
	}
	$art_no=$parts[0];
	$count=$parts[1];
	$basket[$art_no]=$count;
}

$db->autoCommit(false);

$transaction = new Transaction();
$transaction->amount = 0;
$transaction->commit();

foreach($basket as $id => $count) {
	$product = Product::from_id($id);
	$amount=$product->price*$count;
	$transaction->amount+=$amount;
	$product->count -= $count;
	$product->commit();
	$transaction_content = new TransactionContent();
	$transaction_content->transaction_id = $transaction->id;
	$transaction_content->product_id = $product->id;
	$transaction_content->count = $count;
	$transaction_content->amount = $amount;
	$transaction_content->commit();
}
$transaction_content = new TransactionContent();
$transaction_content->transaction_id = $transaction->id;
$transaction_content->product_id = 0;
$transaction_content->count = 1;
$transaction_content->amount = $diff;
$transaction_content->commit();
$transaction->amount+=$diff;
$transaction->commit();

if($transaction->amount != $sum || -0.5 >= $diff || $diff > 0.5 ) {
	die("Klienten har räknat fel eller priser har ändrats sedan klienten hämtade dem. $diff $sum $transaction->amount");
}
$db->commit();
kick("retail?last_sum=$sum&last_recieved=$recieved&last_change=$change");

?>
