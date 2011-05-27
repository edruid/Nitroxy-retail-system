<?php
if(empty($_SESSION['login'])) {
	kick('login?kickback='.htmlspecialchars(kickback_url()));
}
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
			hookOnEmptyProduct: function() {
				if(!is_empty(this.basket) && finish()) {
					return true;
				}
				return false;
			},
			minBasketAmount: -1
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
			true,
			<?=$product->value?>
		);
	<? endforeach ?>
	products.start();
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

window.addEventListener ?
  window.addEventListener('load', start, false) :
  window.attachEvent('onload', start);

--></script>
<h2>Inventering</h2>
<form autocomplete="off" id="stock_taking_form" action="scripts/take_stock.php" method="post">
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
