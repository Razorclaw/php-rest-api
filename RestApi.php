<?php
class RestApi {
	private $_hs = array();

	function on($method, $callback) {
		$key = strtoupper($method);
		$this->_hs[$key] = $callback;
	}

	function handle() {
		$rm = strtoupper($_SERVER['REQUEST_METHOD']);
		if (array_key_exists($rm, $this->_hs)) {
			$data = self::parse_data($rm);
			$call = $this->_hs[$rm];
			$result = $call();
			echo self::convert_result($result);
		} else {
			echo $rm;
			print_r($this->_hs);
		}
	}

	static function parse_data($rm) {
		return array();
	}

	static function convert_result($result) {
		return $result;
	}
}
?>
