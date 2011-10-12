<form action="/Transaction/make" method="post">
	<table>
		<tr>
			<th>Beskrivning</th>
			<td><input type="text" name="description" /></td>
		</tr>
		<tr>
			<th>Från</th>
			<td>
				<select name="from_account">
					<option selected="selected" disabled="disabled"></option>
					<? foreach($accounts as $account): ?>
						<option value="<?=$account->code_name?>" title="<?=$account->description?>"><?=$account?></option>
					<? endforeach ?>
				</select>
			</td>
		</tr>
		<tr>
			<th>Till</th>
			<td>
				<select name="to_account">
					<option selected="selected" disabled="disabled"></option>
					<? foreach($accounts as $account): ?>
						<option value="<?=$account->code_name?>" title="<?=$account->description?>"><?=$account?></option>
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
