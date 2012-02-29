<form action="/Product/modify/<?=$product->id?>" method="post">
	<table>
		<tr>
			<th>Namn<input type="hidden" name="product" value="<?=$product->id?>" /></th>
			<td><input type="text" name="name" value="<?=$product->name?>" /></td>
		</tr>
		<tr>
			<th>Status</th>
			<td>
				<select name="active">
					<option value="0" <?=$product->active?'':'selected="selected"'?> >Inaktiv</option>
					<option value="1" <?=$product->active?'selected="selected"':''?> >Aktiv</option>
				</select>
			</td>
		</tr>
		<tr>
			<th>Pris</th>
			<td><input type="text" name="price" value="<?=$product->price?>" /></td>
		</tr>
		<tr>
			<th>EAN</th>
			<td><input type="text" name="ean" value="<?=$product->ean?>" /></td>
		</tr>
		<tr>
			<th>Lager buffert</th>
			<td><input type="text" name="inventory_threshold" value="<?=$product->inventory_threshold?>" /></td>
		</tr>
		<tr>
			<th>Kategori</th>
			<td>
				<select name="category_id">
					<? foreach($categories as $category): ?>
						<option value="<?=$category->id?>" <?=$product->category_id==$category->id?'selected="selected"':''?>><?=$category->name?></option>
					<? endforeach ?>
				</select>
			</td>
		</tr>
		<tr>
			<th>Bokföringskonto</th>
			<td>
				<select name="account_id">
					<option value="">--default--</option>
					<?php foreach($accounts as $account): ?>
						<option value="<?=$account->id?>" <?=$product->account_id==$account->id?'selected="selected"':''?> />
							<?= $account ?>
						</option>
					<?php endforeach ?>
				</select>
			</td>
		</tr>
		<tr>
			<td colspan="2"><input type="submit" value="Spara" /></td>
		</tr>
	</table>
</form>
