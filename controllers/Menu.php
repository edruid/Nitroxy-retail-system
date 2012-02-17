<?php
class MenuC extends Controller {
	protected $_default_site = 'index';

	public function index($params) {
		$this->_access_type('html');
		$this->loged_in = !empty($_SESSION['login']);
		$this->categories = Category::selection(array(
			'@order' => 'name',
		));
		$this->accounts = Account::selection(array(
			'@order' => array('account_type:desc', 'default_sign', 'name'),
		));
	}
}
?>
