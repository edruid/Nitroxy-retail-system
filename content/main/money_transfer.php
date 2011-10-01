<?php
if(empty($_SESSION['login'])) {
	kick('login?kickback='.htmlspecialchars(kickback_url()));
}
$accounts = Account::selection(array('@order' => 'name'));
?>
<form action="/scripts/money_transfer.php" method="post">
	<table>
		<tr>
			<th>Beskrivning</th>
			<td><input type="text" name="description" /></td>
		</tr>
		<tr>
			<th>Från</th>
			<td>
				<select name="from_account_id">
					<option selected="selected" disabled="disabled"></option>
					<? foreach($accounts as $account): ?>
						<option value="<?=$account->id?>" title="<?=$account->description?>"><?=$account?></option>
					<? endforeach ?>
				</select>
			</td>
		</tr>
		<tr>
			<th>Till</th>
			<td>
				<select name="to_account_id">
					<option selected="selected" disabled="disabled"></option>
					<? foreach($accounts as $account): ?>
						<option value="<?=$account->id?>" title="<?=$account->description?>"><?=$account?></option>
					<? endforeach ?>
				</select>
			</td>
		</tr>
		<tr>
			<th>Summa</th>
			<td><input type="text" name="amount" onkeypress="fix_comma(event, this);" /></td>
		</tr>
		<tr>
			<td colspan="2"><input type="submit" value="Bokför" /></td>
		</tr>
	</table>
</form>
