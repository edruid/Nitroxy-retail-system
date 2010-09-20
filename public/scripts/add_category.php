<?php
require_once "../../includes.php";
if(empty($_SESSION['login'])) {
	kick('login?kickback='.kickback_url('list_categories'));
}
$category = new Category();
$category->name = ClientData::post('name');
$category->commit();
kick('list_categories');
?>
