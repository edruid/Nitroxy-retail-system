<?php
$transaction = Transaction::last();
$products = Product::selection(array(
	'category_id:!=' => 0,
	'@order' => 'product_id',
));
?>
<script type="text/javascript" src="/js/purchase.js"></script>
<script type="text/javascript" src="/js/suggest.js"></script>
<script type="text/javascript">
<!--
var products;

/**
 * Takes a keyPressed event in recieved amount and deciedes what
 * to do with it.
 */
function keyHook(e) {
	var recieved_elem=document.getElementById('recieved');
	var change_elem=document.getElementById('change');
	var keynum;
	var keychar;
	var finish_transaction=false;

	if(window.event) { // IE
		keynum = e.keyCode;
	} else if(e.which) { // Netscape/Firefox/Opera
		keynum = e.which;
	}
	keychar = String.fromCharCode(keynum);

	if(fix_comma(e, recieved_elem)) {
		setTimeout("update_change()",5);
		return false;
	} else if(keynum==13) { // return/enter
		return finish(sum, recieved_elem.value, change_elem.innerHTML);
	}
	var numcheck = /\d/;
	if(keychar==undefined || !(numcheck.test(keychar) ||
			keynum==8 || keynum==46 || keynum==undefined ||
			keychar=='.')) {
		alert("Var god mata endast in siffror och punkt i fältet. Du tryckte på tangent "+keynum+": "+keychar);
		return false;
	}

	setTimeout("update_change()",5);
	return true;
}

/**
 * Updates the sum owed for the basket.
 */
function updateSum() {
	var calc_amount=0;
	for(var i in this.basket) {
		if(this.basket[i]!=NaN) {
			calc_amount+=this.basket[i]*this.products[i].price;
		}
	}

	var diff = calc_amount;
	calc_amount = Math.round(calc_amount);
	diff = calc_amount - diff;

	var sum_elem=document.getElementById('sum');
	sum_elem.innerHTML=calc_amount+" kr";

	var diff_elem = document.getElementById('diff');
	diff_elem.innerHTML = diff.toFixed(2)+" kr";

	sum=calc_amount;
	update_change();
}

/**
 * Check if an object is empty (has no members)
 */
function is_empty(obj) {
	for(var i in obj) {
		return false;
	}
	return true;
}

function start() {
	document.getElementById('ean').focus();
	products = new Products("product_list", "suggest", "ean",
		{
			hookOnUpdatedProductList: updateSum,
			hookOnEmptyProduct: function() {
				if(!is_empty(this.basket)) {
					document.getElementById('recieved').focus();
				}
				return false;
			}
		}
	);
	<? foreach($products as $product): ?>
		products.addProduct(
			<?=$product->id?>,
			"<?=$product->ean?>",
			"<?=addslashes(htmlspecialchars_decode($product->name, ENT_QUOTES))?>",
			"[<?=$product->id?>] <?=addslashes(htmlspecialchars_decode($product->name, ENT_QUOTES))?> (<?=$product->price?> kr)",
			<?=$product->count?>,
			<?=$product->price?>,
			<?=$product->active?>
		);
	<? endforeach ?>
	products.start();
}

function finish(sum, recieved, change_string) {
	if(lock) {
		alert("Formuläret är redan skickat, vänta lite till.");
		return false;
	}
	var change=recieved-sum;
	if(change<0) {
		return false;
	}
	if(change+" kr"!=change_string) {
		alert(change);
		alert(change_string);
		alert("Ett fel uppstod vid beräkning av växel");
		return false;
	}
	javascript:void((new Image()).src='http://localhost:1138?random=' + Math.random());
	if(confirm("Godkänn?\nAtt betala: "+sum+"\nBetalt: "+recieved+"\nVäxel: "+change)) {
		lock = true;
		var wait = document.getElementById('wait');
		wait.innerHTML = '';
		wait.appendChild(wait_img);
		wait.appendChild(document.createTextNode(' Var god vänta.'));

		document.getElementById('retail_form').submit();
		return true;
	} else {
		return false;
	}
}

/**
 * Update the change element with the amount to be returned.
 */
function update_change() {
	var recieved=document.getElementById('recieved').value;
	var change_elem=document.getElementById('change');
	if(recieved == "") {
		change_elem.innerHTML = "";
	} else {
		change_elem.innerHTML = recieved-sum + " kr";
		if(recieved-sum < 0) {
			change_elem.style.color = "red";
		} else {
			change_elem.style.color = "green";
		}
	}
}

window.addEventListener ?
  window.addEventListener('load', start, false) :
  window.attachEvent('onload', start);

--></script>
<h2>Nytt köp</h2>
<form autocomplete="off" id="retail_form" action="scripts/finish_transaction.php" method="post">

	<div id="this_purchase">
		<h2>Detta köp</h2>
		<table>
			<tr>
				<td>Att betala</td>
				<td><strong id="sum">0 kr</strong></td>
			</tr>
			<tr>
				<td>Öresavrundning</td>
				<td><strong id="diff">0.00 kr</strong></td>
			</tr>
			<tr>
				<td>Mottaget</td>
				<td><input style="font-weight: bold; width: 6em;" maxlength="6" tabindex="2" type="text" name="recieved" id="recieved" onkeypress="return keyHook(event);" /> kr</td>
			</tr>
			<tr>
				<td>Växel</td>
				<td><strong id="change"></strong></td>
			</tr>
		</table>
	</div>

	<div style="float: left;">
		<div id="product_list_container">
			<ul id="product_list">
				<li style="display: none;"></li>
			</ul>
		</div>
		<p>
			<label>
				EAN-kod, artikelnummer eller beskrivning<br />
				<input type="text" tabindex="1" name="ean" id="ean" />
				<span id="suggest"></span>
			</label>
			<input type="submit" onclick="products.addToBasket(); return false;" value="OK" />
		</p>
		<div id="wait">
			<ul class="help">
				<li><strong>+&lt;antal&gt;</strong> lägger till <em>antal</em> av senast scannad vara</li>
				<li><strong>-&lt;antal&gt;</strong> tar bort <em>antal</em> av senast scannad vara</li>
				<li><strong>*&lt;antal&gt;</strong> sätter antalet av senast scannad vara till <em>antal</em></li>
			</ul>
		</div>
	</div>
</form>
<? if($transaction): ?>
	<div id="last_purchase">
		<h2>Föregående köp</h2>
		<?=$transaction->timestamp?>
		<table>
			<tr>
				<td>Att betala</td>
				<td><?=$transaction->amount?> kr</td>
			</tr>
			<tr>
				<td>Mottaget</td>
				<td><?=ClientData::request("last_recieved")?> kr</td>
			</tr>
			<tr>
				<td>Växel</td>
				<td><?=ClientData::request("last_recieved")-$transaction->amount?> kr</td>
			</tr>
		</table>
		<table>
			<? foreach($transaction->TransactionContent(array('@limit' => 5)) as $content): ?>
				<tr>
					<td><?=$content->Product?></td>
					<td><?=$content->count?> st</td>
					<td><?=$content->amount?> kr</td>
				</tr>
			<? endforeach ?>
		</table>
	</div>
<? endif ?>
