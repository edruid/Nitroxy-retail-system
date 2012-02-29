<?php
class ProductC extends Controller {
	protected $_default_site = 'price_list';

	public function modify($params) {
		$this->_access_type('script');
		verify_login(kickback_url());
		$product = Product::from_id(ClientData::post('product'));
		if(!$product) {
			Message::add_error('Produkten finns inte');
			kick('/Product/index');
		}
		$fields = array(
			'name',
			'active',
			'price',
			'ean',
			'inventory_threshold',
			'category_id',
			'account_id',
		);
		foreach($fields as $field) {
			$value = ClientData::post($field);
			if($value === '') $value = null;
			$product->$field = $value;
		}
		$product->commit();
		Message::add_notice("Produkten har blivit uppdaterad");
		kick("/Product/view/{$product->id}");
	}

	public function edit($params) {
		$this->_access_type('html');
		verify_login(kickback_url());
		$this->product = Product::from_id(array_shift($params));
		if(!$this->product) {
			self::_partial('Static/not_found');
			return;
		}
		$this->categories = Category::selection();
		$this->accounts = Account::selection(array(
			'@order'       => 'name',
			'account_type' => 'result',
			'@or'          => array(
				'default_sign'        => 'kredit',
				'warn_on_non_default' => false,
			),
		));
		$this->packages = ProductPackage::selection(array(
			'package' => $this->product->id,
		));
		self::_partial('Layout/html', $this);
	}

	public function log($params) {
		$this->_access_type('html');
		verify_login(kickback_url());
		$search = array(
			'@order' => array('Product.name', 'changed_at'),
		);
		$product_id = array_shift($params);
		if(is_numeric($product_id)) {
			$search['product_id'] = $product_id;
		}
		$this->prices = ProductLog::selection($search);
		self::_partial('Layout/html', $this);
	}

	public function price_list($params) {
		$this->_access_type('html');
		$this->categories = Category::selection(array(
			'@order' => 'name',
			'category_id:!=' => 0,
		));
		self::_partial('Layout/html', $this);
	}

	public function stock_list($params) {
		$this->_access_type('html');
		verify_login(kickback_url());
		$this->products = array_shift($params);
	}

	public function view($params) {
		$this->_access_type('html');
		verify_login(kickback_url());
		$this->product = Product::from_id(array_shift($params));
		if(!$this->product) {
			self::_partial('Static/not_found');
			return;
		}
		$this->packages = ProductPackage::selection(array(
			'package' => $this->product->id
		));
		if($this->packages) {
			$this->total = $this->product->value;
		}
		$search = array('product_id' => $this->product->id);
		$this->total_sold = TransactionContent::sum('count', $search);
		$this->total_income = TransactionContent::sum('amount', $search);
		$this->total_delivered = DeliveryContent::sum('count', $search);
		$this->total_purchase_price = DeliveryContent::sum(array(
			'cost', '*', 'count'
		), $search);
		self::_partial('Layout/html', $this);
	}

	public function stock($params) {
		$this->_access_type('html');
		verify_login(kickback_url());
		$this->products = Product::selection(array(
			'@custom_order' => '`products`.`count` > 0 DESC',
			'@order' => array(
				'Category.name',
				'name',
			),
			'category_id:!=' => 0,
		));
		$this->stock_value = Product::sum(array('value', '*', 'count'));
		self::_partial('Layout/html', $this);
	}
	
	public function take_stock($params) {
		$this->_access_type('html');
		verify_login(kickback_url());
		$this->products = Product::selection(array(
			'category_id:!=' => 0,
			'@order' => 'product_id',
		));
		$this->_register_global('js', array(
			'purchase.js',
			'suggest.js',
			'take_stock.js',
		));
		self::_partial('Layout/html', $this);
	}

	public function taken_stock($params) {
		$this->_access_type('script');
		verify_login(kickback_url());
		global $db;
		$db->autocommit(false);
		$products = ClientData::post('product_id');
		$counts = ClientData::post('product_count');
		$money_diff = 0;
		$delivery = new Delivery();
		$delivery->description = "Inventering";
		$delivery->user_id = $_SESSION['login'];
		$delivery->commit();
		foreach($products as $i => $product_id) {
			// Create purchase
			$product = Product::from_id($product_id);
			$diff = $counts[$i] - $product->count;
			if($diff != 0) {
				$money_diff += $diff * $product->value;
				$product->count = $counts[$i];
				$product->commit();
				$contents = new DeliveryContent();
				$contents->cost = 0;
				$contents->delivery_id = $delivery->id;
				$contents->product_id = $product->id;
				$contents->count = $diff;
				$contents->commit();
			}
		}
		if($money_diff != 0) {
			$transaction = new AccountTransaction();
			$transaction->description="inventering: {$delivery->id}";
			$transaction->user_id = $_SESSION['login'];
			$transaction->commit();
			$transaction->add_contents(array(
				'stock_diff' => -$money_diff,
				'stock'      => $money_diff,
			));
		}
		$db->commit();
		kick("/Delivery/view/{$delivery->id}");
	}
}
