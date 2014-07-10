<?php
class DeliveryC extends Controller {
	protected $_default_site = 'index';

	public function __construct($site, $data = array()) {
		parent::__construct($site, $data);
		verify_login(kickback_url());
	}
	public function make($params) {
		$this->_access_type('script');
		global $db;
		$user = new User($_SESSION['login']);

		$multiplyer = ClientData::post('multiplyer');
		$single = ClientData::post('price_per');
		if($single != 'product_type' && $single != 'each_product') {
			$errors['Pris'] = 'Inte valt pris per enskild vara/varotyp';
		}

		$eans = ClientData::post('ean');
		$counts = ClientData::post('count');
		$sales_prices = ClientData::post('sales_price');
		$names = ClientData::post('name');
		$categories = ClientData::post('category');
		$purchase_prices = ClientData::post('purchase_price');
		$pant = ClientData::post('pant');
		for($i = 0; $i < count($purchase_prices); $i++) {
			if($pant[$i]) {
				$purchase_prices[$i] += $pant[$i];
			}
			$purchase_prices[$i] *= $multiplyer;
			if($single == 'each_product') {
				$purchase_prices[$i] *= $counts[$i];
			}
		}

		$at_least_1_item = false;
		$db->autoCommit(false);
		$delivery = new Delivery();
		$delivery->description = ClientData::post('description');
		$delivery->user_id = $_SESSION['login'];
		$delivery->commit();
		$stock_change_amount = 0;
		for($i=0; $i < count($eans); $i++) {
			if(empty($eans[$i])) {
				continue;
			}
			$at_least_1_item = true;
			try{
				$product = Product::from_ean($eans[$i]);
				if($product == null) {
					$product = new Product();
					$product->ean = $eans[$i];
				}

				$contents = new DeliveryContent();
				$product->value = ($product->value * $product->count + $purchase_prices[$i]) / ($product->count + $counts[$i]);
				$contents->cost = $purchase_prices[$i] / $counts[$i];
				$product->count += $counts[$i];
				$product->name = $names[$i];
				$product->price = $sales_prices[$i];
				$product->category_id = $categories[$i];
				$product->commit();
				$contents->delivery_id = $delivery->id;
				$contents->product_id = $product->id;
				$contents->count = $counts[$i];
				$contents->commit();
				$stock_change_amount += $purchase_prices[$i];
			} catch(Exception $e) {
				$errors[$i] = $e->getMessage();;
			}
		}
		$transaction = new AccountTransaction();
		$transaction->description = "Inköp id: {$delivery->id}";
		$transaction->user_id = $_SESSION['login'];
		$transaction->commit();
		$transactions = array(
			'stock'        => $stock_change_amount,
		);

		$balance_amount = 0;
		$balance_amounts = ClientData::post('amount');
		$balance_accounts = ClientData::post('from_account');
		for($i = 0; $i < count($balance_amounts); $i++) {
			$balance_amount += $balance_amounts[$i];
			$transactions[$balance_accounts[$i]] = -$balance_amounts[$i];
		}
		if(abs($balance_amount - $stock_change_amount) > 0.5) {
			$errors['kassa'] = 'Lagervärde av produkterna och penningåtgång stämmer inte överens. Du måste tala om vart pengarna kommer ifrån (det är ok att avrunda till närmaste krona)';
		}
		$transactions['rounding'] = $balance_amount - $stock_change_amount;

		try{
			$transaction->add_contents($transactions);
		} catch(Exception $e) {
			$errors['Konton'] = $e->getMessage();
		}

		if(empty($errors) && $at_least_1_item) {
			$db->commit();
			kick('/Delivery/view/'.$delivery->id);
		} else {
			$_SESSION['_POST'] = $_POST;
			foreach($errors as $index => $error) {
				Message::add_error("Rad $index: $error");
			}
			kick('Delivery/create');
		}
	}

	public function index($params) {
		$this->_access_type('html');
		$this->page = array_shift($params);
		$this->deliveries = Delivery::selection(array(
			'@order' => 'timestamp:desc',
			'@limit' => array($this->page * 50, 50),
		));
		$this->last_page = ceil(Delivery::count()/50)-1;
		self::_partial('Layout/html', $this);
	}

	public function view($params) {
		$this->_access_type('html');
		$this->delivery = Delivery::from_id(array_shift($params));
		self::_partial('Layout/html', $this);
	}

	public function create($params) {
		$this->_access_type('html');
		$this->categories = Category::selection(array(
			'category_id:!=' => 0,
		));
		$this->products = Product::selection(array(
			'category_id:!=' => 0,
		));
		$this->_register_global('js', array('delivery.js'));
		$this->old_values = ClientData::session('_POST');
		$values = array();
		if($this->old_values) {
			for($i=0; $i < count($this->old_values['ean'])-2; $i++) {
				$values[] = array(
					'ean'            => $this->old_values['ean'][$i],
					'name'           => $this->old_values['name'][$i],
					'sales_price'    => $this->old_values['sales_price'][$i],
					'category'       => $this->old_values['category'][$i],
					'count'          => $this->old_values['count'][$i],
					'purchase_price' => $this->old_values['purchase_price'][$i],
					'pant'           => $this->old_values['pant'][$i],
				);
			}
		}
		$values[] = array(
			'ean'            => '',
			'name'           => '',
			'sales_price'    => '',
			'category'       => '',
			'count'          => '',
			'purchase_price' => '',
			'pant'           => '',
		);
		$this->values = $values;
		unset($_SESSION['_POST']);
		self::_partial('Layout/html', $this);
	}
}
