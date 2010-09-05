<?php
$delivery = Delivery::from_id(array_shift($request));
?>
<h1>Leverans <?=$delivery->timestamp?></h1>
<p><?=$delivery->description?></p>
<?=$delivery->user?>
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
