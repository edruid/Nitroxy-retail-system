#!/usr/bin/php
<?php
	if(isset($argv[1]) && $argv[1]!='--help' && $argv[1]!='-h') {
		$ext = "sql";
		if(isset($argv[2])) {
			$ext = $argv[2];
			if($ext != "sql" && $ext != "php") {
				die("Unknown extention $ext\n");
			}
		}
		$filename=date("YmdHis")."_".$argv[1].".$ext";
		$f = fopen(dirname(__FILE__) . "/$filename",'w');
		if($ext == "php") {
			fwrite($f, "<?php
/*
 * Please be verbose. Use echo and end lines with \\n
 * Use migration_sql(\$query) to run queries (prints and runs the query)
 */
?>");
		}
		fclose($f);
		echo "Created migration ".dirname(__FILE__)."/$filename\n";
	} else {
		echo "Usage: ./create_migration name [*sql|php]\n";
	}
?>
