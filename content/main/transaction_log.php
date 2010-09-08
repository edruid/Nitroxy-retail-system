<h1>Transaktionslogg</h1>
<a href="transaction_log?page=<?=ClientData::request('page')-1?>">&lt; föregående</a> <a href="transaction_log?page=<?=ClientData::request('page')+1?>">nästa &gt;</a>
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
<a href="transaction_log?page=<?=ClientData::request('page')-1?>">&lt; föregående</a> <a href="transaction_log?page=<?=ClientData::request('page')+1?>">nästa &gt;</a>
