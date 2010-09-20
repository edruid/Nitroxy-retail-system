<?php
$categories = Category::selection(array(
	'@order' => 'name',
	'category_id:!=' => 0,
));
?>
<h1>Prislista</h1>
<? foreach($categories as $category): ?>
	<? if($category->Product == array()) continue; ?>
	<h3><?=$category->name?></h3>
	<table>
		<thead>
			<tr>
				<th>Namn</th>
				<th>Pris</th>
			</tr>
		</thead>
		<? if(($products = $category->Product(array('count:>' => 0, '@order' => 'name'))) != array()): ?>
			<tbody>
				<? foreach($products as $product): ?>
					<tr>
						<td><?=$product->name?></td>
						<td><?=$product->price?></td>
					</tr>
				<? endforeach ?>
			</tbody>
		<? endif ?>
		<? if(($products = $category->Product(array('count:<=' => 0, '@order' => 'name'))) != array()): ?>
			<tbody>
				<tr>
					<td colspan="2"><a href="#" onclick="parentNode.parentNode.parentNode.nextSibling.nextSibling.style.display='table-row-group';return false;">Visa även varor som är slut</a></td>
				</tr>
			</tbody>
			<tbody style="display: none;">
				<? foreach($products as $product): ?>
					<tr>
						<td><?=$product->name?></td>
						<td><?=$product->price?></td>
					</tr>
				<? endforeach ?>
			</tbody>
		<? endif ?>
	</table>
<? endforeach ?>
