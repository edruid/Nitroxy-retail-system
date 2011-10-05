<?php
class LayoutC extends Controller {
	public function html($params) {
		$this->_access_type('html');
		global $application;
		if(!$this->title) {
			$this->_register_global('title', $application['name']);
		} else {
			$this->_register_global('title', "{$application['name']} - {$this->title}");
		}
		$this->content = $params->_path;
	}
}
?>
