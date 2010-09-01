<?php
$d=dir(dirname(__FILE__));
while($entry = $d->read()) {
	$file = substr($entry, 0, -4);
	echo $file."<br/>\n";
}
