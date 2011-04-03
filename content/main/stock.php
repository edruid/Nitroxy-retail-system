<?php
if(empty($_SESSION['login'])) {
	kick('login?kickback='.htmlspecialchars(kickback_url()));
}
function mark($bool) {
	if($bool) {
		return ' marked';
	} else {
		return '';
	}
}
$products = Product::selection(array(
	'@custom_order' => '`products`.`count` > 0 DESC',
	'@order' => array(
		'Category.name',
		'name',
	),
	'category_id:!=' => 0,
));
$i = 0;
$revenue = array();
$db->prepare_fetch("
	SELECT
		SUM(`transaction_contents`.`amount` -
			`transaction_contents`.`count` * `products`.`value`
		) as revenue
	FROM
		`transaction_contents` JOIN
		`products` ON (`products`.`product_id` = `transaction_contents`.`product_id`) JOIN
		`transactions` ON (`transaction_contents`.`transaction_id` = `transactions`.`transaction_id`)
	WHERE
		`transactions`.`timestamp` > ?",
	$revenue, 's', date('Y-m-d', time()-60*60*24*30));

?>
<table>
	<tr>
		<th>Lagrets värde</th>
		<td class="numeric"><?=number(Product::sum(array('value', '*', 'count')))?> kr</td>
	</tr>
	<tr>
		<th>Försäljning senaste 30 dagarna</th>
		<td class="numeric"><?=number(TransactionContent::sum('amount', array('Transaction.timestamp:>' => date('Y-m-d', time()-60*60*24*30))))?> kr</td>
	</tr>
	<tr>
		<th>Vinst senaste 30 dagarna</th>
		<td class="numeric"><?=number($revenue['revenue'])?> kr</td>
	</tr>
</table>
<? require "../content/list_products.php" ?>
