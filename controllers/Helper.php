<?php
class HelperC extends Controller {
	public function pager($params) {
		$this->_access_type('html');
		$this->url_format = array_shift($params);
		$this->page       = array_shift($params);
		$this->last_page  = array_shift($params);
	}
}
