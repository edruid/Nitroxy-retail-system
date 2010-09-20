<h1>Leveranser</h1>
<table class="alternate">
	<thead>
		<tr>
			<th>Tid</th>
			<th>Ansvarig</th>
			<th>Summa</th>
			<th>Kommentar</th>
		</tr>
	</thead>
	<tbody>
		<? foreach(Delivery::selection(array('@order' => 'timestamp:desc')) as $delivery): ?>
			<tr>
				<td><a href="/view_delivery/<?=$delivery->id?>"><?=$delivery->timestamp?></a></td>
				<td><?=$delivery->user?></td>
				<td class="numeric"><?=number(DeliveryContent::sum(array('cost', '*', 'count'), array('delivery_id' => $delivery->id)))?> kr</td>
				<td class="pre"><?=$delivery->description?></td>
			</tr>
		<? endforeach ?>
	</tbody>
</table>
