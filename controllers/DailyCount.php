<?php
class DailyCountC extends Controller {
	protected $_default_site = 'create';

	public function __construct($site, $data = array()) {
		parent::__construct($site, $data);
		verify_login(kickback_url());
	}

	public function create($params) {
		$this->_access_type('html');
		$this->daily_count = DailyCount::last();
		$this->old_till = TransactionContent::sum('amount', array(
			'Transaction.timestamp:>' => $this->daily_count->time,
			'Transaction.timestamp:<=' => date('Y-m-d H:i:s'),
		));
		$this->old_till += $this->daily_count->amount;
		$this->account_change = AccountTransactionContent::sum('amount', array(
			'Account.code_name' => 'till',
			'AccountTransaction.timestamp:>' => $this->daily_count->time,
			'AccountTransaction.timestamp:<=' => date('Y-m-d H:i:s'),
		));
		$this->old_till += $this->account_change;
		$this->sales = Transaction::sum('amount', array(
			'timestamp:>' => $this->daily_count->time
		));
		$this->stock_amount = TransactionContent::sum('stock_usage', array(
			'Transaction.timestamp:>' => $this->daily_count->time,
			'Transaction.timestamp:<=' => date('Y-m-d H:i:s'),
		));
		self::_partial('Layout/html', $this);
	}

	public function make($params) {
		$this->_access_type('script');
		global $db;
		$time = date('Y-m-d H:i:s');
		$daily_count = DailyCount::last();

		if(isset($_SESSION['last_request']) &&
				$_SESSION['last_request'] == ClientData::post('random')) {
			Message::add_error('This request has already been sent');
			kick("/Transaction/view/{$daily_count->account_transaction_id}");
		}
		if(!is_numeric(ClientData::post('till'))) {
			Message::error('Vänligen kontrollera värdet i kassan, det var inte numeriskt');
			kick('/DailyCount');
		}

		$_SESSION['last_request'] = ClientData::post('random');
		if(strtotime($daily_count->time) + 120 > time()) {
			Message::error('Det måste gå minst 2 minuter mellan två kassaslut.');
			kick('/DailyCount');
		}
		$sales_amount = Transaction::sum('amount', array(
			'timestamp:>' => $daily_count->time,
			'timestamp:<=' => $time,
		));
		if($sales_amount == null) $sales_amount = 0;

		$old_till = AccountTransactionContent::sum('amount', array(
			'Account.code_name' => 'till',
			'AccountTransaction.timestamp:>' => $daily_count->time,
			'AccountTransaction.timestamp:<=' => $time,
		));
		$old_till += $daily_count->amount;
		$till = ClientData::post('till') - $old_till;

		$stock_amount = TransactionContent::sum('stock_usage', array(
			'Transaction.timestamp:>' => $daily_count->time,
			'Transaction.timestamp:<=' => $time,
		));
		$stock_amount = $stock_amount ?: 0;

		$db->autocommit(false);

		$transaction = new AccountTransaction();
		$transaction->description = 'Dagsavslut';
		$transaction->user_id = $_SESSION['login'];
		$transaction->timestamp = $time;
		$transaction->commit();
		$transaction->add_contents(array(
			'sales'        => -$sales_amount,
			'till'         => $till,
			'diff'         => $sales_amount - $till,
			'stock'        => -$stock_amount,
			'stock_change' => $stock_amount,
		));

		$daily_count = new DailyCount();
		$daily_count->time = $time;
		$daily_count->amount = ClientData::post('till');
		$daily_count->account_transaction_id = $transaction->id;
		$daily_count->user_id = $_SESSION['login'];
		$daily_count->commit();

		$db->commit();
		kick("/Transaction/view/{$transaction->id}");
	}
}
