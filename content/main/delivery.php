<?php
if(empty($_SESSION['login'])) {
	kick('login?kickback='.htmlspecialchars("http".($_SERVER['HTTPS']?'s':'')."://{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}"));
}
$categories = Category::selection(array(
	'category_id:!=' => 0,
));
$products = Product::selection(array(
	'category_id:!=' => 0,
));
$old_values = session_get('_POST');
unset($_SESSION['_POST']);
?>
<script type="text/javascript">
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
</script>
<a href="scripts/logout.php">Logga ut</a>
<h1>Ny inleverans</h1>
<form action="<?=absolute_path('scripts/delivery.php');?>" method="post" onsubmit="return confirm('Vill du fortsätta skapa leveransen?');">
<textarea rows="5" cols="50" name="description"></textarea>
<table>
	<thead>
		<tr>
			<th>EAN</th>
			<th>Namn</th>
			<th>Försäljningspris</th>
			<th>Kategori</th>
			<th>Antal</th>
			<th>Inköpspris inkl moms</th>
		</tr>
	</thead>
	<tfoot>
		<tr>
			<td colspan="6"><input type="submit" value="Skicka" /></td>
		</tr>
	</tfoot>
	<tbody id="delivery_products">
		<? if($old_values): ?>
			<? for($i=0; $i < count($old_values['ean']); $i++): ?>
				<tr>
					<td><input type="text" class="ean" name="ean[]" onblur="addLine()" onkeypress="return updateInfo(event, this)" value="<?=$old_values['ean'][$i]?>" /></td>
					<td><input type="text" class="name" name="name[]" value="<?=$old_values['name'][$i]?>" /></td>
					<td><input type="text" class="sales_price" name="sales_price[]" value="<?=$old_values['sales_price'][$i]?>" /></td>
					<td>
						<select class="category" name="category[]" />
							<? foreach($categories as $category): ?>
								<option value="<?=$category->id?>" <?=$old_values['category'][$i]==$category->id?'selected="selected"':''?>><?=$category->name?></option>
							<? endforeach ?>
						</select>
					</td>
					<td><input type="text" class="count" name="count[]" value="<?=$old_values['count'][$i]?>" /></td>
					<td><input type="text" class="purchase_price" name="purchase_price[]" value="<?=$old_values['purchase_price'][$i]?>" /></td>
				</tr>
			<? endfor ?>
		<? endif ?>
		<tr>
			<td><input type="text" class="ean" name="ean[]" onblur="addLine()" onkeypress="return updateInfo(event, this)" /></td>
			<td><input type="text" class="name" name="name[]" /></td>
			<td><input type="text" class="sales_price" name="sales_price[]" /></td>
			<td>
				<select class="category" name="category[]" />
					<? foreach($categories as $category): ?>
						<option value="<?=$category->id?>"><?=$category->name?></option>
					<? endforeach ?>
				</select>
			</td>
			<td><input type="text" class="count" name="count[]" /></td>
			<td><input type="text" class="purchase_price" name="purchase_price[]" /></td>
		</tr>
		<tr id="template" style="display: none;">
			<td><input type="text" class="ean" name="ean[]" onblur="addLine()" onkeypress="return updateInfo(event, this)" /></td>
			<td><input type="text" class="name" name="name[]" /></td>
			<td><input type="text" class="sales_price" name="sales_price[]" /></td>
			<td>
				<select class="category" name="category[]" />
					<? foreach($categories as $category): ?>
						<option value="<?=$category->id?>"><?=$category->name?></option>
					<? endforeach ?>
				</select>
			</td>
			<td><input type="text" class="count" name="count[]" /></td>
			<td><input type="text" class="purchase_price" name="purchase_price[]" /></td>
		</tr>
	</tbody>
</table>
</form>
