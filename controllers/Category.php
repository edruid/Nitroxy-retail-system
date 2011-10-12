<?php
class CategoryC extends Controller {
	protected $_default_site = 'index';

	public function __construct($site, $data = array()) {
		parent::__construct($site, $data);
		verify_login(kickback_url());
	}
	public function make($params) {
		$this->_access_type('script');
		$category = new Category();
		$category->name = ClientData::post('name');
		$category->commit();
		kick('/Category');
	}

	public function modify($params) {
		$this->_access_type('script');
		$category = Category::from_id(array_shift($params));
		if($category == null) {
			Message::add_error("Kategorin finns inte");
			kick('/Category');
		}
		$category->name = ClientData::post('name');
		$category->commit();
		kick('/Category');
	}

	public function index($params) {
		$this->_access_type('html');
		$this->categories = Category::selection(array('@order' => 'name'));
		self::_partial('Layout/html', $this);
	}

	public function view($params) {
		$this->_access_type('html');
		$this->category = Category::from_id(array_shift($params));
		if($this->category == null) {
			self::_partial('Static/not_found');
		} else {
			$this->products = $this->category->Product(array('@order' => 'name'));
			self::_partial('Layout/html', $this);
		}
	}

	public function edit($params) {
		$this->_access_type('html');
		$this->category = Category::from_id(array_shift($params));
		if($this->category == null) {
			self::_partial('Static/not_found');
		} else {
			self::_partial('Layout/html', $this);
		}
	}
}
