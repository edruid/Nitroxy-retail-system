<?php
class RetailC extends Controller {
	protected $_default_site = 'create';

	public function create($params) {
		$this->_access_type('html');
		$this->last_purchase = Transaction::last();
		$this->products = Product::selection(array(
			'category_id:!=' => 0,
			'@order'         => 'product_id',
		));
		$this->last_recieved = array_shift($params);
		$this->_register_global('js', array(
			'purchase.js',
			'suggest.js',
			'retail.js',
		));
		$this->_partial('Layout/html', $this);
	}
	
	public function log($params) {
		$this->_access_type('html');
		verify_login(kickback_url());
		$per_page = 20;
		$this->page = array_shift($params) ?: 0;
		$this->last_page = ceil(Transaction::count() / $per_page)-1;
		$this->transactions = Transaction::selection(array(
			'@order' => 'timestamp:desc',
			'@limit' => array($this->page * $per_page, $per_page)
		));
		$this->_partial('Layout/html', $this);
	}

	public function make($params) {
		$this->_access_type('script');
		global $db;

		$sum=0;
		$recieved=ClientData::post("recieved");
		$prices = ClientData::post("product_price");
		$counts = ClientData::post("product_count");

		if(isset($_SESSION['random']) && $_SESSION['random'] == ClientData::post('random')) {
			die('Form was already submitted');
		}

		$db->autoCommit(false);

		$transaction = new Transaction();
		$transaction->amount = 0;
		foreach(ClientData::post("product_id") as $i => $product_id) {
			$transaction->add_content($product_id, $prices[$i], $counts[$i]);
		}
		$diff = abs(round($transaction->amount) - $transaction->amount);
		if($diff != 0) {
			$content = new TransactionContent();
			$content->product_id = 0;
			$content->count = 1;
			$content->amount = $diff;
			$content->transaction_id = $transaction->id;
			$transaction->amount+=$diff;
			$transaction->commit();
		}

		if($transaction->amount > $recieved) {
			die("Det är för lite betalt. $transaction->amount < $recieved");
		}
		$transaction->commit();
		$db->commit();
		$_SESSION['random'] = ClientData::post('random');
		kick("/Retail/create/$recieved");
	}
}
