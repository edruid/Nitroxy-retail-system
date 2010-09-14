<?php
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
?>
<table class="alternate">
	<thead>
		<tr>
			<th class="name_column"><a href="#" onclick="javascript:sort(this.parentNode.parentNode.parentNode.parentNode, 0); return false;">Namn</a></th>
			<th class="value_column"><a href="#" onclick="javascript:sort(this.parentNode.parentNode.parentNode.parentNode, 1); return false;">Värde/st</a></th>
			<th class="price_column"><a href="#" onclick="javascript:sort(this.parentNode.parentNode.parentNode.parentNode, 2); return false;">Försäljningspris</a></th>
			<th class="count_column"><a href="#" onclick="javascript:sort(this.parentNode.parentNode.parentNode.parentNode, 3); return false;">Lager</a></th>
			<th class="value_column"><a href="#" onclick="javascript:sort(this.parentNode.parentNode.parentNode.parentNode, 4); return false;">Lagervärde</a></th>
			<th class="ean_column"><a href="#" onclick="javascript:sort(this.parentNode.parentNode.parentNode.parentNode, 5); return false;">EAN</a></th
			<th class="category_column"><a href="#" onclick="javascript:sort(this.parentNode.parentNode.parentNode.parentNode, 6); return false;">Kategori</a></th>
		</tr>
	</thead>
	<tbody>
		<? foreach($products as $product): ?>
			<tr>
				<td class="name_column"><a href="product/<?=$product->id?>"><?=$product->name?></a></td>
				<td class="value_column"><?=$product->value?></td>
				<td class="price_column"><?=$product->price?></td>
				<td class="count_column"><?=$product->count?></td>
				<td class="value_column"><?=$product->count * $product->value?></td>
				<td class="ean_column"><?=$product->ean?></td>
				<td class="category_column"><?=$product->Category->name?></td>
			</tr>
		<? endforeach ?>
	</tbody>
</table>

