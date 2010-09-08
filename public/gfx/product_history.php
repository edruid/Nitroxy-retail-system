<?php
require "../../includes.php";
$result = array(&$count, &$date, &$product_id);
$stmt = $db->prepare_full("
	SELECT
		SUM(`transaction_contents`.`count`),
		DATE(`transactions`.`timestamp`) as d,
		product_id
	FROM
		`transaction_contents` JOIN
		`transactions` ON (`transaction_contents`.`transaction_id` = `transactions`.`transaction_id`)
	WHERE
		`transactions`.`timestamp` > ? AND
		`transaction_contents`.`product_id` in (?)
	GROUP BY
		DATE(`transactions`.`timestamp`), product_id
	ORDER BY
		product_id,
		d",
	$result,
	'si',
	(date('Y')-1).date('-m-d'),
	ClientData::request('id')
);
$image = new Graph();
$image->slide = 7;
$image->period = 365;
$image->height = 200;
$image->width = 730;
$image->vertical_lable = 5;
$start = gregoriantojd(date('m'), date('d'), date('Y')-1);
$old_product_id = null;
while($stmt->fetch()) {
	if($old_product_id != $product_id) {
		$product = Product::from_id($product_id);
	}
	$s_date = explode('-', $date);
	$image->add($count, gregoriantojd($s_date[1], $s_date[2], $s_date[0])-$start, $product->name);
}
$image->draw();
?>
