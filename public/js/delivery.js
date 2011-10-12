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
	var row_sums = table.getElementsByClassName('row-sum');
	var per_product = !(document.getElementById('product_type').checked);
	var multiplyer = document.getElementById('multiplyer').value;
	var sum = 0;
	for(var i=0; i< counts.length; ++i) {
		if(per_product) {
			row_sum = counts[i].value * prices[i].value * multiplyer;
			
		} else {
			row_sum = prices[i].value * multiplyer;
		}
		row_sums[i].innerHTML = row_sum;
		sum += row_sum;
	}
	document.getElementById('sum').innerHTML = sum;
}
