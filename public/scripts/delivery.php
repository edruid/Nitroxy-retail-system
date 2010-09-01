<?
require "../../includes.php";

$ean = request_get('ean');
$count = request_get('count');
$purchase_price = request_get('purchase_price');
$sales_price = request_get('sales_price');
$name = request_get('name');
$category = request_get('category');
$at_least_1_item = false;
$db->autoCommit(false);
$delivery = new Delivery();
$delivery->description = post_get('description');
$delivery->commit();
for($i=0; $i < count($ean); $i++) {
	if(empty($count[$i])) {
		continue;
	}
	$at_least_1_item = true;
	try{
		$product = Product::from_ean($ean[$i]);
		if($product == null) {
			$product = new Product();
			$product->ean = $ean[$i];
		}
		$product->value = ($product->value * $product->count + $purchase_price[$i] * $count[$i]) / ($product->count + $count[$i]);
		$product->count += $count[$i];
		$product->name = $name[$i];
		$product->price = $sales_price[$i];
		$product->category_id = $category[$i];
		$product->commit();
		$delivery->add_contents($product, $count[$i], $purchase_price[$i]);
	} catch(Exception $e) {
		$errors[$i] = $e->getMessage();;
	}
}
if(empty($errors) && $at_least_1_item) {
	$db->commit();
	echo "OK";
} else {
	$_SESSION['_POST'] = $_POST;
	foreach($errors as $index => $error) {
		Message::add_error("Rad $index: $error");
	}
	kick('delivery');
}
?>
