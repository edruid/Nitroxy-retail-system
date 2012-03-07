<?php
$chars = "qwertyuiopasdfghjkzxcvbnmQWERTYUPASDFGHJKLZXCVBNM123456789!#¤%&/()=?+";
$length = 10;
if(isset($_SERVER['argv']) && isset($_SERVER['argv'][1])) {
	$data = $_SERVER['argv'][1];
	if(is_numeric($data) && $data > 0 && $data <= 999) {
		$length = $data;
	} else {
		$password = $data;
	}
}
if(!$password) {
	$password = "";
	for($i = 0; $i<$length; $i++) {
		$password .= $chars[rand() % strlen($chars)];
	}
}
$crypt = crypt($password, '$5$rounds=5000$'.rand());

echo "$password $crypt\n";
?>
