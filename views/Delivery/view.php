<h1>Leverans <?=$delivery->timestamp?></h1>
<table>
	<tr>
		<th>Beskrivning</th>
		<td class="pre"><?=$delivery->description?></td>
	</tr>
	<tr>
		<th>Användare</th>
		<td><?=$delivery->User?></td>
	</tr>
	<tr>
		<th>Total summa</th>
		<td><?=number(DeliveryContent::sum(array('cost', '*', 'count'), array('delivery_id' => $delivery->id)))?> kr</td>
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
		<?php foreach($delivery->DeliveryContent() as $content): ?>
			<tr>
				<td><a href="/Product/view/<?=$content->product_id?>"><?=$content->Product?></a></td>
				<td class="numeric"><?=$content->count?> st</td>
				<td class="numeric"><?=number($content->cost)?> kr</td>
				<td class="numeric"><?=number($content->cost * $content->count)?> kr</td>
			</tr>
		<?php endforeach ?>
	</tbody>
</table>
