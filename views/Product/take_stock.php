<script type="text/javascript">
<!--
var products;

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
			minBasketAmount: -1,
			diplayProductListItem: function(product, count) {
				var item=document.createElement('li');
				item.innerHTML=product.name+" [art "+product.id+"]"+
					"<div class=\"product_price\">"+
					count+" st diff: "+(count-product.count)+" = "+(count-product.count)*product.value+" kr"+
					"</div>"+
					'<input type="hidden" name="product_id[]" value="'+product.id+'"/>'+
					'<input type="hidden" name="product_count[]" value="'+count+'" />';
				return item;
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
			true,
			<?=$product->value?>
		);
	<? endforeach ?>
	products.start();
}

window.addEventListener ?
  window.addEventListener('load', start, false) :
  window.attachEvent('onload', start);

--></script>
<h2>Inventering</h2>
<form autocomplete="off" id="stock_taking_form" action="/Product/taken_stock" method="post">
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
