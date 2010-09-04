<?php
$categories = Category::selection(array(
	'@order' => 'name',
));
?>
<h1>Prislista</h1>
<? foreach($categories as $category): ?>
	<h3><?=$category->name?></h3>
	<table>
		<thead>
			<tr>
				<th>Namn</th>
				<th>Pris</th>
			</tr>
		</thead>
		<tbody>
			<? foreach($category->Product(array('count:>' => 0, '@order' => 'name')) as $product): ?>
				<tr>
					<td><?=$product->name?></td>
					<td><?=$product->price?></td>
				</tr>
			<? endforeach ?>
		</tbody>
		<tbody>
			<tr>
				<td colspan="2"><a href="#" onclick="parentNode.parentNode.parentNode.nextSibling.nextSibling.style.display='table-row-group';return false;">Visa även varor som är slut</a></td>
			</tr>
		</tbody>
		<tbody style="display: none;">
			<? foreach($category->Product(array('count:<=' => 0, '@order' => 'name')) as $product): ?>
				<tr>
					<td><?=$product->name?></td>
					<td><?=$product->price?></td>
				</tr>
			<? endforeach ?>
		</tbody>
	</table>
<? endforeach ?>
