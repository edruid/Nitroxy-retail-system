<?php
if(isset($_GET['id'])) {
	
} else {
	$dagsavslut = array();
	$stmt = $db->prepare_full("
		SELECT *
		FROM dagsavslut
		ORDER BY id",
		&$dagsavslut
	);
	?>
	<table>
		<caption>dagsavslut</caption>
		<tr>
			<th>Tid</th>
			<th>kassa</th>
			<th>Drycker</th>
			<th>Godis</th>
			<th>Mat</th>
			<th>Frukt</th>
			<th>Övrigt</th>
		</tr>
		<?php
		$last = '1970-01-01 01:00:00'
		while($stmt->fetch()) {
			$ret = array();
			$db->prepare_fetch("
				SELECT
					sum(transaction_contents.amount) as sale,
					class
				FROM
					transactions JOIN
					transaction_contents ON (transaction_contents.transaction_id = transactions.transaction_id) JOIN
					products ON (products.product_id = transaction_contents.product_id)
				WHERE
					transactions.timestamp between ? and ?
				GROUP BY
					products.class
			?><tr>
				<td><?=$dagsavslut['time']?></td>
				<td><?=$dagsavslut['kassa']?></td>
				<td>
		
