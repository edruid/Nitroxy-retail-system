<?php
class Controller {
	protected $_default_site = null;
	protected $_private_data = array();
	protected $_path = null;
	protected $_site = null;

	public static function _access_type($type) {
		if(defined('HTML_ACCESS')) {
			if(HTML_ACCESS != ($type == 'html')) {
				throw new Exception("Trying to change HTML_ACCESS value.");
			}
			return;
		}
		if($type == 'html') {
			define('HTML_ACCESS', true);
		} else {
			define('HTML_ACCESS', false);
		}

		if($type == 'script') {
			if(!array_key_exists('HTTP_REFERER', $_SERVER) ||
					!in_array(strpos($_SERVER['HTTP_REFERER'], "://{$_SERVER['HTTP_HOST']}"), array(4,5))) {
				throw new Exception("Referer missmatch. Suspected CSRF attack. Referer is: {$_SERVER['HTTP_REFERER']} host is: {$_SERVER['HTTP_HOST']}");
			}
			$random = ClientData::post('random');
			if($random && ClientData::session('random') == $random) {
				throw new DoubleSubmitException('This form has already been posted.');
			} elseif($random) {
				ClientData::session_set('random', $random);
			}
		}
	}

	public function __construct($site, $data = array()) {
		if(!$site) {
			$site = $this->_default_site;
			if(!$site) {
				throw new Exception("No default site to visit");
			}
		}
		if(substr($site, 0, 1) == '_') {
			throw new Exception("Trying to access disalowed site");
		}
		if(!method_exists($this, $site)) {
			throw new Exception("No such site: \"".get_called_class()."/$site\"");
		}
		$this->_site = $site;
		$this->_path = substr(get_called_class(), 0, -1).'/'.$site;
	}
	
	private static function _reg($key) {
		static $data = array();
		$params = func_get_args();
		if(count($params) == 3) {
			return $data;
		}
		if(count($params) == 2) {
			$data[$key] = $params[1];
		} else {
			if(array_key_exists($key, $data)) {
				return $data[$key];
			} else {
				return null;
			}
		}
	}

	public function __set($key, $value) {
		if($key[0] == '_') {
			throw new Exception("Illigal setting of parameter [$key]");
		}
		$this->_private_data[$key] = $value;
	}

	public function __get($key) {
		if($key[0] == '_') {
			throw new Exception("Illigal getting of parameter [$key]");
		}
		if(array_key_exists($key, $this->_private_data)) {
			return $this->_private_data[$key];
		}
		return $this->_reg($key);
	}

	protected function _register_global($key, $value) {
		self::_reg($key, $value);
	}

	protected function _get($key) {
		if(array_key_exists($key, $this->_private_data)) {
			return $this->_private_data[$key];
		}
		return self::_reg($key);
	}

	private static function _stack($path, $data, $action) {
		static $paths = array();
		if(!array_key_exists($path, $paths) || $action == 'add') {
			$controller = self::_make_controller($path, $data);
			$path = $controller->_path;
			$paths[$path][] = $controller;
			$site = $controller->_site;
			$controller->$site($data);
		}
		if($action == 'display') {
			$controller = array_shift($paths[$path]);
			if(count($paths[$path]) == 0) unset($paths[$path]);
			return $controller;
		}
	}

	private static function _make_controller($path, $data) {
		$tokens = explode('/', $path);
		$controller = array_shift($tokens).'C';
		return new $controller(array_shift($tokens), $data);
	}

	public static function _partial($path, $data = array()) {
		self::_stack($path, $data, 'display')->_display();
	}

	public static function _declare($path, $data = array()) {
		self::_stack($path, $data, 'add');
	}

	public function _display() {
		foreach(self::_reg(0,0,0) as $key => $value) {
			$$key = $value;
		}
		foreach($this->_private_data as $key => $value) {
			$$key = $value;
		}
		require "../views/{$this->_path}.php";
	}
}
class DoubleSubmitException extends Exception {}
?>
