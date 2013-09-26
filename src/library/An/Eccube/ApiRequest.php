<?php

class An_Eccube_ApiRequest {
    public $resource;
    
    public $method;
    
    /**
     * @var array
     */
    public $headers = array();

    /**
     * @var mixed
     */
    public $body;

    /**
     * @var array
     */
    public $query;
    
    public $api_key;
    
    public $authorized = false;
    
    public function __construct($resource, $method) {
        $this->resource = $resource;
        $this->method = $method;
    }
    
    public function authenticate($correct_api_key) {
        if ($this->api_key == '') {
            return false;
        }
        
        return $this->api_key == $correct_api_key;
    }
    
    public function authorize() {
        $this->authorized = true;
    }
    
    public static function createFromCurrentRequest() {
        $resource = is_string(@$_REQUEST['resource']) ? $_REQUEST['resource'] : '';
        
        $method = is_string(@$_REQUEST['method']) ? $_REQUEST['method'] : $_SERVER['REQUEST_METHOD'];
        $method = strtolower($method);

        if (function_exists('getallheaders')) {
            $headers = getallheaders();
        } else {
            $headers = self::getRawHeaders($_SERVER);
        }
        
        $stdin = fopen('php://input', 'r');
        $body = stream_get_contents($stdin);
        switch (@$headers['Content-Type']) {
            case 'application/json':
                $body = An_Eccube_Utils::decodeJson($body, true);
                break;
        
            case 'application/x-www-form-urlencoded';
            default:
                $body = $_POST;
                break;
        }

        if (isset($headers['X-AnCoupon-API-Key'])) {
            $api_key = $headers['X-AnCoupon-API-Key'];
        } elseif (is_string(@$_REQUEST['api_key'])) {
            $api_key = $_REQUEST['api_key'];
        } else {
            $api_key = null;
        }
        
        $request = new self($resource, $method);
        $request->headers = $headers;
        $request->body = $body;
        $request->query = $_GET;
        $request->api_key = $api_key;

        return $request;
    }
}
