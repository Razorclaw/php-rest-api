<?php

class ErrorHandler
{
    function raise($code, $message)
    {
        header("HTTP/1.0 $code $message");
        exit();
    }
}

class RestApi
{
    private $_format;
    private $_method;
    private $_callback;

    function __construct($format = 'json')
    {
        $this->_format = strtolower($format);
        $this->_method = strtolower($_SERVER['REQUEST_METHOD']);
    }

    function bind($method, $callback)
    {
        if ($method == $this->_method)
        {
            $this->_callback = $callback;
        }
        return $this;
    }

    function handle()
    {
        $data   = $this->parse_data();
        $result = $this->_callback($data, new ErrorHandler());
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
        if ($json = json_encode($result)) {
            $error = json_last_error();

            // TODO: put error message
        }
        return $json;
    }

    static function to_xml($result)
    {
        // TODO
        return $result;
    }
}
