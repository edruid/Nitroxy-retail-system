<?php
function number($float) {
	return number_format($float, 2, '.', '');
}

function verify_login($url = null){
	if(empty($_SESSION['login'])) {
		Message::add_error('Du har blivit utloggad, logga in igen för att återgå.');
		$_SESSION['_POST'] = $_POST;
		kick('login?kickback='.htmlspecialchars($url));
	}
}
?>
