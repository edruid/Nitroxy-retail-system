<h1>Transaktion - <?=$transaction->description?></h1>
<table>
	<tr>
		<th>Användare</th>
		<td><?=$transaction->User?></td>
	</tr>
	<tr>
		<th>Tid</th>
		<td><?=$transaction->timestamp?></td>
	</tr>
</table>
<table>
	<thead>
		<tr>
			<th>Konto</th>
			<th>Summa</th>
		</tr>
	</thead>
	<tbody>
		<?php foreach($contents as $content): ?>
			<?php $account = $content->Account ?>
			<tr>
				<td><a href="/Account/view/<?=$account->code_name?>"><?=$account?></a></td>
				<td class="numeric"><?=$content->amount?> kr</td>
			</tr>
		<?php endforeach ?>
	</tbody>
</table>
