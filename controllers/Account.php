<?php
class AccountC extends Controller {
	protected $_default_site = 'index';

	public function __construct($site, $data = array()) {
		parent::__construct($site, $data);
		verify_login(kickback_url());
	}

	public function make($params) {
		$this->_access_type('script');
		die('Not implemented');
	}

	public function view($params) {
		$this->_access_type('html');
		$this->account = Account::from_code_name(array_shift($params));
		if($this->account == null) {
			Message::add_error('Kontot finns inte');
			kick('/Transaction');
		}
		$this->page = array_shift($params);
		$this->balance = $this->account->balance;
		if($this->page == null) $page = 0;
		$this->contents = AccountTransactionContent::selection(array(
			'account_id' => $this->account->id,
			'@order' => 'AccountTransaction.timestamp:desc',
			'@limit' => array($this->page*30, $this->page*30+30),
		));
		self::_partial('Layout/html', $this);
	}

	public function index($params) {
		$this->_access_type('html');
		$this->accounts = Account::selection(array(
			'@order' => array('account_type', 'name'),
		));
		self::_partial('Layout/html', $this);
	}
}
