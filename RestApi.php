<?php

class RestApi
{
    private $_format;
    private $_method;
    private $_callback;

    function __construct($format = 'json')
    {
        $this->_format = strtolower($format);
        $this->_method = strtolower($_SERVER['REQUEST_METHOD']);
        $this->_callback = NULL;
    }

    function bind($method, $callback)
    {
        if (strtolower($method) == $this->_method) {
            $this->_callback = $callback;
        }
        return $this;
    }

    function handle()
    {
        $data = $this->parse_data();
        if (($callback = $this->_callback)) {
            $result = $callback($data);
        } else {
            $result = array('1' => 'empty');
        }
        $this->display($result);
        exit();
    }
	
	static function contentType()
	{
		if (empty($_SERVER['CONTENT_TYPE'])) {
			return '';
		}
		$contentType = explode(';', $_SERVER['CONTENT_TYPE']);
		return $contentType[0];
	}

    function parse_data()
    {
        $input = file_get_contents("php://input");
        $contentType = self::contentType();
        switch ($contentType) {
			default:
            case 'application/x-www-form-urlencoded':
                parse_str($input, $data);
                return $data;
            case 'application/json':
            case 'application/x-javascript':
            case 'text/javascript':
            case 'text/x-javascript':
            case 'text/x-json':
                return json_decode($input, true);
            case 'text/xml':
            case 'application/xml':
            case 'multipart/form-data':
            case 'text/plain':
                trigger_error("RestApi: Unsupported content-type: $contentType");
                return array();
        }
    }

    function display($result)
    {
        switch ($this->_format) {
            case 'json':
                header('Content-type: application/json; charset=utf-8');
                echo self::to_json($result);
                break;
            case 'xml':
                header('Content-type: text/xml; charset=utf-8');
                echo self::to_xml($result);
                break;
            default:
                print_r($result);
        }
    }

    static function to_json($result)
    {
        if (!($json = json_encode($result))) {
            self::raise(500, "JSON Error: " + json_last_error());
        }
        return $json;
    }

    static function to_xml($result)
    {
        $xml = new SimpleXMLElement('<?xml version="1.0"?><root></root>');
        if (is_array($result)) {
            self::array_to_xml($result, $xml);
        } else {
            $xml->addChild("root", $result);
        }
        return $xml->asXML();
    }

    /**
     * @param $array
     * @param SimpleXMLElement $xml
     */
    static function array_to_xml($array, &$xml)
    {
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                if (is_int($key)) {
                    $node = $xml->addChild("element");
                    $node->addAttribute("id", $key);
                } else {
                    $node = $xml->addChild("$key");
                }
                self::array_to_xml($value, $node);
            } else {
                if (is_int($key)) {
                    $xml->addChild("element", $value)->addAttribute("id", $key);
                } else {
                    $xml->addChild("$key", "$value");
                }
            }
        }
    }

    static function raise($code, $message)
    {
        header("HTTP/1.0 $code $message");
        exit();
    }
}
