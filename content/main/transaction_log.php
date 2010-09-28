<?php
if(empty($_SESSION['login'])) {
	kick('login?kickback='.htmlspecialchars(kickback_url()));
}
$last_page = ceil(Transaction::count() / 10)-1;
$curr_page = ClientData::request('page');
?>
<h1>Transaktionslogg</h1>
<div>
<? if($curr_page > 0): ?>
	<a href="transaction_log?page=0">&lt;&lt;&lt;</a>
	<a href="transaction_log?page=<?=$curr_page-1?>">föregående</a>
<? else: ?>
	&lt;&lt;&lt;
	föregående
<? endif ?>
<? if($curr_page < $last_page): ?>
	<a href="transaction_log?page=<?=$curr_page+1?>">nästa</a>
	<a href="transaction_log?page=<?=$last_page?>">&gt;&gt;&gt;</a>
<? else: ?>
	nästa
	&gt;&gt;&gt;
<? endif ?>
</div>
<table class="body_border">
	<? foreach(Transaction::selection(array('@order' => 'timestamp:desc', '@limit' => array(ClientData::request('page')*10, 10))) as $transaction): ?>
		<tbody>
			<tr>
				<th colspan="2"><?=$transaction->timestamp?></th>
				<th><?=$transaction->amount?> kr</th>
			</tr>
			<? foreach($transaction->TransactionContent() as $content): ?>
				<tr>
					<td><a href="/product/<?=$content->product_id?>"><?=$content->Product?></a></td>
					<td><?=$content->count?> st</td>
					<td><?=$content->amount?> kr</td>
				</tr>
			<? endforeach ?>
		</tbody>
	<? endforeach ?>
</table>
<div>
<? if($curr_page > 0): ?>
	<a href="transaction_log?page=0">&lt;&lt;&lt;</a>
	<a href="transaction_log?page=<?=$curr_page-1?>">föregående</a>
<? else: ?>
	&lt;&lt;&lt;
	föregående
<? endif ?>
<? if($curr_page < $last_page): ?>
	<a href="transaction_log?page=<?=$curr_page+1?>">nästa</a>
	<a href="transaction_log?page=<?=$last_page?>">&gt;&gt;&gt;</a>
<? else: ?>
	nästa
	&gt;&gt;&gt;
<? endif ?>
</div>
