<?="<?xml version=\"1.0\" encoding=\"utf-8\"?>"?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
	<head>
		<title><?=$title?></title>
		<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
		<link type="text/css" rel="stylesheet" href="/style.css" />
		<script type="text/javascript" src="/js/dom.js"></script>
		<script type="text/javascript" src="/js/sort.js"></script>
		<script type="text/javascript" src="/js/utils.js"></script>
		<?php if(isset($js)): ?>
			<?php foreach($js as $j): ?>
				<script type="text/javascript" src="/js/<?=$j?>"></script>
			<?php endforeach ?>
		<?php endif ?>
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
		<ul class="menu">
			<li><a href="/Retail">Handla</a></li>
			<li><a href="/Product/price_list">Prislista</a></li>
			<?php if(!empty($_SESSION['login'])): ?>
				<li class="dir">
					<a href="/Product/stock">Lager</a>
					<ul>
						<li class="dir">
							<a href="/Category">Kategorier</a>
							<ul>
								<?php foreach(Category::selection() as $category): ?>
									<li><a href="/Category/view/<?=$category->id?>"><?=$category?></a></li>
								<?php endforeach ?>
							</ul>
						</li>
						<li><a href="/Delivery/create">Ny leverans</a></li>
						<li><a href="/Delivery/index">Leveranser</a></li>
						<li><a href="/Product/take_stock">Inventering</a></li>
						<li><a href="/Product/log">Prislogg</a></li>
					</ul>
				</li>
				<li class="dir">
					<a href="/Account">Bokföring</a>
					<ul>
						<li class="dir">
							<a href="/Account">Konton</a>
							<ul>
								<?php foreach(Account::selection() as $account): ?>
									<li title="<?=$account->description?>">
										<a href="/Account/view/<?=$account->code_name?>">
											<?=$account?>
										</a>
									</li>
								<?php endforeach ?>
							</ul>
						</li>
						<li><a href="/DailyCount">Dagsavslut</a></li>
						<li><a href="/Transaction/create">Ny transaktion</a></li>
						<li><a href="/Transaction/index">Verifikationslista</a></li>
						<li><a href="/Retail/log">Transaktionslogg</a></li>
					</ul>
				</li>
				<li>
					<form action="/Session/logout" method="post">
						<div>
							<input type="submit" value="Logga ut" />
						</div>
					</form>
				</li>
			<?php else: ?>
				<li>
					<a href="/Session/login?kickback=<?=htmlspecialchars(kickback_url())?>">
						Logga in
					</a>
				</li>
			<?php endif ?>
		</ul>
		<?php foreach(Message::get_errors() as $error): ?>
			<p class="error"><?=$error?></p>
		<?php endforeach ?>
		<?php foreach(Message::get_warnings() as $warning): ?>
			<p class="warning"><?=$warning?></p>
		<?php endforeach ?>
		<?php foreach(Message::get_notices() as $notice): ?>
			<p class="notice"><?=$notice?></p>
		<?php endforeach ?>
		<?php $this->_partial($content) ?>
	</body>
</html>
