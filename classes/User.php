<?php
class User extends BasicObject{
	public static function table_name() {
		return 'users';
	}

	public static function login($data) {
		preg_match('/^userid: "(.*)"$/m', $data, $match);
		$user_id = $match[1];
		preg_match('/^firstname: "(.*)"$/m', $data, $match);
		$first_name = $match[1];
		preg_match('/^surname: "(.*)"$/m', $data, $match);
		$surname = $match[1];
		preg_match('/^username: "(.*)"$/m', $data, $match);
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
