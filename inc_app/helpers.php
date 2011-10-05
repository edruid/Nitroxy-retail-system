<?php
function number($float) {
	if($float === null) {
		return '-';
	}
	return number_format($float, 2, '.', '');
}

function verify_login($url = null){
	if(empty($_SESSION['login'])) {
		Message::add_error('Du har blivit utloggad, logga in igen för att återgå.');
		if($_SERVER['HTTP_METHOD'] == 'POST') {
			$_SESSION['_POST'] = $_POST;
		}
		kick('Session/login?kickback='.htmlspecialchars($url));
	}
}

function get_rand() {
	$random = rand();
	if(isset($_SESSION['random']) && $random == $_SESSION['random']) {
		$random++;
	}
	return $random;
}

function mark($bool) {
	if($bool) {
		return 'marked';
	} else {
		return '';
	}
}
?>
