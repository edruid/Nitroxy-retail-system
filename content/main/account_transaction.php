<?php
if(empty($_SESSION['login'])) {
	kick('login?kickback='.kickback_url());
}
$transaction = AccountTransaction::from_id(array_shift($request));
$contents = $transaction->AccountTransactionContent;
?>
<h1>Transaktion - <p><?=$transaction->description?></h1>
<table>
	<tr>
		<th>Användare</th>
		<td><?=$transaction->User?></td>
	</tr>
	<tr>
		<th>Tid</th>
		<td><?=$transaction->timestamp?></td>
	</tr>
</table>
<table>
	<thead>
		<tr>
			<th>Konto</th>
			<th>Summa</th>
		</tr>
	</thead>
	<tbody>
		<? foreach($contents as $content): ?>
			<tr>
				<td><?=$content->Account?></td>
				<td><?=$content->amount?> kr</td>
			</tr>
		<? endforeach ?>
	</tbody>
</table>
