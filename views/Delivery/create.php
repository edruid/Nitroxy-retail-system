<script type="text/javascript">
<!--
var products = new Array();
<? foreach($products as $product): ?>
	products['<?=$product->ean?>'] = {
		id: <?=$product->id?>,
		name: '<?=addslashes(htmlspecialchars_decode($product->name, ENT_QUOTES))?>',
		sales_price: '<?=$product->price?>',
		category_id: <?=$product->category_id?>
	}
<? endforeach ?>
-->
</script>
<h1>Ny inleverans</h1>
<form action="/Delivery/make" method="post"
		onsubmit="return confirm('Vill du fortsätta skapa leveransen?');">
	<div>
		<input type="hidden" name="random" value="<?=get_rand()?>" />
		<textarea rows="5" cols="50" name="description" id="initial_focus"><?=$old_values?
				$old_values['description'] :
				''?></textarea>
	</div>
	<table>
		<tfoot>
			<tr colspan="2">
				<td>
					<a href="#" onclick="
						var con = document.getElementById('account_container');
						var account = document.getElementById('account').cloneNode(true);
						var amount = document.getElementById('amount').cloneNode(true);
						account.id = '';
						amount.id = '';
						amount.getElementsByTagName('input')[0].value = '';
						con.appendChild(account);
						con.appendChild(amount);
						return false;
					">
						Lägg till konto
					</a>
				</td>
			</tr>
			<tr>
				<th>Totalsumma för leveransen:</th>
				<td><strong id="sum">0</strong> kr</td>
			</tr>
		</tfoot>
		<tbody id="account_container">
			<tr id="account">
				<th>Pengarna kommer från:</th>
				<td>
					<select name="from_account[]">
						<option
							value=""
							disabled="disabled"
							<? if(!$old_values || $old_values['from_account'][0] == ''): ?>
								selected="selected"
							<? endif ?>
						>
							Välj konto
						</option>
						<? foreach(Account::selection(array(
								'account_type' => 'balance', 
								'@order' => 'name',
								'code_name:not_in' => array('stock',),
						)) as $account): ?>
							<option
								value="<?=$account->code_name?>"
								<? if($old_values && $old_values['from_account'][0] == $account->code_name): ?>
									selected="selected"
								<? endif ?>
								title="<?=$account->description?>"
							>
								<?=$account->name?>
							</option>
						<? endforeach ?>
					</select>
				</td>
				<td rowspan="2"><a href="#" onclick="
					var row = parentNode.parentNode;
					if(row.id != 'account') {
						row.parentNode.removeChild(row.nextSibling);
						row.parentNode.removeChild(row);
					}
					return false;
				">X</a></td>
			</tr>
			<tr id="amount">
				<th>Summa:</th>
				<td>
					<input type="text"
						name="amount[]"
						onkeyress="fix_comma(event, this);"
						value="<?=$old_values?$old_values['amount'][0]:''?>"
						style="width: 3em;" />
				</td>
			</tr>
		</tbody>
	</table>
	<ul>
		<li>
			<label>
				<input type="radio"
					name="price_per"
					id="product_type"
					value="product_type"
					onchange="update_sum();"
					<?=($old_values && $old_values['price_per']=='product_type') ?
							'checked="checked"' :
							''?> />
				Pris per varotyp
			</label>
		</li>
		<li>
			<label>
				<input type="radio"
					name="price_per"
					value="each_product"
					onchange="update_sum();"
					<?=($old_values && $old_values['price_per']=='each_product') ?
							'checked="checked"' :
							''?> />
				Pris per enskild vara
			</label>
		</li>
	</ul>
	<p>
		Multipliserare (för moms mm)
		<input
			type="text"
			name="multiplyer"
			id="multiplyer"
			onchange="update_sum();"
			onkeyress="fix_comma(event, this);"
			<? if($old_values): ?>
				value="<?=$old_values['multiplyer']?>"
			<? else: ?>
				value="1.0"
			<? endif ?>
		/>
	</p>
	<table id="delivery_form">
		<thead>
			<tr>
				<th>EAN</th>
				<th>Namn</th>
				<th>Försäljningspris</th>
				<th>Kategori</th>
				<th>Antal</th>
				<th>Inköpspris</th>
				<th>Rad kostnad</th>
				<th>a kostnad</th>
				<th>Marginal</th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="6"><input type="submit" value="Skicka" /></td>
			</tr>
		</tfoot>
		<tbody id="delivery_products">
			<?php foreach($values as $row): ?>
				<tr>
					<td><input type="text" class="ean" name="ean[]"
							onblur="addLine()"
							onkeypress="return updateInfo(event, this)"
							value="<?=$row['ean']?>" /></td>
					<td><input type="text" class="name" name="name[]"
							value="<?=$row['name']?>" /></td>
					<td>
						<input
							type="text"
							class="sales_price"
							name="sales_price[]"
							onkeypress="fix_comma(event, this)"
							value="<?=$row['sales_price']?>"
						/>
					</td>
					<td>
						<select class="category" name="category[]" >
							<option
								value=""
								disabled="disabled"
								<?php if($row['category'] == ''): ?>
									selected="selected"
								<?php endif ?>
							>
								Välj kategori
							</option>
							<?php foreach($categories as $category): ?>
								<option
									value="<?=$category->id?>"
									<?php if($row['category']==$category->id): ?>
										selected="selected"
									<?php endif ?>
								>
									<?=$category->name?>
								</option>
							<?php endforeach ?>
						</select>
					</td>
					<td><input type="text" class="count" name="count[]"
							onblur="update_sum();"
							value="<?=$row['count']?>" /></td>
					<td>
						<input
							type="text"
							class="purchase_price"
							name="purchase_price[]"
							onblur="update_sum();"
							onkeypress="fix_comma(event, this)"
							value="<?=$row['purchase_price']?>"
						/>
					</td>
					<td class="row-sum numeric"></td>
					<td class="row-a numeric"></td>
					<td class="row-margin numeric"></td>
				</tr>
			<?php endforeach ?>
			<tr id="template" style="display: none;">
				<td><input type="text" class="ean" name="ean[]"
						onblur="addLine()"
						onkeypress="return updateInfo(event, this)" /></td>
				<td><input type="text" class="name" name="name[]" /></td>
				<td>
					<input
						type="text"
						class="sales_price"
						onblur="update_sum()"
						onkeypress="fix_comma(event, this)"
						name="sales_price[]"
					/>
				</td>
				<td>
					<select class="category" name="category[]" >
						<option value="" disabled="disabled" selected="selected">
							Välj kategori
						</option>
						<? foreach($categories as $category): ?>
							<option value="<?=$category->id?>"><?=$category->name?></option>
						<? endforeach ?>
					</select>
				</td>
				<td><input type="text" class="count"
						name="count[]"
						onblur="update_sum();" /></td>
				<td><input type="text" class="purchase_price"
						name="purchase_price[]"
						onkeypress="fix_comma(event, this)"
						onblur="update_sum();" /></td>
				<td><input type="text" class="purchase_price"
						name="pant[]"
						onkeypress="fix_comma(event, this)"
						onblur="update_sum()"/></td>

				<td class="row-sum numeric"></td>
				<td class="row-a numeric"></td>
				<td class="row-margin numeric"></td>
			</tr>
		</tbody>
	</table>
</form>
