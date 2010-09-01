<?php
$products = Product::selection(array(
	'@custom_order' => '`products`.`count` > 0 DESC',
	'@order' => array(
		'Category.name',
		'name',
	),
));
?>
<table>
	<thead>
		<tr>
			<th>Namn</th>
			<th>Värde</th>
			<th>Pris</th>
			<th>Lager</th>
			<th>Totalt värde</th>
			<th>EAN</th>
			<th>Kategori</th>
		</tr>
	</thead>
	<tbody>
		<? foreach($products as $product): ?>
			<tr>
				<td><?=$product->name?></td>
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

