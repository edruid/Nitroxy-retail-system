<table>
	<tr>
		<th>Art.nr</th>
		<th>Produkt</th>
		<th>Inleverans</th>
		<th>Försäljning</th>
		<th>Lagersaldo</th>
	</tr>
<?
$stmt=$db->prepare_full("
	SELECT product_id, name, (select sum(count) from delivery_contents where delivery_contents.product_id=products.product_id) as delivered, (select sum(count) from transaction_contents where transaction_contents.product_id=products.product_id )as sold, (select delivered - sold) as stock from products",
	array(&$product_id, &$name, &$delivered, &$sold, &$stock)
);
$counter=0;
while($stmt->fetch()) {
	$counter++;
	$style="";
	if($counter % 2) {
		$style=" background-color: rgb(200,200,200);";
	}
		
	?>
	<tr style="<?=$style?>">
		<td><?=$product_id?></td>
		<td><?=$name?></td>
		<td><?=$delivered?></td>
		<td><?=$sold?></td>
		<?
		if($stock < 0) {
			?><td style="background-color: red;"><?
		} else {
			?><td><?
		}
		?>
		<?=$stock?></td>
	</tr>
	<?
}
?>
</table>
