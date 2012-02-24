<form method="get">
	<fieldset>
		<legend>Visa rapport för:</legend>
		<label>
			Från och med:<br />
			<input type="text" name="start" value="<?= $start ?>" />
		</label><br />
		<label>
			Till och med:<br />
			<input type="text" name="end" value="<?= $end ?>" />
		</label><br />
		<input type="submit" value="visa" />
	</fieldset>
</form>
<h1>Resultatrapport</h1>
<table>
	<thead>
		<tr>
			<th>Konto</th>
			<th>Summa</th>
		</tr>
	</thead>
	<tfoot>
		<tr>
			<th>Total:</th>
			<th class="numeric"><?= number(-$total) ?></th>
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
				<td class="numeric">
					<?= number(-AccountTransactionContent::sum('amount', array(
						'account_id'                      => $account->id,
						'AccountTransaction.timestamp:>=' => $start,
						'AccountTransaction.timestamp:<=' => $end.' 23:59:59',
					))) ?>
				</td>
			</tr>
		<?php endforeach ?>
	</tbody>
</table>
