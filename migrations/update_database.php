#!/usr/bin/php
<?
include dirname(__FILE__) . "/../includes.php";
include "$repo_root/libs/color_terminal.php";

$ignored_files = array("update_database.php", "create_migration.php", "README.txt");

if(isset($argv[1])) {
	$username = $argv[1];
	echo "Password: ";
	system('stty -echo');
	$password = trim(fgets(STDIN));
	system('stty echo');
	echo "\n";
} else {
	$username = null;
	$password = null;
}

unset($db);
try {
	$db = new DatabaseConnectioni($webpage_stage, true, $username, $password);
} catch(Exception $e) {
	die("Misslyckades med att ansluta till databasen. Felmeddelande: ".$e->getMessage());
}

create_migration_table_if_not_exists();


$db->autocommit(FALSE);
foreach(migration_list() as $version => $file) {
	if(!migration_applied($version)) {
		run_migration($version,$file);
	}
}
ColorTerminal::set("green");
echo "All migrations completed\n";
ColorTerminal::set("normal");

$db->close();

/**
 * Creates a hash :migration_version => file_name
 */
function migration_list() {
	global $repo_root, $ignored_files;
	$dir = opendir($repo_root."/migrations");
	$files = array();
	while($f = readdir()) {
		if($f[0] != "." && ! in_array($f,$ignored_files)) {
			$files[get_version($f)] = $f;
		}
	}
	ksort($files);
	closedir($dir);
	return $files;
}

function get_version($file) {
	return $file;
}

/**
 * Runs the migration
 */
function run_migration($version, $filename) {
	global $db, $repo_root;
	try {
		$ext = end(explode('.', $filename));
		ColorTerminal::set("blue");
		echo "============= BEGIN $filename =============\n";
		ColorTerminal::set("normal");
		if(filesize("$repo_root/migrations/$filename") == 0) {
			ColorTerminal::set("red");
			echo "$filename is empty. Migrations aborted\n";
			ColorTerminal::set("normal");
			exit;
		}
		switch($ext) {
			case "php":
				echo "Parser: PHP\n";
				{
					require "$repo_root/migrations/$filename";
				}
				break;
			case "sql":
				echo "Parser: MySQL\n";
				$queries = preg_split("/;[[:space:]]*\n/",file_contents("$repo_root/migrations/$filename"));
				foreach($queries as $q) {
					$q = trim($q);
					if($q != "") {
						echo "$q\n";
						$ar=run_sql($q);
						echo "Affected rows: $ar \n";
					}
				}
				break;
			default:
				ColorTerminal::set("red");
				echo "Unknown extention: $ext\n";
				echo "All following migrations aborted\n";
				$db->rollback();
				ColorTerminal::set("normal");
				exit;
		}
		//Add migration to schema_migrations:
		run_sql("INSERT INTO `schema_migrations` (`version`) VALUES ('$version');");

		$db->commit();
		ColorTerminal::set("green");
		echo "Migration complete\n";
		echo "============= END $filename =============\n";
		ColorTerminal::set("normal");

	} catch (Exception $e) {
		ColorTerminal::set("red");
		if($e instanceof QueryException) {
			echo "Error in migration $filename:\n".$e->getMessage()."\n";
		} else {
			echo "Error in migration $filename:\n".$e."\n";
		}
		ColorTerminal::set("lightred");
		echo "============= ROLLBACK  =============\n";
		$db->rollback();
		echo "All following migrations aborted\n";
		ColorTerminal::set("normal");
		exit;
	}
}

/** 
 * Returns true if the specified version is applied
 */
function migration_applied($version) {
	global $db;
	$stmt = $db->prepare_full("SELECT 1 FROM `schema_migrations` WHERE `version` = '$version'");
	$res = $stmt->fetch();
	$stmt->close();
	return $res;
}

/**
 * Helper funcition for migration scripts
 */
function migration_sql($query) {
	echo $query."\n";
	echo "Affected rows: ".run_sql($query)."\n";
}

/**
 * Runs sql query or throw exception
 */
function run_sql($query) {
	global $db;
	$status = $db->query($query);
	if($status == false) {
		throw new QueryException("Query failed: ".$db->error);
	}
	$affected_rows = $db->affected_rows;
	return $affected_rows;
}

function file_contents($filename) {
	$handle = fopen($filename, "r");
	$contents = fread($handle, filesize($filename));
	fclose($handle);
	return $contents;
}

/**
 * Creates the migration table if is does not exist
 */
function create_migration_table_if_not_exists() {
	global $db;
	run_sql("CREATE TABLE IF NOT EXISTS `schema_migrations` (
		`version` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
		UNIQUE KEY `unique_schema_migrations` (`version`)
		)");
}

class QueryException extends Exception{
	public function __construct($message) {
		$this->message = $message;
	}
}
?>
