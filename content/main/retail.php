<script type="text/javascript" src="/js/purchase.js"></script>
<script type="text/javascript">
<!--
var names=new Array();
var suggestions=new Array();
var prices=new Array();
var eans=new Array();

<?php
$products = Product::selection(array(
	'category_id:!=' => 0,
	'@order' => 'product_id',
));
foreach($products as $product) {
	?>
	names[<?=$product->id?>]="<?=addslashes(htmlspecialchars_decode($product->name, ENT_QUOTES))?>";
	<? if($product->active): ?>
		suggestions[<?=$product->id?>]="[<?=$product->id?>] <?=addslashes(htmlspecialchars_decode($product->name, ENT_QUOTES))?> (<?=$product->price?> kr)";
	<? endif ?>
	prices[<?=$product->id?>]=<?=$product->price?>;
	eans['<?=strtolower($product->ean)?>']="<?=$product->id?>";
	<?
}
$transaction = Transaction::last();
?>
--></script> 
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
				<td><input style="font-weight: bold; width: 6em;" maxlength="6" tabindex="2" type="text" name="recieved" id="recieved" onkeypress="return key_hook(event);" /> kr</td>
			</tr>
			<tr>
				<td>Växel</td>
				<td><strong id="change"></strong></td>
			</tr>
		</table>
	</div>
	
	<div style="float: left;">
		<select name="product_list" disabled="disabled" id="product_list" size="15" >
			<option value="" disabled="disabled">&nbsp;</option>
		</select>
		<p>
			<label>
				EAN-kod, artikelnummer eller beskrivning<br />
				<input type="text" tabindex="1" name="ean" id="ean" />
				<span id="suggest"></span>
			</label>
			<input type="submit" onclick="purchase(); return false;" value="OK" />
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
