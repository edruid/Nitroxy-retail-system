<?php
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
		<link type="text/css" rel="stylesheet" href="<?=absolute_path('style.css'); ?>" />
		<script type="text/javascript" src="<?=absolute_path('js/dom.js')?>"></script>
		<script type="text/javascript" src="/js/sort.js"></script>
		<script type="text/javascript">
		<!--
		function set_initial_focus() {
			var elem = document.getElementById('initial_focus');
			if(elem) {
				elem.focus();
			}
		}
		window.onload = set_initial_focus;
		-->
		</script>
	</head>
	<body>
		<? foreach(Message::get_errors() as $error): ?>
			<p class="error"><?=$error?></p>
		<? endforeach ?>
		<ul class="menu">
			<li><a href="/retail">Handla</a></li>
			<li><a href="/price_list">Prislista</a></li>
			<? if(!empty($_SESSION['login'])): ?>
				<li class="dir">
					<a href="/stock">Lager</a>
					<ul>
						<li class="dir">
							<a href="/list_categories">Kategorier</a>
							<ul>
								<? foreach(Category::selection() as $category): ?>
									<li><a href="/category/<?=$category->id?>"><?=$category?></a></li>
								<? endforeach ?>
							</ul>
						</li>
						<li><a href="/delivery">Ny leverans</a></li>
						<li><a href="/list_deliveries">Leveranser</a></li>
						<li><a href="/correct_product">Inventering</a></li>
					</ul>
				</li>
				<li class="dir">
					<a href="/accounts">Bokföring</a>
					<ul>
						<li class="dir">
							<a href="/accounts">Konton</a>
							<ul>
								<? foreach(Account::selection() as $account): ?>
									<li title="<?=$account->description?>">
										<a href="/account/<?=$account->id?>">
											<?=$account?>
										</a>
									</li>
								<? endforeach ?>
							</ul>
						</li>
						<li><a href="/daily_count">Dagsavslut</a></li>
						<li><a href="/money_transfer">Ny transaktion</a></li>
						<li><a href="/account_transactions">Verifikationslista</a></li>
						<li><a href="/transaction_log">Transaktionslogg</a></li>
					</ul>
				</li>
				<li>
					<form action="/scripts/logout.php" method="post">
						<div>
							<input type="submit" value="Logga ut" />
						</div>
					</form>
				</li>
			<? else: ?>
				<li><a href="/login?kickback=<?=htmlspecialchars(kickback_url())?>">Logga in</a></li>
			<? endif ?>
		</ul>
		</div>
		<?require $main?>
	</body>
</html>

