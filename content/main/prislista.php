<table>
	<tr>
		<th>Art.nr</th>
		<th>Produkt</th>
		<th>Högsta inköpspris</th>
		<th>Snitt-inköpspris</th>
		<th>Försäljningspris</th>
		<th>Vinst mot högsta inköp</th>
		<th>Vinst m h i %</th>
	</tr>
<?
$stmt=$db->prepare_full("
	SELECT
		product_id,
		name,
		(select max(cost) from delivery_contents where delivery_contents.product_id=products.product_id) as cost,
		(select sum(cost*`count`)/sum(`count`) from delivery_contents where delivery_contents.product_id=products.product_id) as cost,
		price, 
		(select price - cost ) as revenue
	FROM 
		products
	",
	array(&$product_id, &$name, &$max_cost, &$avg_cost, &$price, &$revenue)
);
$products = Product::selection(array(
	'@order' => 'name'
));
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

		<td><a href="/product/<?=$product_id?>"><?=$name?></a></td>

		<?
		if($max_cost <= 0) {
			?><td style="background-color: red;"><?
		} else {
			?><td><?
		}
		?>
		<?=$max_cost?></td>

		<td><?=$avg_cost?></td>

		<td><?=$price?></td>

		<?
		if($revenue < 1) {
			?><td style="background-color: red;"><?
		} else {
			?><td><?
		}
		?>
		<?=$revenue?></td>

		<?
		$percent=@round($revenue / $max_cost * 100);
		if($percent < 10) {
			?><td style="background-color: red;"><?
		} else {
			?><td><?
		}
		?>
		<?=$percent?> %</td>
	</tr>
	<?
}
?>
</table>
