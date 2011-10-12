<?php
class TransactionC extends Controller {
	protected $_default_site = 'index';

	public function __construct($site, $data = array()) {
		parent::__construct($site, $data);
		verify_login(kickback_url());
	}

	public function make($params) {
		$this->_access_type('script');
		global $db;
		$db->autocommit(false);
		try{
			$transaction = new AccountTransaction();
			$transaction->description = ClientData::post('description');
			$transaction->user_id = $_SESSION['login'];
			$transaction->commit();
			$transaction->add_contents(array(
				ClientData::post('from_account')  => -ClientData::post('amount'),
				ClientData::post('to_account') => ClientData::post('amount'),
			));
			$db->commit();
		} catch(Exception $e) {
			die("Nånting gick fel:<pre>{$e->getMessage()}</pre>");
		}
		Message::add_notice("Transaktionen är nu bokförd");
		kick("/Transaction/view/{$transaction->id}");
	}

	public function create($params) {
		$this->_access_type('html');
		$this->accounts = Account::selection(array('@order' => 'name'));
		$this->_partial('Layout/html', $this);
	}

	public function view($params) {
		$this->_access_type('html');
		$this->transaction = AccountTransaction::from_id(array_shift($params));
		$this->contents = $this->transaction->AccountTransactionContent;
		$this->_partial('Layout/html', $this);
	}

	public function index($params) {
		$this->_access_type('html');
		$this->page = array_shift($params) ?: 0;
		$this->transactions = AccountTransaction::selection(array(
			'@order' => 'timestamp:desc',
			'@limit' => array($this->page*50, 50),
		));
		$this->_partial('Layout/html', $this);
	}
}
