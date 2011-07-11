<h2>Start page</h2>
<?php
$dir = opendir("../content/main/");
$files = array();
while($file = readdir($dir)) {
	if(substr($file, -4, 4) == '.php') {
		$files[] = $file;
	}
}
sort($files);
?>
<ul>
	<? foreach($files as $file): ?>
		<li><a href="/<?=substr($file, 0, -4)?>"><?=$file?></a></li>
	<?endforeach?>
</ul>
