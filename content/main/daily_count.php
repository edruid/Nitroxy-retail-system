<?php
if(empty($_SESSION['login'])) {
	kick();
}
$daily_count = DailyCount::last();
$old_till = TransactionContent::sum('amount', array(
	'Transaction.timestamp:>' => $daily_count->time,
	'Transaction.timestamp:<=' => date('Y-m-d H:i:s'),
));
$old_till += $daily_count->amount;
$account_change = AccountTransactionContent::sum('amount', array(
	'Account.code_name' => 'till',
	'AccountTransaction.timestamp:>' => $daily_count->time,
	'AccountTransaction.timestamp:<=' => date('Y-m-d H:i:s'),
));
$old_till += $account_change;
$sales = Transaction::sum('amount', array('timestamp:>' => $daily_count->time));
$stock_amount = TransactionContent::sum('stock_usage', array(
	'Transaction.timestamp:>' => $daily_count->time,
	'Transaction.timestamp:<=' => date('Y-m-d H:i:s'),
));

?>
<script type="text/javascript">
<!--
	function update_diff(value) {
		var calc = document.getElementById('calculated').innerHTML;
		var diff = document.getElementById('diff');
		var html = ''
		if(value != '') {
			html = value - calc;
		}
		diff.innerHTML = html;

	}
-->
</script>
<h1>Dagsavslut</h1>
<table>
	<tr>
		<th>Senast dagsavslut:</th>
		<td><?=$daily_count->time?></td>
	</tr>
	<tr>
		<th>Försäljning sen senast:</th>
		<td><?=$sales?> kr</td>
	</tr>
	<tr>
		<th>Varuförbrukning sen senast:</th>
		<td><?=$stock_amount?> kr</td>
	</tr>
	<tr>
		<th>Vinst</th>
		<td><?=$sales-$stock_amount?> kr</td>
	</tr>
</table>
<form method="post" action="/scripts/daily_count.php" >
	<table>
		<thead>
			<tr>
				<th>Plats</th>
				<th>Beräknat</th>
				<th>Räknat</th>
				<th>Diff</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<th>I kassan</th>
				<td id="calculated"><?=$old_till?></td>
				<td><input type="text" name="till" onkeyup="update_diff(this.value)" /></td>
				<td id="diff"></td>
			</tr>
			<tr>
				<td colspan="4"><input type="submit" value="bokför" /></td>
			</tr>
		</tbody>
	</table>
</form>
