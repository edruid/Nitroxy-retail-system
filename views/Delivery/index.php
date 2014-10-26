<?php self::_partial('Helper/pager', array('/Delivery/index/%d', $page, $last_page)) ?>
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
		<?php foreach($deliveries as $delivery): ?>
			<tr>
				<td><a href="/Delivery/view/<?=$delivery->id?>"><?=$delivery->timestamp?></a></td>
				<td><?=$delivery->User?></td>
				<td class="numeric"><?=number(DeliveryContent::sum(array('cost', '*', 'count'), array('delivery_id' => $delivery->id)))?> kr</td>
				<td class="pre"><?=$delivery->description?></td>
			</tr>
		<?php endforeach ?>
	</tbody>
</table>
<?php self::_partial('Helper/pager', array('/Delivery/index/%d', $page, $last_page)) ?>
