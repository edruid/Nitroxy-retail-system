<script type="text/javascript">
<!--
var names=new Array();
var suggestions=new Array();
var prices=new Array();
var eans=new Array();
var lock = false;

<?php
$products = Product::selection(array(
	'category_id:!=' => 0,
	'@order' => 'product_id',
));
foreach($products as $product) {
	?>
	names[<?=$product->id?>]="<?=addslashes(htmlspecialchars_decode($product->name, ENT_QUOTES))?>";
	suggestions[<?=$product->id?>]="[<?=$product->id?>] <?=addslashes(htmlspecialchars_decode($product->name, ENT_QUOTES))?> (<?=$product->price?> kr)";
	prices[<?=$product->id?>]=<?=$product->price?>;
	eans['<?=strtolower($product->ean)?>']="<?=$product->id?>";
	<?
}
?>

var basket=new Array();

// finnished inputing ean, pressed enter. Adding product to list or, if shift is pressed,
// deleting from list.
var last_product = null;
function purchase() {
	var input_ean=document.getElementById('ean');
	var ean=input_ean.value;
	var amount=1;
	var artno = null;
	ean=ean.toLowerCase();
	if(ean=="") {
		if(basket.length != 0) {
			document.getElementById('recieved').focus();
		}
		return false;
	}

	if(names[ean]==undefined) {
		if(eans[ean] != undefined) {
			artno = eans[ean];
		}
		for(var i=0;i<suggestions.length;i++) {
			if(suggestions[i]!=undefined) {
				var sugg=suggestions[i];
				sugg=sugg.toLowerCase();
				if(sugg==ean) {
					artno=i;
				}
			}
		}

		if(last_product && ean.match(/^[\+\*\-][0-9]+$/)) {
			var sign = ean.substr(0,1);
			artno = last_product;
			amount = parseInt(ean.substr(1));
			if(basket[artno] == undefined) {
				basket[artno] = 0;
			}
			if(sign == '*') {
				basket[artno]=amount;
			} else if(sign == '+') {
				basket[artno]=basket[artno]+amount;
			} else if(sign == '-') {
				basket[artno]=basket[artno]-amount;
			}
			if(basket[artno] <= 0) {
				basket[artno] = undefined;
			}
			update_product_list();
			update_sum();
			input_ean.value='';
			return true;
		}

		if(artno==null) {
			// Input är ej art.nr eller EAN
			alert("Oväntad inmatning - ej artikelnummer eller EAN");
			return false;
		}
	} else {
		// Art.nr givet
		var artno=ean;
	}
	last_product = artno;

	if(basket[artno]==undefined) {
		basket[artno]=amount;
	} else {
		basket[artno]=basket[artno]+amount;
	}

	update_product_list();
	update_sum();
	input_ean.value='';
	return true;
}

// redraw the product_list select box from the basket
function update_product_list() {
	var product_list=document.getElementById('product_list');

	product_list.innerHTML='';

	for(var i=0;i<basket.length;i++) {
		if(basket[i]!=undefined && basket[i]!=NaN) {
			var namn=document.createElement('option');
			namn.text=names[i]+" [art "+i+"]";
			product_list.add(namn,null);

			var pris=document.createElement('option');
			pris.text=basket[i]+" st * "+prices[i]+" kr = "+basket[i]*prices[i]+" kr";
			product_list.add(pris,null);

		}
	}
}

var sum;

// recalculate the price for the basket
function update_sum() {
	var calc_amount=0;
	for(var i=0;i<basket.length;i++) {
		if(basket[i]!=undefined && basket[i]!=NaN) {
			if(prices[i]==undefined || prices[i]==NaN) {
				alert("Ett fel uppstod: inget pris är definierat för artikel "+i);
			} else {
				calc_amount+=basket[i]*prices[i];
			}
		}
	}

	var diff = calc_amount;
	calc_amount = Math.round(calc_amount);
	diff = calc_amount - diff;
	document.getElementById('transaction_diff').value=diff;

	var sum_elem=document.getElementById('sum');
	sum_elem.innerHTML=calc_amount+" kr";

	var diff_elem = document.getElementById('diff');
	diff_elem.innerHTML = diff.toFixed(2)+" kr";

	sum=calc_amount;
	update_change();
}

// Update change field when a new digigt is added to the payed field.
function update_change(e) {
	var recieved_elem=document.getElementById('recieved');
	var change_elem=document.getElementById('change');
	var keynum;
	var keychar;
	if(e!=undefined) {
		if(window.event) // IE
			{
			keynum = e.keyCode;
			}
		else if(e.which) // Netscape/Firefox/Opera
			{
			keynum = e.which;
			}
		keychar = String.fromCharCode(keynum);
		if(keynum==44) {
			// Kommatecken - ersätt med punkt
			recieved_elem.value=recieved_elem.value+'.';
			return false;
		}
		if(keynum==46) {
			// Punkt - ignorera
			return true;
		}
		var finish_transaction=false;
		if(keynum==13) {
			// Enter - slutför köp
			return finish(sum, recieved_elem.value, change_elem.innerHTML);
		}
		if(keynum==undefined) {
			// Det här verkar bara hända med TAB
			// Ignorera
			return true;
		}
		if(keynum==8) {
			// Backspace - låt användaren göra som den vill (return true),
			// och kolla hur det blev om 5 ms.
			setTimeout("update_change()",5);
			return true;
		}
		var numcheck = /\d/;
		if(!numcheck.test(keychar)) {
			alert("Var god mata endast in siffror och punkt i fältet. Du tryckte på tangent "+keynum+": "+keychar);
			return false;
		}
	}

	// Tolka inmatningen så att vi kan beräkna från variabeln recieved
	if(keychar!=undefined && keynum!=13) {
		var recieved=recieved_elem.value+keychar;
	} else {
		var recieved=recieved_elem.value;
	}

	// Visa inte växel innan mottaget belopp har börjat matas in
	if(recieved==undefined || recieved=="" || (recieved==0 && basket.length == 0)) {
		var change=0;
		change_elem.innerHTML="";
	} else {
		var change=recieved-sum;
		change_elem.innerHTML=change+" kr";
	}

	// Sätt rätt färg på växelbeloppet
	if(change < 0) {
		// Change ska vara röd
		change_elem.style.color = "red";
	} else {
		// Change ska vara grön
		change_elem.style.color = "green";
	}

	return true;
}

