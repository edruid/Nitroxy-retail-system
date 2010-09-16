<?php
function mark($bool) {
	if($bool) {
		return 'style="background-color: red;"';
	} else {
		return '';
	}
}
if(empty($_SESSION['login'])) {
	kick('login?kickback='.htmlspecialchars(kickback_url()));
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
?>
<table class="alternate hidable" id="products">
	<thead>
		<tr>
			<th class="name_column"><a href="#" onclick="sort.sort(<?=$i++?>, sort.tagInsensitiveComparator);return false">Namn</a></th>
			<th class="value_column"><a href="#" onclick="sort.sort(<?=$i++?>);return false">Värde/st</a></th>
			<th class="price_column"><a href="#" onclick="sort.sort(<?=$i++?>);return false">Försäljningspris</a></th>
			<th class="count_column"><a href="#" onclick="sort.sort(<?=$i++?>);return false">Lager</a></th>
			<th class="total_value_column"><a href="#" onclick="sort.sort(<?=$i++?>);return false">Lagervärde</a></th>
			<th class="ean_column"><a href="#" onclick="sort.sort(<?=$i++?>);return false">EAN</a></th>
			<th class="category_column"><a href="#" onclick="sort.sort(<?=$i++?>, sort.tagInsensitiveComparator);return false">Kategori</a></th>
			<th class="revenue"><a href="#" onclick="sort.sort(<?=$i++?>);return false">Vinst kr</a></th>
			<th class="revenue_p"><a href="#" onclick="sort.sort(<?=$i++?>);return false">Vinst %</a></th>
			<th class="threshold"><a href="#" onclick="sort.sort(<?=$i++?>);return false">Min buffert</a></th>
			<th class="threshold_diff"><a href="#" onclick="sort.sort(<?=$i++?>);return false">Buffert diff</a></th>
			<th class="sales"><a href="#" onclick="sort.sort(<?=$i++?>);return false">sålda 30 dag</a></th>
		</tr>
	</thead>
	<tbody>
		<? foreach($products as $product): ?>
			<tr>
				<td class="name_column"><a href="product/<?=$product->id?>"><?=$product->name?></a></td>
				<td class="value_column"><?=$product->value?> kr</td>
				<td class="price_column"><?=$product->price?> kr</td>
				<td class="count_column"><?=$product->count?> st</td>
				<td class="total_value_column"><?=$product->count * $product->value?> kr</td>
				<td class="ean_column"><?=$product->ean?></td>
				<td class="category_column"><?=$product->Category->name?></td>
				<td class="revenue" <?=mark(($diff = $product->price-$product->value)<=0)?>><?=$diff?> kr</td>
				<td class="revenue_p" <?=mark(($markup=round($diff/$product->price*100))<20)?>><?=$markup?> %</td>
				<td class="threshold"><?=$product->inventory_threshold?> st</td>
				<td class="threshold_diff" <?=mark(($t_diff=$product->count-$product->inventory_threshold)<=0 && $product->inventory_threshold != null)?>><?=$t_diff?> st</td>
				<td class="sales"><?=TransactionContent::sum('count', array('product_id' => $product->id, 'Transaction.timestamp:>' => date('Y-m-d', time()-60*60*24*30)))?> st</td>
			</tr>
		<? endforeach ?>
	</tbody>
</table>

<script type="text/javascript">
var sort;
function startSort() {
	sort=new Sort();
	sort.table = document.getElementById('products');
	sort.defaultComparator = sort.numericComparator;
}

window.addEventListener ?
	window.addEventListener('load', startSort, false) :
	window.attachEvent('onload', startSort);

</script>
