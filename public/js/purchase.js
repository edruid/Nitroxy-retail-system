var lock = false;
var wait_img = new Image();
wait_img.src="/gfx/loading.gif";
wait_img.alt="wait";

var Products = function() {
	this.initialize.apply(this, arguments);
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

Products.prototype = {
	initialize: function(productList, suggestArea, productField) {
		this.productList = this._getElement(productList);
		this.suggestArea = this._getElement(suggestArea);
		this.productField = this._getElement(productField);
		this.basket = new Object;
		this.last_product = null;
		this.sum = 0;
		this.minBasketAmount = 0;
		this.products = Array();
		this.suggestions = Array();
		this.eans = Array();
		this._addEvent(
			this.productField,
			'submit',
			this._bindEvent(this.submitProductForm)
		);
		this.hookBeforeAddProduct = function() {return true;}
		this.hookOnEmptyProduct = function() {return true;}
		this.hookOnUpdatedProductList = function() {};
		if(arguments[3]) {
			for(var i in arguments[3]) {
				this[i] = arguments[3][i];
			}
		}
	},

	addProduct: function(id, ean, name, suggest_text, count, price, suggested, value) {
		this.products[id] = {
			id: id,
			ean: ean,
			name: name,
			suggest: suggest_text,
			count: count,
			price: price,
			value: value,
		};
		if(suggested) {
			this.suggestions[id] = suggest_text;
		}
		this.eans[ean] = id;
	},

	start: function() {
		new Suggest.Local(
			this.productField,
			this.suggestArea,
			this.suggestions,
			{
				dispMax: 20,
				interval: 200,
				highlight: true,
				hookBeforeSearch: function(text) {
					return !text.match(/^[\*\+\-]\d*$/);
				}
			});
	},

	/**
	 * Searches for a product and returns the product_id or null.
	 * @param string input an input that can be an EAN, a product_id or
	 *  an identical text to a suggestion of the product.
	 * @return the product_id or null.
	 */
	getProduct: function(input) {
		if(this.eans[input] != undefined) {
			// ean of product in input	
			return this.eans[input];
		}
		if(this.products[input]!=undefined) {
			// id of product in input
			return input;
		}
		for(var product in this.products) {
			if(product != undefined && this.products[product].suggest.toLowerCase()==input) {
				// There was a suggested product with this name.
				return product;
			}
		}
		return null;
	},

	/**
	 * Update basket with the product specified in the ean field.
	 * If ean is empty and basket is non-empty, focus is shifted to
	 * the recieved field.
	 * If input was not recognized an error message is shown.
	 */
	addToBasket: function() {
		var input=this.productField.value.toLowerCase();
		var amount=0;
		var artno = null;
		var result = this.hookBeforeAddProduct(input);
		if(result == false) {
			return;
		} else if(typeof result == String) {
			input = result;
		}
		if(input=="") {
			if(this.hookOnEmptyProduct() == false) {
				return;
			}
		}
		if(this.last_product && input.match(/^[\+\*\-][0-9]+$/)) {
			var sign = input.substr(0,1);
			artno = this.last_product;
			amount = parseInt(input.substr(1));
			if(this.basket[artno] == undefined) {
				this.basket[artno] = 0;
			}
			if(sign == '*') {
				amount = amount - this.basket[artno];
			} else if(sign == '-') {
				amount = -1 * amount;
			}
		} else {
			artno = this.getProduct(input);
			amount = 1;
		}
		if(artno==null) {
			// Input är ej art.nr eller EAN
			alert("Oväntad inmatning - ej artikelnummer eller EAN");
			return;
		}

		this.last_product = artno;
		if(this.basket[artno]==undefined) {
			this.basket[artno]=amount;
		} else {
			this.basket[artno]=this.basket[artno]+amount;
		}
		if(this.basket[artno] <= this.minBasketAmount) {
			delete this.basket[artno];
		}
		this.updateProductList();
		this.hookOnUpdatedProductList();
		this.productField.value='';
	},

	/**
	 * Redraws the product_list selection box.
	 */
	updateProductList: function() {
		this.productList.innerHTML='';

		for(var i in this.basket) {
			if(this.basket[i]!=NaN) {
				var product = this.products[i];
				var namn=document.createElement('li');
				namn.innerHTML=product.name+" [art "+i+"]"+
					"<div class=\"product_price\">"+
					this.basket[i]+" st * "+product.price+" kr = "+this.basket[i]*product.price+" kr"+
					"</div>"+
					'<input type="hidden" name="product_id[]" value="'+i+'"/>'+
					'<input type="hidden" name="product_price[]" value="'+product.price+'"/>'+
					'<input type="hidden" name="product_count[]" value="'+this.basket[i]+'" />';
				this.productList.appendChild(namn);


			}
		}
	},

	_getElement: function(element) {
		return (typeof element == 'string') ?
			document.getElementById(element) :
			element;
	},
	
	_addEvent: (window.addEventListener ?
		function(element, type, func) {
			element.addEventListener(type, func, false);
		} :
		function(element, type, func) {
			element.attachEvent('on' + type, func);
		}
	),
	
	_bind: function(func) {
		var self = this;
		var args = Array.prototype.slice.call(arguments, 1);
		return function(){ func.apply(self, args); };
	},
	_bindEvent: function(func) {
		var self = this;
		var args = Array.prototype.slice.call(arguments, 1);
		return function(event){ event = event || window.event; func.apply(self, [event].concat(args)); };
	},
};