// Finish transaction (submit form from recieved field)
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
	if(confirm("Godkänn?\nAtt betala: "+sum+"\nBetalt: "+recieved+"\nVäxel: "+change)) {
		lock = true;
		document.getElementById('wait').innerHTML = '<img src="/gfx/loading.gif" alt="wait" /> Var god vänta.';
		var form_elem=document.getElementById('transaction_form');
		var sum_elem=document.getElementById('transaction_sum');
		var recieved_elem=document.getElementById('transaction_recieved');
		var change_elem=document.getElementById('transaction_change');
		var contents_elem=document.getElementById('transaction_contents');

		sum_elem.value=sum;
		recieved_elem.value=recieved;
		change_elem.value=change;

		var contents="";
		for(var i=0; i<basket.length; i++) {
			if(basket[i]!=undefined && basket[i]!=NaN) {
				contents = contents + i + ":" + basket[i] + "\n";
			}
		}
		contents_elem.value=contents;

		form_elem.submit();
	} else {
		return false;
	}
	return true;
}
--></script> 
<h1>NitroXy Retail System</h1>
<hr />
<h2>Nytt köp</h2>
<form autocomplete="off" action="" onsubmit="return false;">

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
				<td><input style="font-weight: bold; width: 6em;" maxlength="6" tabindex="2" type="text" name="recieved" id="recieved" onkeypress="return update_change(event);" /> kr</td>
			</tr>
			<tr>
				<td>Växel</td>
				<td><strong id="change"></strong></td>
			</tr>
		</table>
	</div>
	
	<div style="float: left;">
		<select name="product_list" disabled="disabled" id="product_list" size="15" ></select>
		<p>
			<label>
				EAN-kod, artikelnummer eller beskrivning<br />
				<input type="text" tabindex="1" name="ean" id="ean" />
				<span id="suggest"></span>
			</label>
			<input type="submit" onclick="purchase(); return false;" value="OK" />
		</p>
		<ul id="wait" class="help">
			<li><strong>+&lt;antal&gt;</strong> lägger till <em>antal</em> av senast scannad vara</li>
			<li><strong>-&lt;antal&gt;</strong> tar bort <em>antal</em> av senast scannad vara</li>
			<li><strong>*&lt;antal&gt;</strong> sätter antalet av senast scannad vara till <em>antal</em></li>
		</ul>
	</div>
</form>
<? if(ClientData::request("last_sum") !== false): ?>
	<div id="last_purchase">
		<h2>Föregående köp</h2>
		<table>
			<tr>
				<td>Att betala</td>
				<td><?=ClientData::request("last_sum")?> kr</td>
			</tr>
			<tr>
				<td>Mottaget</td>
				<td><?=ClientData::request("last_recieved")?> kr</td>
			</tr>
			<tr>
				<td>Växel</td>
				<td><?=ClientData::request("last_change")?> kr</td>
			</tr>
		</table>
		<table>
			<? foreach(TransactionContent::selection(array(
						'transaction_id' => ClientData::request("last_transaction"),
						'@limit' => 5,
					)) as $content): ?>
				<tr>
					<td><?=$content->Product?></td>
					<td><?=$content->count?> st</td>
					<td><?=$content->amount?> kr</td>
				</tr>
			<? endforeach ?>
		</table>
	</div>
<? endif ?>
<form id="transaction_form" method="post" action="<?=absolute_path("scripts/finish_transaction.php");?>" style="display: none;">
	<div>
		<input type="hidden" id="transaction_diff" name="diff" value="0" />
		<input type="text" id="transaction_sum" name="sum" />
		<input type="text" id="transaction_recieved" name="recieved" />
		<input type="text" id="transaction_change" name="change" />
		<input type="text" name="contents" id="transaction_contents" />
	</div>
</form>
<script type="text/javascript" src="<?=absolute_path('js/suggest.js')?>"></script>
<script type="text/javascript">
document.getElementById('ean').focus();

function startSuggest() {
	new Suggest.Local(
		"ean",    // input element id.
		"suggest", // suggestion area id.
		suggestions,      // suggest candidates list
		{
			dispMax: 20,
			interval: 200,
			highlight: true,
			hookBeforeSearch: function(text) {
				return !text.match(/^[\*\+\-][0-9]*$/);
			}
		}); // options
}

window.addEventListener ?
  window.addEventListener('load', startSuggest, false) :
  window.attachEvent('onload', startSuggest);

</script>
