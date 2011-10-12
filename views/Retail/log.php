<h1>Transaktionslogg</h1>
<div>
<? if($page > 0): ?>
	<a href="/Retail/log">&lt;&lt;&lt;</a>
	<a href="/Retail/log/<?=$page-1?>">föregående</a>
<? else: ?>
	&lt;&lt;&lt;
	föregående
<? endif ?>
<? if($page < $last_page): ?>
	<a href="/Retail/log/<?=$page+1?>">nästa</a>
	<a href="/Retail/log/<?=$last_page?>">&gt;&gt;&gt;</a>
<? else: ?>
	nästa
	&gt;&gt;&gt;
<? endif ?>
</div>
<table class="body_border">
	<? foreach($transactions as $transaction): ?>
		<tbody>
			<tr>
				<th colspan="2"><?=$transaction->timestamp?></th>
				<th class="numeric"><?=$transaction->amount?> kr</th>
			</tr>
			<? foreach($transaction->TransactionContent() as $content): ?>
				<tr>
					<td>
						<a href="/Product/view/<?=$content->product_id?>">
							<?=$content->Product?>
						</a>
					</td>
					<td class="numeric"><?=$content->count?> st</td>
					<td class="numeric"><?=$content->amount?> kr</td>
				</tr>
			<? endforeach ?>
		</tbody>
	<? endforeach ?>
</table>
<div>
<? if($page > 0): ?>
	<a href="/Retail/log">&lt;&lt;&lt;</a>
	<a href="/Retail/log/<?=$page-1?>">föregående</a>
<? else: ?>
	&lt;&lt;&lt;
	föregående
<? endif ?>
<? if($page < $last_page): ?>
	<a href="/Retail/log/<?=$page+1?>">nästa</a>
	<a href="/Retail/log/<?=$last_page?>">&gt;&gt;&gt;</a>
<? else: ?>
	nästa
	&gt;&gt;&gt;
<? endif ?>
</div>
