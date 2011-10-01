<?php
require "../../includes.php";
verify_login(kickback_url('delivery'));
$user = new User($_SESSION['login']);

$ean = ClientData::post('ean');
$count = ClientData::post('count');
$purchase_price = ClientData::post('purchase_price');
$multiplyer = ClientData::post('multiplyer');
for($i = 0; $i < count($purchase_price); $i++) {
	$purchase_price[$i]*=$multiplyer;
}
$sales_price = ClientData::post('sales_price');
$name = ClientData::post('name');
$category = ClientData::post('category');
$single = ClientData::post('price_per');
if($single != 'product_type' && $single != 'each_product') {
	$errors['Pris'] = 'Inte valt pris per enskild vara/varotyp';
}
$single = $single == 'each_product';
$at_least_1_item = false;
$db->autoCommit(false);
$delivery = new Delivery();
$delivery->description = ClientData::post('description');
$delivery->user = $_SESSION['login'];
$delivery->commit();
$stock_change_amount = 0;
for($i=0; $i < count($ean); $i++) {
	if(empty($ean[$i])) {
		continue;
	}
	$at_least_1_item = true;
	try{
		$product = Product::from_ean($ean[$i]);
		if($product == null) {
			$product = new Product();
			$product->ean = $ean[$i];
		}
		$contents = new DeliveryContent();
		if($single) {
			$product->value = ($product->value * $product->count + $purchase_price[$i] * $count[$i]) / ($product->count + $count[$i]);
			$contents->cost = $purchase_price[$i];
			$stock_change_amount += $purchase_price[$i] * $count[$i];
		} else {
			$product->value = ($product->value * $product->count + $purchase_price[$i]) / ($product->count + $count[$i]);
			$contents->cost = $purchase_price[$i] / $count[$i];
			$stock_change_amount += $purchase_price[$i];
		}
		$product->count += $count[$i];
		$product->name = $name[$i];
		$product->price = $sales_price[$i];
		$product->category_id = $category[$i];
		$product->commit();
		$contents->delivery_id = $delivery->id;
		$contents->product_id = $product->id;
		$contents->count = $count[$i];
		$contents->commit();
	} catch(Exception $e) {
		$errors[$i] = $e->getMessage();;
	}
}
$transaction = new AccountTransaction();
$transaction->description = "Inköp id: {$delivery->id}";
$transaction->user = $_SESSION['login'];
$transaction->commit();

$stock = new AccountTransactionContent();
$stock->amount = $stock_change_amount;
$stock->account_id = Account::from_code_name('stock')->id;

$stock_change = new AccountTransactionContent();
$stock_change->amount = -1*$stock_change_amount;
$stock_change->account_id = Account::from_code_name('stock_change')->id;

$balance_amount = 0;
$balance_amounts = ClientData::post('amount');
$balance_accounts = ClientData::post('from_account');
for($i = 0; $i < count($balance_amounts); $i++) {
	$balance_amount += $balance_amounts[$i];
	$account = Account::from_code_name($balance_accounts[$i]);
	if($account == null && $balance_amounts[$i] != 0) {
		$errors['konton'] = 'Du måste ange vilket konto pengarna kom ifrån';
		break;
	}
	$balance = new AccountTransactionContent();
	$balance->account_id = $account->id;
	$balance->amount = -$balance_amounts[$i];
	$balance->account_transaction_id = $transaction->id;
	$balance->commit();
}
if(abs($balance_amount - $stock_change_amount) > 0.5) {
	$errors['kassa'] = 'Lagervärde av produkterna och penningåtgång stämmer inte överens. Du måste tala om vart pengarna kommer ifrån (det är ok att avrunda till närmaste krona)';
}
$purchases_account = Account::from_code_name('purchases');

$purchases = new AccountTransactionContent();
$purchases->amount = $balance_amount;
$purchases->account_id = $purchases_account->id;
$purchases->account_transaction_id = $transaction->id;
$purchases->commit();

$stock->account_transaction_id = $transaction->id;
$stock_change->account_transaction_id = $transaction->id;
$stock->commit();
$stock_change->commit();

if(empty($errors) && $at_least_1_item) {
	$db->commit();
	kick('/view_delivery/'.$delivery->id);
} else {
	$_SESSION['_POST'] = $_POST;
	foreach($errors as $index => $error) {
		Message::add_error("Rad $index: $error");
	}
	kick('delivery');
}
?>
