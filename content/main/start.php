<h2>Start page</h2>
<?
$dir =opendir("../content/main/");
?>
<ul>
	<? while($file = readdir($dir)): ?>
		<? if(substr($file, -4, 4) == '.php'): ?>
			<li><a href="/<?=substr($file, 0, -4)?>"><?=$file?></a></li>
		<? endif ?>
	<?endwhile?>
</ul>

	

<div style="clear: both"></div>
