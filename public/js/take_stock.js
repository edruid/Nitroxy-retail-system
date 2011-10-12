/**
 * Check if an object is empty (has no members)
 */
function is_empty(obj) {
	for(var i in obj) {
		return false;
	}
	return true;
}

function finish() {
	if(lock) {
		alert("Formuläret är redan skickat, vänta lite till.");
		return false;
	}
	var count_diff = 0;
	var money_diff = 0;
	var count;
	for(var i in products.basket) {
		count = products.basket[i] - products.products[i].count;
		count_diff += count;
		money_diff += count * products.products[i].value;
	}
		
	if(confirm("Godkänn?\nDiff (antal): "+count_diff+" st\nDiff (summa): "+money_diff+" kr")) {
		lock = true;
		var wait = document.getElementById('wait');
		wait.innerHTML = '';
		wait.appendChild(wait_img);
		wait.appendChild(document.createTextNode(' Var god vänta.'));

		document.getElementById('stock_taking_form').submit();
		return false;
	} else {
		return false;
	}
}

