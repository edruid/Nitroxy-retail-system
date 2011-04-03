<script type="text/javascript" src="http://www.shawnolson.net/scripts/public_smo_scripts.js"></script>
<label><input type="checkbox" onchange="
	if(this.checked) {
		changecss('.inactive','display','none')
	} else {
		changecss('.inactive','display','')
	}
"> hide inactive</label>
<table class="alternate" id="products">
	<thead>
		<tr>
			<th><?$i++?></th>
			<th class="name_column"><a href="#" onclick="sort.sort(<?=$i++?>, sort.tagInsensitiveComparator);return false">Namn</a></th>
			<th class="category_column"><a href="#" onclick="sort.sort(<?=$i++?>, sort.tagInsensitiveComparator);return false">Kategori</a></th>
			<th class="value_column"><a href="#" onclick="sort.sort(<?=$i++?>);return false">Värde/st</a></th>
			<th class="price_column"><a href="#" onclick="sort.sort(<?=$i++?>);return false">Försäljning</a></th>
			<th class="count_column"><a href="#" onclick="sort.sort(<?=$i++?>);return false">Lager</a></th>
			<th class="total_value_column"><a href="#" onclick="sort.sort(<?=$i++?>);return false">Lagervärde</a></th>
			<th class="revenue"><a href="#" onclick="sort.sort(<?=$i++?>);return false">Vinst kr</a></th>
			<th class="revenue_p"><a href="#" onclick="sort.sort(<?=$i++?>);return false">Vinst %</a></th>
			<th class="threshold"><a href="#" onclick="sort.sort(<?=$i++?>);return false">Min buffert</a></th>
			<th class="threshold_diff"><a href="#" onclick="sort.sort(<?=$i++?>);return false">Buffert diff</a></th>
			<th class="sales"><a href="#" onclick="sort.sort(<?=$i++?>);return false">sålda 30 dag st</a></th>
			<th class="sales_value"><a href="#" onclick="sort.sort(<?=$i++?>);return false">sålda 30 dag kr</a></th>
			<th class="sales_revenue"><a href="#" onclick="sort.sort(<?=$i++?>);return false">vinst 30 dag kr</a></th>
		</tr>
	</thead>
	<tbody>
		<? foreach($products as $product): ?>
			<tr 
				<? if(!$product->active): ?>
					class="inactive"
				<? endif ?>
			>
				<td><a href="/edit_product/<?=$product->id?>"><img src="/gfx/edit.png" alt="edit" /></a></td>
				<td class="name_column"><a href="/product/<?=$product->id?>"><?=$product->name?></a></td>
				<td class="category_column"><a href="/category/<?=$product->category_id?>"><?=$product->Category->name?></a></td>
				<td class="numeric value_column"><?=number($product->value)?> kr</td>
				<td class="numeric price_column"><?=$product->price?> kr</td>
				<td class="numeric count_column"><?=$product->count?> st</td>
				<td class="numeric total_value_column"><?=number($product->count * $product->value)?> kr</td>
				<td class="numeric revenue<?=mark(($diff = $product->price-$product->value)<=0)?>" ><?=number($diff)?> kr</td>
				<td class="numeric revenue_p<?=mark(($markup=round($diff/$product->price*100))<20)?>" ><?=$markup?> %</td>
				<td class="numeric threshold"><?=$product->inventory_threshold?$product->inventory_threshold.' st':''?></td>
				<td class="numeric threshold_diff<?=mark(($t_diff=$product->count-$product->inventory_threshold)<=0 && $product->inventory_threshold != null)?>" ><?=$product->inventory_threshold>0?$t_diff.' st':''?></td>
				<td class="numeric sales"><?=$count = TransactionContent::sum('count', array('product_id' => $product->id, 'Transaction.timestamp:>' => date('Y-m-d', time()-60*60*24*30)))?> st</td>
				<td class="numeric sales_value"><?=number($sold = TransactionContent::sum('amount', array('product_id' => $product->id, 'Transaction.timestamp:>' => date('Y-m-d', time()-60*60*24*30))))?> kr</td>
				<td class="numeric sales_value"><?=number($sold - $count*$product->value)?> kr</td>
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
