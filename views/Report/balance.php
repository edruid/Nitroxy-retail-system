<h1>Balansrapport</h1>
<table>
	<thead>
		<tr>
			<th>Konto</th>
			<th>Summa</th>
			<th>Förändring</th>
		</tr>
	</thead>
	<tfoot>
		<tr>
			<th>Total:</th>
			<th class="numeric"><?= number($total) ?></th>
			<th class="numeric"><?= number($change_total) ?></th>
		</tr>
	</tfoot>
	<tbody>
		<?php foreach($this->accounts as $account): ?>
			<tr>
				<td>
					<a href="/Account/view/<?= $account->code_name ?>">
						<?= $account->name ?>
					</a>
				</td>
				<td class="numeric"><?= number($account->balance) ?></td>
				<td class="numeric">
					<?= number(AccountTransactionContent::sum('amount', array(
						'account_id'                      => $account->id,
						'AccountTransaction.timestamp:>=' => "$year-01-01 00:00:00",
						'AccountTransaction.timestamp:<'  => ($year+1)."-01-01 00:00:00",
					))) ?>
				</td>
			</tr>
		<?php endforeach ?>
	</tbody>
</table>
