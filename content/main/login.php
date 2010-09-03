<h1>Logga in</h1>
<form action="/scripts/authenticate.php" method="post">
	<input type="hidden" name="kickback" value="<?=htmlspecialchars(request_get('kickback'), ENT_QUOTES, 'utf-8')?>" />
	<table>
		<tr>
			<th>användarnamn</th>
			<td><input type="text" name="username" /></td>
		</tr>
		<tr>
			<th>Lösenord</th>
			<td><input type="password" name="password" /></td>
		</tr>
		<tr>
			<td colspan="2"><input type="submit" value="Logga in" /></td>
		</tr>
	</table>
</form>
