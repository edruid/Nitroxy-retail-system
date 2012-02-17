<?="<?xml version=\"1.0\" encoding=\"utf-8\"?>"?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
	<head>
		<title><?=$title?></title>
		<link rel="icon" type="image/png" href="http://retail.kari/favicon" />
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
		<?php $this->_partial('Menu') ?>
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
