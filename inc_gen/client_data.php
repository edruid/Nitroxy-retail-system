<?
function post_get($string) {
	if(isset($_POST[$string])) {
		return $_POST[$string];
	}
	return false;
}
function request_get($string) {
	if(isset($_REQUEST[$string])) {
		return $_REQUEST[$string];
	}
	return false;
}

function session_get($string) {
	if(isset($_SESSION[$string])) {
		return $_SESSION[$string];
	}
	return false;
}

function session_set($string,$value) {
	$_SESSION[$string]=$value;
}
?>
