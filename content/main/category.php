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
$category = Category::from_id(array_shift($request));
$i = 0;
?>
<h1><?=$category->name?></h1>
<table>
	<tr>
		<th>Lagrets värde</th>
		<td class="numeric"><?=number(Product::sum(array('value', '*', 'count'), array('category_id' => $category->id)))?> kr</td>
	</tr>
	<tr>
		<th>Försäljning senaste 30 dagarna</th>
		<td class="numeric"><?=number(TransactionContent::sum('amount', array('Product.category_id' => $category->id, 'Transaction.timestamp:>' => date('Y-m-d', time()-60*60*24*30))))?> kr</td>
	</tr>
	<tr>
		<th>Vinst senaste 30 dagarna</th>
		<td class="numeric"><?=number($category->revenue(date('Y-m-d', time()-60*60*24*30)))?> kr</td>
	</tr>
</table>
<?php
$products = $category->Product(array('@order' => 'name'));
require "../content/list_products.php"
?>
