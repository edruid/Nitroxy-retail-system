var basket=new Object();
var last_product = null;
var sum = 0;
var lock = false;
var wait_img = new Image();
wait_img.src="/gfx/loading.gif";
wait_img.alt="wait";

/**
 * Check if an object is empty (has no members)
 */
function is_empty(obj) {
	for(var i in obj) {
		return false;
	}
	return true;
}

/**
 * Searches for a product and returns the product_id or null.
 * @param string input an input that can be an EAN, a product_id or
 *  an identical text to a suggestion of the product.
 * @return the product_id or null.
 */
function get_product(input) {
	if(eans[input] != undefined) {
		// ean of product in input	
		return eans[input];
	}
	if(names[input]!=undefined) {
		// id of product in input
		return input;
	}
	for(var i=0;i<suggestions.length;i++) {
		if(suggestions[i]!=undefined) {
			var sugg=suggestions[i];
			sugg=sugg.toLowerCase();
			if(sugg==input) {
				// There was a suggested product with this name.
				return i;
			}
		}
	}
	return null;
}

/**
 * Update basket with the product specified in the ean field.
 * If ean is empty and basket is non-empty, focus is shifted to
 * the recieved field.
 * If input was not recognized an error message is shown.
 */
function purchase() {
	var input_field=document.getElementById('ean');
	var input=input_field.value.toLowerCase();
	var amount=0;
	var artno = null;
	if(input=="") {
		if(!is_empty(basket)) {
			document.getElementById('recieved').focus();
		}
		return;
	}
	if(last_product && input.match(/^[\+\*\-][0-9]+$/)) {
		var sign = input.substr(0,1);
		artno = last_product;
		amount = parseInt(input.substr(1));
		if(basket[artno] == undefined) {
			basket[artno] = 0;
		}
		if(sign == '*') {
			amount = amount - basket[artno];
		} else if(sign == '-') {
			amount = -1 * amount;
		}
	} else {
		artno = get_product(input);
		amount = 1;
	}
	if(artno==null) {
		// Input är ej art.nr eller EAN
		alert("Oväntad inmatning - ej artikelnummer eller EAN");
		return;
	}

	last_product = artno;
	if(basket[artno]==undefined) {
		basket[artno]=amount;
	} else {
		basket[artno]=basket[artno]+amount;
	}
	if(basket[artno] <= 0) {
		delete basket[artno];
	}
	update_product_list();
	update_sum();
	input_field.value='';
}

/**
 * Redraws the product_list selection box.
 */
function update_product_list() {
	var product_list=document.getElementById('product_list');

	product_list.innerHTML='';

	for(var i in basket) {
		if(basket[i]!=NaN) {
			var namn=document.createElement('option');
			namn.text=names[i]+" [art "+i+"]";
			product_list.add(namn,null);

			var pris=document.createElement('option');
			pris.text=basket[i]+" st * "+prices[i]+" kr = "+basket[i]*prices[i]+" kr";
			product_list.add(pris,null);

		}
	}
}

/**
 * Updates the sum owed for the basket.
 */
function update_sum() {
	var calc_amount=0;
	for(var i in basket) {
		if(basket[i]!=NaN) {
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

/**
 * Takes a keyPressed event in recieved amount and deciedes what
 * to do with it.
 */
function key_hook(e) {
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
 * Checks if a key pressed event is a comma ',' and replaces it with
 * a dot '.'.
 * @param event e The event that was fired.
 * @param inputElement elem The element that recieved the event.
 * @return bool if a comma was replaced, true is returned.
 */
function fix_comma(e, elem) {
	var keynum, keychar;
	if(window.event) { // IE
		keynum = e.keyCode;
	} else if(e.which) { // Netscape/Firefox/Opera
		keynum = e.which;
	}
	keychar = String.fromCharCode(keynum);
	if(keychar== ',') {
		var str = elem.value;
		var pos = elem.selectionStart+1
		elem.value = str.substr(0,elem.selectionStart) + '.' + str.substr(elem.selectionEnd);
		elem.selectionStart = pos;
		elem.selectionEnd = pos;
		return true;
	}
	return false;
}

/**
 * Update the change element with the amount to be returned.
 */
function update_change() {
	var recieved=document.getElementById('recieved').value;
	var change_elem=document.getElementById('change');
	change_elem.innerHTML = recieved-sum + " kr";
	if(recieved-sum < 0) {
		change_elem.style.color = "red";
	} else {
		change_elem.style.color = "green";
	}
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
	javascript:void((new Image()).src='http://localhost/index.php:1138?random=' + Math.random());
	if(confirm("Godkänn?\nAtt betala: "+sum+"\nBetalt: "+recieved+"\nVäxel: "+change)) {
		lock = true;
		var wait = document.getElementById('wait');
		wait.innerHTML = '';
		wait.appendChild(wait_img);
		wait.appendChild(document.createTextNode(' Var god vänta.'));
		var form_elem=document.getElementById('transaction_form');
		var sum_elem=document.getElementById('transaction_sum');
		var recieved_elem=document.getElementById('transaction_recieved');
		var change_elem=document.getElementById('transaction_change');
		var contents_elem=document.getElementById('transaction_contents');

		sum_elem.value=sum;
		recieved_elem.value=recieved;
		change_elem.value=change;

		var contents="";
		for(var i in basket) {
			if(basket[i]!=NaN) {
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
