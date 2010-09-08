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
			<th>Namn</th>
			<th>Värde/st</th>
			<th>Försäljningspris</th>
			<th>Lager</th>
			<th>Lagervärde</th>
			<th>EAN</th>
			<th>Kategori</th>
		</tr>
	</thead>
	<tbody>
		<? foreach($products as $product): ?>
			<tr>
				<td><a href="product/<?=$product->id?>"><?=$product->name?></a></td>
				<td><?=$product->value?></td>
				<td><?=$product->price?></td>
				<td><?=$product->count?></td>
				<td><?=$product->count * $product->value?></td>
				<td><?=$product->ean?></td>
				<td><?=$product->Category->name?></td>
			</tr>
		<? endforeach ?>
	</tbody>
</table>

