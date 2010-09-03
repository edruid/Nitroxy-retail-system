<?php
$product = Product::from_id(array_shift($request));
if(!$product) {
	die('unknown product');
}
?>
<table>
	<tr>
		<th>Namn</th>
		<td><?=$product->name?></td>
	</tr>
	<tr>
		<th>Pris</th>
		<td><?=$product->price?></td>
	</tr>
	<tr>
		<th>Värde</th>
		<td><?=$product->value?></td>
	</tr>
	<tr>
		<th>Lager</th>
		<td><?=$product->count?></td>
	</tr>
	<tr>
		<th>lagrets värde</th>
		<td><?=$product->count * $product->value?></td>
	</tr>
	<tr>
		<th>EAN</th>
		<td><?=$product->ean?></td>
	</tr>
	<tr>
		<th>Kategori</th>
		<td><?=$product->Category->name?></td>
	</tr>
	<tr>
		<th>Totalt levererat</th>
		<td><?=DeliveryContent::sum('count', array('product_id' => $product->id))?></td>
	</tr>
	<tr>
		<th>Totalt sålt</th>
		<td><?=TransactionContent::sum('count', array('product_id' => $product->id))?></td>
	</tr>
	<tr>
		<th>Totalt sålt för</th>
		<td><?=TransactionContent::sum('amount', array('product_id' => $product->id))?></td>
	</tr>
</table>
<h2>EAN</h2>
<img src="/gfx/barcode.php?barcode=<?=$product->ean?>&amp;width=300" alt="<?=$product->ean?>" />
<h2>Försäljnings historik</h2>
<img src="/gfx/product_history.php?id=<?=$product->id?>" alt="Försäljningshistorik"/>

