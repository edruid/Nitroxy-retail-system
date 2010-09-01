<?php
// Cacha headers och innehåll så att vi kan ändra oss och skicka
// en Location-header även efter att vi börjat eka ut innehåll.
ob_start();

// Sätt den globala $repo_root till sökvägen till svn-repots root-mapp.
if(file_exists('classes'))
	$repo_root = '';
else if(file_exists('../classes'))
	$repo_root = '..';
else if(file_exists('../../classes'))
	$repo_root = '../..';

/**
 * Automatiskt anropad av php on-demand för att include:a filer med klassdefinitioner.
 * Antar att den globala variabeln $repo_root innehåller sökvägen till svn-repots root-mapp.
 */
function __autoload($class)
{
	global $repo_root;
	if(file_exists($repo_root.'/classes/'.$class.'.php'))
		require_once $repo_root.'/classes/'.$class.'.php';
}

/**
 * Klasser som behöver instantieras till en global.
 */
$db = new DatabaseConnectioni("nitroxy_retail", true);
?>
