<?php
$delivery = Delivery::from_id(array_shift($request));
?>
<h1>Leverans <?=$delivery->timestamp?></h1>
<table>
	<tr>
		<th>Beskrivning</th>
		<td class="pre"><?=$delivery->description?></td>
	</tr>
	<tr>
		<th>User</th>
		<td><?=$delivery->user?></td>
	</tr>
	<tr>
		<th>Total summa</th>
		<td><?=DeliveryContent::sum(array('cost', '*', 'count'), array('delivery_id' => $delivery->id))?> kr</td>
	</tr>
</table>
<table>
	<thead>
		<tr>
			<th>Produkt</th>
			<th>Antal</th>
			<th>Kostnad/st</th>
			<th>Kostnad totalt</th>
		</tr>
	</thead>
	<tbody>
		<? foreach($delivery->DeliveryContent() as $content): ?>
			<tr>
				<td><a href="/product/<?=$content->product_id?>"><?=$content->Product?></a></td>
				<td><?=$content->count?></td>
				<td><?=$content->cost?></td>
				<td><?=$content->cost * $content->count?></td>
			</tr>
		<? endforeach ?>
	</tbody>
</table>
