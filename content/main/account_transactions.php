<?php
if(empty($_SESSION['login'])) {
	kick('login?kickback='.htmlspecialchars(kickback_url()));
}
$page = ClientData::get('page');
?>
<table class="alternate">
	<thead>
		<tr>
			<th>Tid</th>
			<th>Summa</th>
			<th>Användare</th>
			<th>Konton</th>
			<th>Beskrivning</th>
		</tr>
	</thead>
	<tbody>
		<? foreach(AccountTransaction::selection(array(
				'@order' => 'timestamp:desc',
				'@limit' => array($page*50, 50),
			)) as $transaction):
		?>
			<tr>
				<td><a href="/account_transaction/<?=$transaction->id?>"><?=$transaction->timestamp?></a></td>
				<td class="numeric"><?=AccountTransactionContent::sum('amount', array('amount:>=' => 0, 'account_transaction_id' => $transaction->id))?> kr</td>
				<td><?=$transaction->User?></td>
				<td>
					<ul>
						<? foreach($transaction->AccountTransactionContent as $content): ?>
							<li><a href="/account/<?=$content->account_id?>"><?=$content->Account?></a></li>
						<? endforeach ?>
					</ul>
				</td>
				<td><?=$transaction->description?></td>
			</tr>
		<? endforeach ?>
	</tbody>
</table>
<p>
	<? if($page > 0): ?>
		<a href="/account_transactions?page=<?=$page-1?>">&lt; Senare</a>
	<? endif ?>
	<a href="/account_transactions?page=<?=$page+1?>">Tidigare &gt;</a>
</p>
