<?php
$product = Product::from_id(array_shift($request));
if(!$product) {
	die('unknown product');
}
$packages = ProductPackage::selection(array(
	'package' => $product->id
));
$total = $product->value;
?>
<table>
	<tr>
		<th>Namn</th>
		<td><?=$product->name?></td>
	</tr>
	<tr>
		<th>Status</th>
		<td><?=$product->active?'Aktiv':'Inaktiv'?></td>
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
		<td><a href="/category/<?=$product->category_id?>"><?=$product->Category->name?></a></td>
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
<div>
	<h2>EAN</h2>
	<img src="/gfx/barcode.php?barcode=<?=$product->ean?>&amp;width=300" alt="<?=$product->ean?>" />
</div>
<? if($packages): ?>
	<h2>Andra produkter som ingår i den här</h2>
	<table>
		<tr>
			<th>Produkt</th>
			<th>Antal</th>
			<th>Delvärde</th>
		</tr>
		<tr>
			<? foreach($packages as $package): ?>
				<td><a href="/product/<?=$package->product_id?>"><?=$product = $package->Product()?></a></td>
				<td><?=$package->count?> st</td>
				<td><?=$package->count * $product->value?> kr</td>
				<? $total += $package->count * $product->value ?>
			<? endforeach ?>
		</tr>
	</table>
	<p>Totalt for producten: <?=$total?></p>
<? endif ?>
<div>
	<h2>Försäljnings historik</h2>
	<img src="/gfx/product_history.php?id=<?=$product->id?>" alt="Försäljningshistorik"/>
</div>
<p>
<a href="/edit_product/<?=$product->id?>">Redigera produkten</a>
</p>
