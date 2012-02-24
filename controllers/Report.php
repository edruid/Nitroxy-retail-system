<?php
class ReportC extends Controller {
	protected $_default_site = 'result';

	public function __construct($site, $data = array()) {
		parent::__construct($site, $data);
		verify_login(kickback_url());
	}

	public function balance($params) {
		$this->_access_type('html');
		$year = date('Y');
		$this->start = ClientData::get('start');
		if(!$this->start || !strtotime($this->start)) $this->start = "$year-01-01";
		$this->end = ClientData::get('end');
		if(!$this->end || !strtotime($this->end)) $this->end = "$year-12-31";
		$this->accounts = Account::selection(array(
			'account_type' => 'balance',
			'@order'       => array('default_sign', 'name'),
		));
		$this->total = AccountTransactionContent::sum('amount', array(
			'Account.account_type'            => 'balance',
			'AccountTransaction.timestamp:<=' => $this->end.' 23:59:59',
		));
		$this->change_total = AccountTransactionContent::sum('amount', array(
			'AccountTransaction.timestamp:>=' => $this->start,
			'AccountTransaction.timestamp:<=' => $this->end.' 23:59:59',
			'Account.account_type'            => 'balance',
		));
		self::_partial('Layout/html', $this);
	}

	public function result($params) {
		$this->_access_type('html');
		$year = date('Y');
		$this->start = ClientData::get('start');
		if(!$this->start || !strtotime($this->start)) $this->start = "{$year}-01-01";
		$this->end = ClientData::get('end');
		if(!$this->end || !strtotime($this->end)) $this->end = "{$year}-12-31";
		$this->accounts = Account::selection(array(
			'account_type' => 'result',
			'@order'       => array('default_sign', 'name'),
		));
		$this->total = AccountTransactionContent::sum('amount', array(
			'AccountTransaction.timestamp:>=' => $this->start,
			'AccountTransaction.timestamp:<'  => $this->end." 23:59:59",
			'Account.account_type'            => 'result',
		));
		self::_partial('Layout/html', $this);
	}
}
?>
