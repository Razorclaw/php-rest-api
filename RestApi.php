<?php
class RestApi {
	private $_hs;
	private $_format;

	function __construct($format = '') {
		$this->_hs = array();
		$this->_format = strtoupper($format);
	}

	function on($method, $callback) {
		$key = strtoupper($method);
		$this->_hs[$key] = $callback;
	}

	function handle() {
		$rm = strtoupper($_SERVER['REQUEST_METHOD']);
		if (array_key_exists($rm, $this->_hs)) {
			$data = self::parse_data($rm);
			$call = $this->_hs[$rm];
			$result = $call($data);
			echo $this->convert_result($result);
		}
	}

	static function parse_data($rm) {
		switch($rm) {
			case 'POST':
				return $_POST;
			case 'PUT':
				parse_str(file_get_contents("php://input"), $data);
				return $data;
			default:
				return array();
		}
	}

	function convert_result($result) {
		switch($this->_format) {
			case 'JSON':
				return self::to_json($result);
			case 'XML':
				return self::to_xml($result);
			default:
				return $result;
		}
	}

	static function to_json($result) {
		if ($json = json_encode($result)) {
			$error = json_last_error();

			// TODO: put error message
		}
		return $json;
	}

	static function to_xml($result) {
		// TODO
		return $result;
	}
}
?>
