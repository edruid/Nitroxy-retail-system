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
			Message::add_error('Vänligen kontrollera värdet i kassan, det var inte numeriskt');
			kick('/DailyCount');
		}

		$_SESSION['last_request'] = ClientData::post('random');
		if(strtotime($daily_count->time) + 10 > time()) {
			Message::add_error('Det måste gå minst 10 sekunder mellan två kassaslut.');
			kick('/DailyCount');
		}
		$result = array();
		$stmt = $db->prepare_full("
			SELECT
				SUM(`transaction_contents`.`amount`) as amount,
				`account`.`code_name`
			FROM transaction_contents
			JOIN transactions USING (transaction_id)
			JOIN products USING (product_id)
			LEFT JOIN account USING (account_id)
			WHERE `transactions`.`timestamp` > ?
			GROUP BY account_id", $result, 's', $daily_count->time);
		$transactions = [
			'till'      => 0,
			'diff'      => 0,
			'stock'     => 0,
			'purchases' => 0,
		];
		$sales_amount = 0;
		while($stmt->fetch()) {
			$sales_amount += $result['amount'];
			$account = $result['code_name'] ?: 'sales';
			$transactions[$account] = $transactions[$account] ?: 0;
			$transactions[$account] += -$result['amount'];
		}
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
		$transactions['till']      += $till;
		$transactions['diff']      += $sales_amount - $till;
		$transactions['stock']     += -$stock_amount;
		$transactions['purchases'] += $stock_amount;

		$transaction->add_contents($transactions);

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
