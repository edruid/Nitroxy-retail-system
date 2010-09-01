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
		$arr=session_get("messages_".strtolower($type));
		$arr[]=$message;
		session_set("messages_".strtolower($type),$arr);
	}

	public static function get_errors() {
		return self::get('error');
	}

	private static function get($type) {
		$ret = session_get("messages_".strtolower($type));
		unset($_SESSION["messages_".strtolower($type)]);
		if($ret === false) {
			$ret = array();
		}
		return $ret;
	}

	public static function clear($type=null) {
		if(is_null($type)) {
			session_set("messages_error",null);
			session_set("messages_warning",null);
			session_set("messages_notice",null);
		} elseif(strtolower($type)=="error") {
			session_set("messages_error",null);
		} elseif(strtolower($type)=="warning") {
			session_set("messages_warning",null);
		} elseif(strtolower($type)=="notice") {
			session_set("messages_notice",null);
		}
	}
}
?>
