<script type="text/javascript">
<!--
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
	<?php foreach($products as $product): ?>
		products.addProduct(
			<?=$product->id?>,
			"<?=$product->ean?>",
			"<?=addslashes(htmlspecialchars_decode($product->name, ENT_QUOTES))?>",
			"[<?=$product->id?>] <?=addslashes(htmlspecialchars_decode($product->name, ENT_QUOTES))?> (<?=$product->price?> kr)",
			<?=$product->count?>,
			<?=$product->price?>,
			<?=$product->active?>
		);
	<?php endforeach ?>
	products.start();
}

window.addEventListener ?
  window.addEventListener('load', start, false) :
  window.attachEvent('onload', start);

--></script>
<h2>Nytt köp</h2>
<form autocomplete="off" id="retail_form" action="/Retail/make" method="post">

	<div id="this_purchase">
		<input type="hidden" name="random" value="<?=get_rand()?>" />
		<h2>Detta köp</h2>
		<table>
			<tr>
				<td>Att betala</td>
				<td class="numeric"><strong id="sum">0 kr</strong></td>
			</tr>
			<tr>
				<td>Öresavrundning</td>
				<td class="numeric"><strong id="diff">0.00 kr</strong></td>
			</tr>
			<tr>
				<td>Mottaget</td>
				<td><input style="font-weight: bold; width: 3.5em;" maxlength="6" tabindex="2" type="text" name="recieved" id="recieved" onkeypress="return keyHook(event);" /> kr</td>
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
<?php if($last_purchase): ?>
	<div id="last_purchase">
		<h2>Föregående köp</h2>
		<?=$last_purchase->timestamp?>
		<table>
			<tr>
				<td>Att betala</td>
				<td class="numeric"><?=$last_purchase->amount?> kr</td>
			</tr>
			<tr>
				<td>Mottaget</td>
				<td class="numeric"><?=$last_recieved?> kr</td>
			</tr>
			<tr>
				<td>Växel</td>
				<td class="numeric"><?=$last_recieved-$last_purchase->amount?> kr</td>
			</tr>
		</table>
		<table>
			<?php foreach($last_purchase->TransactionContent(array('@limit' => 5)) as $content): ?>
				<tr>
					<td><?=$content->Product?></td>
					<td class="numeric"><?=$content->count?> st</td>
					<td class="numeric"><?=$content->amount?> kr</td>
				</tr>
			<?php endforeach ?>
		</table>
	</div>
<?php endif ?>
