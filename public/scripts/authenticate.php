<?php
require '../../includes.php';
$request = curl_init('https://bruse.proxxi.org/authenticate.php');
curl_setopt($request, CURLOPT_RETURNTRANSFER, true);
curl_setopt($request, CURLOPT_POST, true);
curl_setopt($request, CURLOPT_POSTFIELDS, array(
	'uname' => post_get('username'),
	'pass' => post_get('password'),
));
$result = curl_exec($request);
if($result == 'not OK') {
	echo "inloggningen misslyckades";
	die();
}
if(!preg_match('/accessess.*"kioskPrice"/s', $result)) {
	echo "inloggningen misslyckades";
	die();
}
$_SESSION['login'] = $result;
header("Location: ".post_get('kickback'));
?>
