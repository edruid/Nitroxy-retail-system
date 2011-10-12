<?php
if(empty($_SESSION['login'])) {
	kick('login?kickback='.htmlspecialchars(kickback_url()));
}
?>
<table class="alternate-body">
	<thead>
		<tr>
			<th>Tid</th>
			<th>Användare</th>
			<th>Konton</th>
			<th>Summa</th>
			<th>Beskrivning</th>
		</tr>
	</thead>
	<?php foreach($transactions as $transaction): ?>
		<?php
			$contents = $transaction->AccountTransactionContent(array(
				'@custom_order' => 'abs(`account_transaction_contents`.`amount`) DESC',
			));
			$num_rows = count($contents);
			$content = array_shift($contents);
			$account = $content->Account;
		?>
		<tbody>
			<tr>
				<td rowspan="<?=$num_rows?>"><a href="/Transaction/view/<?=$transaction->id?>"><?=$transaction->timestamp?></a></td>
				<td rowspan="<?=$num_rows?>"><?=$transaction->User?></td>
				<td><a href="/Account/view/<?=$account->code_name?>"><?=$account?></a></td>
				<td class="numeric"><?=$content->amount?> kr</td>
				<td rowspan="<?=$num_rows?>"><?=$transaction->description?></td>
			</tr>
			<? foreach($contents as $content): ?>
				<?php $account = $content->Account; ?>
				<tr>
					<td><a href="/Account/view/<?=$account->code_name?>"><?=$account?></a></td>
					<td class="numeric"><?=$content->amount?> kr</td>
				</tr>
			<? endforeach ?>
		</tbody>
	<? endforeach ?>
</table>
<p>
	<? if($page > 0): ?>
		<a href="/Transaction/index/<?=$page-1?>">&lt; Senare</a>
	<? endif ?>
	<a href="/Transaction/index/<?=$page+1?>">Tidigare &gt;</a>
</p>
