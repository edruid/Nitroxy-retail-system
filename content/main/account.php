<?php
if(empty($_SESSION['login'])) {
	kick('login?kickback='.htmlspecialchars(kickback_url()));
}
$account = Account::from_id(array_shift($request));
if(!$account) {
	die('Kontot finns inte');
}
$page = array_shift($request);
if($page == null) {
	$page = 0;
}
?>
<table>
	<tr>
		<th>Konto</th>
		<td><?=$account?></td>
	</tr>
	<tr>
		<th>Saldo</td>
		<td><?=AccountTransactionContent::sum('amount', array('account_id' => $account->id))?></td>
	</tr>
</table>
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
		<? 
			foreach(AccountTransactionContent::selection(array(
				'account_id' => $account->id,
				'@order' => 'AccountTransaction.timestamp:desc',
				'@limit' => array($page*30, $page*30+30),
			)) as $transaction_content):
		?>
			<tr>
				<td><a href="/account_transaction/<?=$transaction_content->account_transaction_id?>"><?=($transaction=$transaction_content->AccountTransaction)?></a></td>
				<td><?=$transaction->user?></td>
				<td><?=$transaction_content->amount?></td>
				<td><?=$transaction->description?></td>
			</tr>
		<? endforeach ?>
	</tbody>
</table>
