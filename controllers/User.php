<?php
class UserC extends Controller {
	public function index($params) {
		self::_access_type('html');
		$this->users = User::selection(array(
			'@order' => 'username',
		));
		self::_partial('Layout/html', $this);
	}

	public function view($params) {
		self::_access_type('html');
		$this->user = User::from_username(array_shift($params));
		self::_partial('Layout/html', $this);
	}

	public function edit($params) {
		self::_access_type('html');
		$this->user = User::from_username(array_shift($params));
		self::_partial('Layout/html', $this);
	}

	public function create($params) {
		self::_access_type('html');
		self::_partial('Layout/html', $this);
	}

	public function modify($params) {
		self::_access_type('script');
		$this->user = User::from_username(array_shift($params));
	}

	public function make($params) {
		self::_access_type('script');
	}
}
