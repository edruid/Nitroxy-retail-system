<h1><?=$category->name?></h1>
<table>
	<tr>
		<th>Lagrets värde</th>
		<td class="numeric">
			<?=number(Product::sum(array('value', '*', 'count'), array(
				'category_id' => $category->id
			)))?> kr
		</td>
	</tr>
</table>
<?php self::_partial('Product/stock_list', array($products)) ?>
