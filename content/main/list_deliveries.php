<h1>Leveranser</h1>
<table>
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
				<td><?=DeliveryContent::sum(array('cost', '*', 'count'), array('delivery_id' => $delivery->id))?></td>
				<td><pre><?=$delivery->description?></pre></td>
			</tr>
		<? endforeach ?>
	</tbody>
</table>
