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
		<th>EAN</th>
		<td><?=$product->ean?></td>
	</tr>
	<tr>
		<th>Kategori</th>
		<td>
			<a href="/Category/view/<?=$product->category_id?>">
				<?=$product->Category->name?>
			</a>
		</td>
	</tr>
	<tr>
		<th>Pris</th>
		<td class="numeric"><?=number($product->price)?> kr</td>
	</tr>
	<tr>
		<th>Värde</th>
		<td class="numeric"><?=number($product->value)?> kr</td>
	</tr>
	<tr>
		<th>Lager</th>
		<td class="numeric"><?=$product->count?> st</td>
	</tr>
	<tr>
		<th>lagrets värde</th>
		<td class="numeric"><?=number($product->count * $product->value)?> kr</td>
	</tr>
	<tr>
		<th>Totalt levererat</th>
		<td class="numeric"><?=$total_delivered?> st</td>
	</tr>
	<tr>
		<th>Totalt sålt</th>
		<td class="numeric"><?=number($total_sold)?> kr</td>
	</tr>
	<tr>
		<th>Totalt sålt för</th>
		<td class="numeric"><?=number($total_income)?> kr</td>
	</tr>
	<tr>
		<th>Totalt inköps värde</th>
		<td class="numeric"><?=number($total_purchase_price)?> kr</td>
	</tr>
	<tr>
		<th>Totalt vinst</th>
		<td class="numeric">
			<?=number($total_income - $total_purchase_price + $product->count * $product->value)?> kr
		</td>
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
				<td><a href="/Product/view/<?=$package->product_id?>"><?=$product = $package->Product()?></a></td>
				<td><?=$package->count?> st</td>
				<td><?=$package->count * $product->value?> kr</td>
				<? $total += $package->count * $product->value ?>
			<? endforeach ?>
		</tr>
	</table>
	<p>Totalt for produkten: <?=$total?></p>
<? endif ?>
<div>
	<h2>Försäljnings historik</h2>
	<img src="/gfx/product_history.php?id=<?=$product->id?>" alt="Försäljningshistorik"/>
</div>
<p>
<a href="/Product/edit/<?=$product->id?>">Redigera produkten</a>
</p>
