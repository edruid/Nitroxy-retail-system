<?php
require_once "../../includes.php";
verify_login(kickback_url('list_categories'));
$category = new Category();
$category->name = ClientData::post('name');
$category->commit();
kick('list_categories');
?>
