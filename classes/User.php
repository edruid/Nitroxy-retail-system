<?php
class User {
	private $first_name;
	private $surname;
	private $username;
	private $accesses;

	public function __construct($data) {
		preg_match('/^firstname: "(.*)"$/m', $data, $match);
		$this->first_name = $match[1];
		preg_match('/^surname: "(.*)"$/m', $data, $match);
		$this->surname = $match[1];
		preg_match('/^username: "(.*)"$/m', $data, $match);
		$this->username = $match[1];
	}

	public function __toString() {
		return "{$this->first_name} '{$this->username}' {$this->surname}";
	}
}
?>
