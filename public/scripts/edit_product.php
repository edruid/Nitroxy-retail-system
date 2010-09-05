<?php
require "../../includes.php";
if(empty($_SESSION['login'])) {
	kick('login?kickback='.htmlspecialchars("https://{$_SERVER['HTTP_HOST']}/edit_product/".post_get('product')));
}
$product = Product::from_id(post_get('product'));
$fields = array(
	'name',
	'price',
	'value',
	'count',
	'ean',
	'category_id',
);
foreach($fields as $field) {
	$product->$field = post_get($field);
}
$product->commit();
kick('product/'.$product->id);
?>
