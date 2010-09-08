<?
define('HTML_ACCESS', true);
require "../includes.php";

// Prepare path
$path_info=$_SERVER['PATH_INFO'];
$untouched_request=$path_info;
$request=explode('/',$path_info);
array_shift($request);
$main=array_shift($request);
$simple_main=$main;
$main="../content/main/".basename($main).".php";
if(!file_exists($main)) {
	$main="../content/main/".$application['default_content'];
}
?>
<?="<?xml version=\"1.0\" encoding=\"utf-8\"?>"?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
	<head>
		<title><?=$application['name']?></title>
		<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
		<link rel="stylesheet" href="<?=absolute_path('style.css'); ?>" />
		<script type="text/javascript" src="<?=absolute_path('js/dom.js')?>"></script>
	</head>
	<body>
		<? foreach(Message::get_errors() as $error): ?>
			<p class="error"><?=$error?></p>
		<? endforeach ?>
		<? if(!empty($_SESSION['login'])): ?>
			<a href="/scripts/logout.php">Logga ut</a>
		<? endif ?>
		<?require $main?>
	</body>
</html>

