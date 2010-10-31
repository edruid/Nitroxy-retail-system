<?php
$post = ClientData::session('loggin_form');
?>
<h1>Logga in</h1>
<form action="/scripts/authenticate.php" method="post">
	<table>
		<tr>
			<th>Användarnamn<input type="hidden" name="kickback" value="<?=$post?$post['kickback']:ClientData::request('kickback')?>" /></th>
			<td><input type="text" name="username" id="initial_focus" <?=$post?"value=\"{$post['username']}\"":''?> /></td>
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
