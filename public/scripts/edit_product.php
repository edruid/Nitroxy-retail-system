<?php
require "../../includes.php";
verify_login(kickback_url("edit_product/".ClientData::post('product')));
$product = Product::from_id(ClientData::post('product'));
$fields = array(
	'name',
	'active',
	'price',
	'value',
	'ean',
	'category_id',
	'inventory_threshold',
);
foreach($fields as $field) {
	$product->$field = ClientData::post($field);
}
$product->commit();
kick('product/'.$product->id);
?>
