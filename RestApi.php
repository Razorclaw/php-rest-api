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

    static function raise($code, $message)
    {
        header("HTTP/1.0 $code $message");
        exit();
    }

    function bind($method, $callback)
    {
        if ($method == $this->_method) {
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

    function parse_data()
    {
        switch ($this->_method) {
            case 'post':
                return $_POST;
            case 'put':
                parse_str(file_get_contents("php://input"), $data);
                return $data;
            default:
                return array();
        }
    }

    function display($result)
    {
        switch ($this->_format) {
            case 'json':
                header('Content-type: application/json');
                echo self::to_json($result);
                break;
            case 'xml':
                header('Content-type: text/xml');
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

    static function array_to_xml($array, &$xml)
    {
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $node = $xml->addChild("$key");
                self::array_to_xml($value, $node);
            } else {
                $xml->addChild("$key", "$value");
            }
        }
    }
}
