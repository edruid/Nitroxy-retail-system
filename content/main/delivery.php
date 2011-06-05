<?php
if(empty($_SESSION['login'])) {
	kick('login?kickback='.htmlspecialchars(kickback_url()));
}
$categories = Category::selection(array(
	'category_id:!=' => 0,
));
$products = Product::selection(array(
	'category_id:!=' => 0,
));
$old_values = ClientData::session('_POST');
unset($_SESSION['_POST']);
?>
<script type="text/javascript">
<!--
function addLine() {
	var hidden=document.getElementById("template");
	var element = hidden.previousSibling;
	while(!String(element).match('HTMLTableRowElement')) {
		element = element.previousSibling;
	}
	var last_element_inputs = element.getElementsByTagName('input');
	for(i=0; i<last_element_inputs.length; i++) {
		if(last_element_inputs[i].value != '') {
			var new_line=hidden.cloneNode(true);
			new_line.removeAttribute('style');
			new_line.removeAttribute('id');
			hidden.parentNode.insertBefore(new_line, hidden);
			return;
		}
	}
}
var products = new Array();
<? foreach($products as $product): ?>
	products['<?=$product->ean?>'] = {
		id: <?=$product->id?>,
		name: '<?=addslashes(htmlspecialchars_decode($product->name, ENT_QUOTES))?>',
		sales_price: '<?=$product->price?>',
		category_id: <?=$product->category_id?>
	}
<? endforeach ?>

function updateInfo(e, field) {
	var keynum;
	if(window.event) { // IE
		keynum = e.keyCode;
	} else if(e.which) { //Netscape/Firefox/Opera
		keynum = e.which;
	}
	if(keynum == 13) {
		var tr = field.parentNode.parentNode;
		var ean = field.value;
		if(products[ean]) {
			tr.getElementsByClassName('sales_price')[0].value = products[ean].sales_price;
			tr.getElementsByClassName('name')[0].value = products[ean].name;
			var categories = tr.getElementsByClassName('category')[0];
			for(var i=0; i < categories.options.length; i++) {
				if(categories.options[i].value == products[ean].category_id) {
					categories.selectedIndex = i;
					break;
				}
			}
			tr.getElementsByClassName('count')[0].focus();
		} else {
			tr.getElementsByClassName('name')[0].focus();
		}
		if (e.preventDefault) {
			e.preventDefault();
			e.stopPropagation();
		} else {
			e.returnValue = false;
			e.cancelBubble = true;
		}
	}
}

function update_sum() {
	var table = document.getElementById('delivery_form');
	var counts = table.getElementsByClassName('count');
	var prices = table.getElementsByClassName('purchase_price');
	var per_product = !(document.getElementById('product_type').checked);
	var multiplyer = document.getElementById('multiplyer').value;
	var sum = 0;
	for(var i=0; i< counts.length; ++i) {
		if(per_product) {
			sum += counts[i].value * prices[i].value * multiplyer;
		} else {
			sum += prices[i].value * multiplyer;
		}
	}
	document.getElementById('sum').innerHTML = sum;
}
-->
</script>
<h1>Ny inleverans</h1>
<form action="/scripts/delivery.php" method="post"
		onsubmit="return confirm('Vill du fortsätta skapa leveransen?');">
	<div>
		<textarea rows="5" cols="50" name="description" id="initial_focus"><?=$old_values?
				$old_values['description'] :
				''?></textarea>
	</div>
	<table>
		<tr>
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
					<? foreach(Account::selection(array('account_type' => 'balance', '@order' => 'name')) as $account): ?>
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
		</tr>
		<tr>
			<th>Summa:</th>
			<td>
				<input type="text"
					name="amount[]"
					value="<?=$old_values?$old_values['amount'][0]:''?>"
					style="width: 3em;" />
			</td>
		</tr>
		<tr>
			<th>Totalsumma för leveransen:</th>
			<td><strong id="sum">0</strong> kr</td>
		</tr>
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
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="6"><input type="submit" value="Skicka" /></td>
			</tr>
		</tfoot>
		<tbody id="delivery_products">
			<? if($old_values): ?>
				<? for($i=0; $i < count($old_values['ean'])-2; $i++): ?>
					<tr>
						<td><input type="text" class="ean" name="ean[]"
								onblur="addLine()"
								onkeypress="return updateInfo(event, this)"
								value="<?=$old_values['ean'][$i]?>" /></td>
						<td><input type="text" class="name" name="name[]"
								value="<?=$old_values['name'][$i]?>" /></td>
						<td><input type="text" class="sales_price"
								name="sales_price[]"
								value="<?=$old_values['sales_price'][$i]?>" /></td>
						<td>
							<select class="category" name="category[]" >
								<? foreach($categories as $category): ?>
									<option value="<?=$category->id?>"
											<?=$old_values['category'][$i]==$category->id?'selected="selected"':''?>>
										<?=$category->name?>
									</option>
								<? endforeach ?>
							</select>
						</td>
						<td><input type="text" class="count" name="count[]"
								onblur="update_sum();"
								value="<?=$old_values['count'][$i]?>" /></td>
						<td><input type="text" class="purchase_price"
								name="purchase_price[]" onblur="update_sum();"
								value="<?=$old_values['purchase_price'][$i]?>" /></td>
					</tr>
				<? endfor ?>
			<? endif ?>
			<tr>
				<td><input type="text" class="ean" name="ean[]" onblur="addLine()"
						onkeypress="return updateInfo(event, this)" /></td>
				<td><input type="text" class="name" name="name[]" /></td>
				<td><input type="text" class="sales_price" name="sales_price[]" /></td>
				<td>
					<select class="category" name="category[]" >
						<? foreach($categories as $category): ?>
							<option value="<?=$category->id?>">
								<?=$category->name?>
							</option>
						<? endforeach ?>
					</select>
				</td>
				<td><input type="text" class="count" name="count[]"
						onblur="update_sum();" /></td>
				<td><input type="text" class="purchase_price"
						name="purchase_price[]" onblur="update_sum();" /></td>
			</tr>
			<tr id="template" style="display: none;">
				<td><input type="text" class="ean" name="ean[]"
						onblur="addLine()"
						onkeypress="return updateInfo(event, this)" /></td>
				<td><input type="text" class="name" name="name[]" /></td>
				<td><input type="text" class="sales_price" name="sales_price[]" /></td>
				<td>
					<select class="category" name="category[]" >
						<? foreach($categories as $category): ?>
							<option value="<?=$category->id?>"><?=$category->name?></option>
						<? endforeach ?>
					</select>
				</td>
				<td><input type="text" class="count" name="count[]"
						onblur="update_sum();" /></td>
				<td><input type="text" class="purchase_price"
						name="purchase_price[]" onblur="update_sum();" /></td>
			</tr>
		</tbody>
	</table>
</form>
