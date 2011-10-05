<?php
class RetailC extends Controller {
	protected $_default_site = 'create';

	public function make($params) {
		$this->_access_type('script');
		global $db;
	}

	public function create($params) {
		$this->_access_type('html');
		$this->last_purchase = Transaction::last();
		$this->products = Product::selection(array(
			'category_id:!=' => 0,
			'@order'         => 'product_id',
		));
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
			'@limit' => array(ClientData::request('page')*10, 10)
		));
		$this->_partial('Layout/html', $this);
	}
}
