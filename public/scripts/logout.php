<?php
require "../../includes.php";
$_SESSION['login'] = null;
header("Location: ".$_SERVER['HTTP_REFERER']);
?>
