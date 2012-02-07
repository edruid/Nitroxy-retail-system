<?php
class ReportC extends Controller {
	protected $_default_site = 'result';

	public function __construct($site, $data = array()) {
		parent::__construct($site, $data);
		verify_login(kickback_url());
	}

	public function balance($params) {
		$this->_access_type('html');
		$this->year = array_shift($params);
		if(!$this->year || !preg_match("/\d{4}/", $this->year)) $this->year = date('Y');
		$this->accounts = Account::selection(array(
			'account_type' => 'balance',
			'@order'       => array('default_sign', 'name'),
		));
		$this->total = AccountTransactionContent::sum('amount', array(
			'Account.account_type'            => 'balance',
		));
		$this->change_total = AccountTransactionContent::sum('amount', array(
			'AccountTransaction.timestamp:>=' => "$this->year-01-01 00:00:00",
			'AccountTransaction.timestamp:<'  => ($this->year+1)."-01-01 00:00:00",
			'Account.account_type'            => 'balance',
		));
		self::_partial('Layout/html', $this);
	}

	public function result($params) {
		$this->_access_type('html');
		$this->year = array_shift($params);
		if(!$this->year || !preg_match("/\d{4}/", $this->year)) $this->year = date('Y');
		$this->accounts = Account::selection(array(
			'account_type' => 'result',
			'@order'       => array('default_sign', 'name'),
		));
		$this->total = AccountTransactionContent::sum('amount', array(
			'AccountTransaction.timestamp:>=' => "$this->year-01-01 00:00:00",
			'AccountTransaction.timestamp:<'  => ($this->year+1)."-01-01 00:00:00",
			'Account.account_type'            => 'result',
		));
		self::_partial('Layout/html', $this);
	}
}
?>
