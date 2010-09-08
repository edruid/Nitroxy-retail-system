<?
require "../../includes.php";
if(empty($_SESSION['login'])) {
	$_SESSION['_POST'] = $_POST;
	kick('login?kickback='.htmlspecialchars("https://{$_SERVER['HTTP_HOST']}/delivery"));
}
$user = new User($_SESSION['login']);

$ean = post_get('ean');
$count = post_get('count');
$purchase_price = post_get('purchase_price');
$sales_price = post_get('sales_price');
$name = post_get('name');
$category = post_get('category');
$single = post_get('single');
$at_least_1_item = false;
$db->autoCommit(false);
$delivery = new Delivery();
$delivery->description = post_get('description');
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
