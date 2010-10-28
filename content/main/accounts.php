<?php
if(empty($_SESSION['login'])) {
	kick('login?kickback='.htmlspecialchars(kickback_url()));
}
$accounts = Account::selection(array('@order' => 'name'));
?>
<p><a href="/money_transfer">Ny transaktion</a></p>
<table>
	<thead>
		<tr>
			<th>Konto</th>
			<th>Summa</th>
		</tr>
	</thead>
	<tbody>
		<? foreach($accounts as $account): ?>
			<tr>
				<td><a href="/account/<?=$account->id?>"><?=$account?></a></td>
				<td><?=AccountTransactionContent::sum('amount', array('account_id' => $account->id))?></td>
			</tr>
		<? endforeach ?>
	</tbody>
</table>
