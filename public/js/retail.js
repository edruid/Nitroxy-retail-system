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
		setTimeout(update_change,5);
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
