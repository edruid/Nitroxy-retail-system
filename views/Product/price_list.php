<h1>Prislista</h1>
<?php foreach($categories as $category): ?>
	<?php if($category->Product == array()) continue; ?>
	<h3><?=$category->name?></h3>
	<table>
		<thead>
			<tr>
				<th>Namn</th>
				<th>Pris</th>
			</tr>
		</thead>
		<?php
			$products = $category->Product(array(
				'count:>' => 0, 
				'@order'  => 'name',
			));
		?>
		<?php if(!empty($products)): ?>
			<tbody>
				<?php foreach($products as $product): ?>
					<tr>
						<td><?=$product->name?></td>
						<td><?=$product->price?></td>
					</tr>
				<?php endforeach ?>
			</tbody>
		<?php endif ?>

		<?php
			$products = $category->Product(array(
				'count:<=' => 0, 
				'@order'  => 'name',
			));
		?>
		<?php if(!empty($products)): ?>
			<tbody>
				<tr>
					<td colspan="2"><a href="#" onclick="parentNode.parentNode.parentNode.nextSibling.nextSibling.style.display='table-row-group';return false;">Visa även varor som är slut</a></td>
				</tr>
			</tbody>
			<tbody style="display: none;">
				<?php foreach($products as $product): ?>
					<tr>
						<td><?=$product->name?></td>
						<td><?=$product->price?></td>
					</tr>
				<?php endforeach ?>
			</tbody>
		<?php endif ?>
	</table>
<?php endforeach ?>
