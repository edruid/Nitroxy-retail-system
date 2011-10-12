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
		<td class="numeric"><?=number($sales)?> kr</td>
	</tr>
	<tr>
		<th>Varuförbrukning sen senast:</th>
		<td class="numeric"><?=number($stock_amount)?> kr</td>
	</tr>
	<tr>
		<th>Vinst</th>
		<td class="numeric"><?=number($sales-$stock_amount)?> kr</td>
	</tr>
</table>
<form method="post" action="/DailyCount/make">
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
				<td id="calculated" class="numeric"><?=number($old_till)?></td>
				<td>
					<input type="text" name="till" onkeyup="update_diff(this.value)" onkeypress="fix_comma(event, this);" />
					<input type="hidden" name="random" value="<?=rand()?>" />
				</td>
				<td id="diff"></td>
			</tr>
			<tr>
				<td colspan="4"><input type="submit" value="bokför" /></td>
			</tr>
		</tbody>
	</table>
</form>
