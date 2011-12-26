<?php
$chars = "qwertyuiopasdfghjkzxcvbnmQWERTYUPASDFGHJKLZXCVBNM123456789!#¤%&/()=?+";
$length = 10;
if(isset($_SERVER['argv']) && isset($_SERVER['argv'][1])) {
	$length = $_SERVER['argv'][1];
}
$password = "";
for($i = 0; $i<$length; $i++) {
	$password .= $chars[rand() % strlen($chars)];
}
$crypt = crypt($password, '$5$rounds=5000$'.rand());

echo "$password $crypt\n";
?>
