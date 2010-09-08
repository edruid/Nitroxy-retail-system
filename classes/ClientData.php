<?
class ClientData {
	public static function clean($data) {
		if(HTML_ACCESS) {
			if(is_array($data)) {
				foreach($data as $key => $value) {
					$data[$key] = self::clean($value);
				}
				return $data;
			} else {
				return htmlspecialchars($data, ENT_QUOTES, 'utf-8');
			}
		} else {
			return $data;
		}
	}

	public static function post($string) {
		if(isset($_POST[$string])) {
			return self::clean($_POST[$string]);
		}
		return false;
	}
	public static function request($string) {
		if(isset($_REQUEST[$string])) {
			return self::clean($_REQUEST[$string]);
		}
		return false;
	}

	public static function session($string) {
		if(isset($_SESSION[$string])) {
			return self::clean($_SESSION[$string]);
		}
		return false;
	}

	public static function session_set($string,$value) {
		$_SESSION[$string]=$value;
	}
}
?>
