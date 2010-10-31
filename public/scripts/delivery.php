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
		} else {
			$product->value = ($product->value * $product->count + $purchase_price[$i]) / ($product->count + $count[$i]);
			$contents->cost = $purchase_price[$i] / $count[$i];
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
if(!is_numeric($from_till)) {
	$errors['kassa'] = 'Inte fyllt i hur mycket du tagit från kassan';
} else if($from_till != 0) {
	$transaction = new AccountTransaction();
	$transaction->description = "Inköp id: {$delivery->id}";
	$transaction->user = $user->__toString();
	$from = new AccountTransactionContent();
	$from->amount = -1*$from_till;
	$from->account_id = 1; // Kassa
	$to = new AccountTransactionContent();
	$to->amount = $from_till;
	$to->account_id = 3; // Inköp
	$transaction->commit();
	$from->account_transaction_id = $transaction->id;
	$to->account_transaction_id = $transaction->id;
	$from->commit();
	$to->commit();
}

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
