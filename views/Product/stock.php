<table>
	<tr>
		<th>Lagrets värde</th>
		<td class="numeric"><?=number($stock_value)?> kr</td>
	</tr>
</table>
<?php $this->_partial("Product/stock_list", array($products)) ?>
