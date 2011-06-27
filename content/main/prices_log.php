<?php
if(empty($_SESSION['login'])) {
	kick('login?kickback='.htmlspecialchars(kickback_url()));
}
$prices = ProductLog::selection();
?>
<table>
	<thead>
		<tr>
			<th>Produkt</th>
			<th>Pris före</th>
			<th>Pris efter</th>
			<th>datum</th>
			<th>användare</th>
		</tr>
	</thead>
	<tbody>
		<? foreach($prices as $price): ?>
			<tr>
				<td><a href="/product/<?=$price->product_id?>"><?=$price->Product?></a></td>
				<td class="numeric"><?=$price->old_price?> kr</td>
				<td class="numeric"><?=$price->new_price?> kr</td>
				<td><?=$price->changed_at?></td>
				<td><?=$price->user?></td>
			</tr>
		<? endforeach ?>
	</tbody>
</table>
