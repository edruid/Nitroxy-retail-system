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
				<td title="<?=$account->description?>"><a href="/Account/view/<?=$account->code_name?>"><?=$account?></a></td>
				<td><?=AccountTransactionContent::sum('amount', array('account_id' => $account->id))?></td>
			</tr>
		<? endforeach ?>
	</tbody>
</table>
