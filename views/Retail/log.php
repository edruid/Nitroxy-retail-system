<h1>Transaktionslogg</h1>
<?php self::_partial('Helper/pager', array('/Retail/log/%d', $page, $last_page)) ?>
<table class="body_border">
	<?php foreach($transactions as $transaction): ?>
		<tbody>
			<tr>
				<th colspan="2"><?=$transaction->timestamp?></th>
				<th class="numeric"><?=$transaction->amount?> kr</th>
			</tr>
			<?php foreach($transaction->TransactionContent() as $content): ?>
				<tr>
					<td>
						<a href="/Product/view/<?=$content->product_id?>">
							<?=$content->Product?>
						</a>
					</td>
					<td class="numeric"><?=$content->count?> st</td>
					<td class="numeric"><?=$content->amount?> kr</td>
				</tr>
			<?php endforeach ?>
		</tbody>
	<?php endforeach ?>
</table>
<?php self::_partial('Helper/pager', array('/Retail/log/%d', $page, $last_page)) ?>
