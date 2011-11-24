<form action="/Category/modify/<?=$category->id?>" method="post">
	<fieldset>
		<legend>Byt namn på <?=$category?></legend>
		<input type="hidden" name="random" value="<?=get_rand()?>" />
		<table>
			<tr>
				<th>Namn</th>
				<td><input type="text" name="name" value="<?=$category?>" /></td>
			</tr>
		</table>
		<input type="submit" value="Byt namn" />
	</fieldset>
</form>
