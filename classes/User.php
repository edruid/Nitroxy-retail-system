<?php
class User extends BasicObject{
	public static function table_name() {
		return 'users';
	}

	public static function from_username($username) {
		return self::from_field('username', $username);
	}

	public static function login($username, $password) {
		$user = self::internal_login($username, $password);
		if($user) return $user;
		return self::external_login($username, $password);
	}

	private static function internal_login($username, $password) {
		$user = self::from_username($username);
		if(!$user || !$user->password) return null;
		$crypt = crypt($password, $user->password);
		if($crypt == '' || $crypt != $user->password) {
			throw new Exception("Fel användarnamn och/eller lösenord");
		}
		return $user;
	}

	private  static function external_login($username, $password) {
		$request = curl_init('https://bruse.proxxi.org/authenticate.php');
		curl_setopt($request, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($request, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($request, CURLOPT_POST, true);
		curl_setopt($request, CURLOPT_POSTFIELDS, array(
			'uname' => ClientData::post('username'),
			'pass' => ClientData::post('password'),
		));
		$result = curl_exec($request);

		if($result == 'not OK' || !preg_match('/accessess.*"kioskPrice"/s', $result)) {
			throw new Exception("Fel användarnamn och/eller lösenord");
		}

		preg_match('/^userid: "(.*)"$/m', $result, $match);
		$user_id = $match[1];
		preg_match('/^firstname: "(.*)"$/m', $result, $match);
		$first_name = $match[1];
		preg_match('/^surname: "(.*)"$/m', $result, $match);
		$surname = $match[1];
		preg_match('/^username: "(.*)"$/m', $result, $match);
		$username = $match[1];
		$user = User::from_id($user_id);
		if(!$user) {
			$user = new User();
			$user->user_id = $user_id;
		}
		$user->username = $username;
		$user->first_name = $first_name;
		$user->surname = $surname;
		$user->commit();
		return $user;
	}

	public function __toString() {
		return "{$this->first_name} '{$this->username}' {$this->surname}";
	}
}
?>
