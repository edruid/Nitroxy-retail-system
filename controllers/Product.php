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
			'value',
			'ean',
			'category_id',
			'inventory_threshold',
		);
		foreach($fields as $field) {
			$product->$field = ClientData::post($field);
		}
		$product->commit();
		Message::add_error("Produkten har blivit uppdaterad");
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
		$this->packages = ProductPackage::selection(array(
			'package' => $this->product->id,
		));
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
		
}
