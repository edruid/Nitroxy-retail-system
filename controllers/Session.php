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
		try {
			$user = User::login(ClientData::post('username'),
					ClientData::post('password'));
			unset($_SESSION['loggin_form']);
			$_SESSION['login'] = $user->id;
			kick(ClientData::post('kickback'));
		} catch(Exception $e) {
			Message::add_error($e->getMessage());
			kick('/Session/login');
		}
	}

	public function logout($params) {
		$this->_access_type('script');
		unset($_SESSION['login']);
		kick($_SERVER['HTTP_REFERER']);
	}
}
