<?php
require '../../includes.php';
$_SESSION['loggin_form'] = $_POST;
$request = curl_init('https://bruse.proxxi.org/authenticate.php');
curl_setopt($request, CURLOPT_RETURNTRANSFER, true);
curl_setopt($request, CURLOPT_POST, true);
curl_setopt($request, CURLOPT_POSTFIELDS, array(
	'uname' => ClientData::post('username'),
	'pass' => ClientData::post('password'),
));
$result = curl_exec($request);
if($result == 'not OK') {
	Message::add_error("Fel användarnamn och/eller lösenord");
	kick('login');
}
if(!preg_match('/accessess.*"kioskPrice"/s', $result)) {
	Message::add_error("Fel användarnamn och/eller lösenord");
	kick('login');
}
$_SESSION['loggin_form'] = null;
$_SESSION['login'] = $result;
header("Location: ".ClientData::post('kickback'));
?>
