<?php

class SessionC extends Controller {

	protected $_default_site = 'login';
	public function login($params) {
		$this->_access_type('html');
		$this->post = ClientData::session('loggin_form');
		self::_partial('Layout/html', $this);
	}

	public function authenticate($params) {
		$this->_access_type('script');
		$_SESSION['loggin_form'] = $_POST;

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
			Message::add_error("Fel användarnamn och/eller lösenord");
			kick('Session/login');
		}

		unset($_SESSION['loggin_form']);
		$user = User::login($result);
		$_SESSION['login'] = $user->id;
		kick(ClientData::post('kickback'));
	}

	public function logout($params) {
		$this->_access_type('script');
		unset($_SESSION['login']);
		kick($_SERVER['HTTP_REFERER']);
	}
}
