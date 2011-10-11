<?php
class Message {
	public static function add_error($message) {
		self::add($message,"error");
	}
	public static function add_warning($message) {
		self::add($message,"warning");
	}
	public static function add_notice($message) {
		self::add($message,"notice");
	}

	private static function add($message, $type) {
		$arr=ClientData::session("messages_".strtolower($type));
		$arr[]=$message;
		ClientData::session_set("messages_".strtolower($type),$arr);
	}

	public static function get_errors() {
		return self::get('error');
	}

	public static function get_warnings() {
		return self::get('warning');
	}

	public static function get_notices() {
		return self::get('notice');
	}

	private static function get($type) {
		$ret = ClientData::session("messages_".strtolower($type));
		unset($_SESSION["messages_".strtolower($type)]);
		if($ret === false) {
			$ret = array();
		}
		return $ret;
	}

	public static function clear($type=null) {
		if(is_null($type)) {
			ClientData::session_set("messages_error",null);
			ClientData::session_set("messages_warning",null);
			ClientData::session_set("messages_notice",null);
		} elseif(strtolower($type)=="error") {
			ClientData::session_set("messages_error",null);
		} elseif(strtolower($type)=="warning") {
			ClientData::session_set("messages_warning",null);
		} elseif(strtolower($type)=="notice") {
			ClientData::session_set("messages_notice",null);
		}
	}
}
?>
