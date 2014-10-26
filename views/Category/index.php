<form action="/Category/make" method="post">
	<fieldset>
		<legend>Lägg till kategori</legend>
		<input type="hidden" name="random" value="<?=get_rand()?>" />
		<table>
			<tr>
				<th>Namn</th>
				<td><input type="text" name="name"/></td>
			</tr>
			<tr>
				<td colspan="2"><input type="submit" value="skapa" /></td>
			</tr>
		</table>
	</fieldset>
</form>
<table class="alternate" id="categories">
	<thead>
		<tr>
			<th><?php $i=1 ?></th>
			<th class="name_column"><a href="#" onclick="sort.sort(<?=$i++?>, sort.tagInsensitiveComparator);return false">Namn</a></th>
			<th class="total_value_column"><a href="#" onclick="sort.sort(<?=$i++?>);return false">Lagervärde</a></th>
			<th class="sales_value"><a href="#" onclick="sort.sort(<?=$i++?>);return false">sålt 30 dag</a></th>
			<th class="sales_revenue"><a href="#" onclick="sort.sort(<?=$i++?>);return false">vinst 30 dag</a></th>
			<th class="revenue_p"><a href="#" onclick="sort.sort(<?=$i++?>);return false">vinst %</a></th>
		</tr>
	</thead>
	<tbody>
		<?php foreach($categories as $category): ?>
			<tr>
				<td>
					<a href="/Category/edit/<?=$category->id?>">
						<img src="/gfx/edit.png" alt="edit" />
					</a>
				</td>
				<td class="name_column">
					<a href="/Category/view/<?=$category->id?>">
						<?=$category->name?>
					</a>
				</td>
				<td class="numeric total_value_column">
					<?=number(Product::sum(array('value', '*', 'count'), array(
						'category_id' => $category->id
					)))?> kr
				</td>
				<td class="numeric sales_value">
					<?=number($sale = TransactionContent::sum('amount', array(
						'Product.category_id' => $category->id,
						'Transaction.timestamp:>' => date('Y-m-d', time()-60*60*24*30)
					)))?> kr
				</td>
				<td class="numeric sales_revenue">
					<?=number($revenue = $category->revenue(date('Y-m-d', time()-60*60*24*30)))?> kr
				</td>
				<td class="numeric revenue_p">
					<?php if($sale!=0): ?>
						<?=number($revenue/$sale*100)?> %
					<?php endif ?>
				</td>
			</tr>
		<?php endforeach ?>
	</tbody>
</table>
<script type="text/javascript">
var sort;
function startSort() {
	sort=new Sort();
	sort.table = document.getElementById('categories');
	sort.defaultComparator = sort.numericComparator;
}

window.addEventListener ?
	window.addEventListener('load', startSort, false) :
	window.attachEvent('onload', startSort);

</script>
