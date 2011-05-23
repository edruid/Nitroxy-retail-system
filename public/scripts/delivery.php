<?
require "../../includes.php";
if(empty($_SESSION['login'])) {
	$_SESSION['_POST'] = $_POST;
	kick('login?kickback='.htmlspecialchars(kickback_url('delivery')));
}
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
$delivery->user = $user->__toString();
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
$from_till = ClientData::post('from_till');
$stock_account = Account::from_code_name('stock');
$stock_change_account = Account::from_code_name('stock_change');
$transaction = new AccountTransaction();
$transaction->description = "Inköp id: {$delivery->id}";
$transaction->user = $user->__toString();

$stock = new AccountTransactionContent();
$stock->amount = $stock_change_amount;
$stock->account_id = $stock_account->id;

$stock_change = new AccountTransactionContent();
$stock_change->amount = -1*$stock_change_amount;
$stock_change->account_id = $stock_change_account->id;
if(!is_numeric($from_till)) {
	$errors['kassa'] = 'Inte fyllt i hur mycket du tagit från kassan';
} else if($from_till != 0) {
	// TODO: book keeping should always be done.
	// TODO: should be possible to choose from_account.
	$money_source_account = Account::from_code_name('till');
	$purchases_account = Account::from_code_name('purchases');

	$money_source = new AccountTransactionContent();
	$money_source->amount = -1*$from_till;
	$money_source->account_id = $money_source_account->id;

	$purchases = new AccountTransactionContent();
	$purchases->amount = $from_till;
	$purchases->account_id = $purchases_account->id;

}
$transaction->commit();

if($from_till != 0) {
	$money_source->account_transaction_id = $transaction->id;
	$purchases->account_transaction_id = $transaction->id;
	$money_source->commit();
	$purchases->commit();
}
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
