<?php self::_partial('Helper/pager', array(
	"/Account/view/{$account->code_name}/%d", $page, $last_page
)) ?>
<table>
	<tr>
		<th>Konto</th>
		<td><?=$account?></td>
	</tr>
	<tr>
		<th>Saldo</th>
		<td class="numeric"><?=$balance?> kr</td>
	</tr>
</table>
<p><?=$account->description?></p>
<h2>Transaktioner</h2>
<table>
	<thead>
		<tr>
			<th>Tid</th>
			<th>Användare</th>
			<th>Summa</th>
			<th>Beskrivning</th>
		</tr>
	</thead>
	<tbody>
		<?php foreach($contents as $content): ?>
			<tr>
				<td>
					<a href="/Transaction/view/<?=$content->account_transaction_id?>">
						<?=($transaction=$content->AccountTransaction)?>
					</a>
				</td>
				<td><?=$transaction->User?></td>
				<td><?=$content->amount?></td>
				<td><?=$transaction->description?></td>
			</tr>
		<? endforeach ?>
	</tbody>
</table>
<?php self::_partial('Helper/pager', array(
	"/Account/view/{$account->code_name}/%d", $page, $last_page
)) ?>
