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
        $resource = is_string(@$_GET['resource']) ? $_GET['resource'] : '';
        
        if (is_string(@$_GET['method'])) {
            $method = $_GET['method'];
        } else {
            $method = $_SERVER['REQUEST_METHOD'];
        }
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
                $body = json_decode($body, true);
                break;
        
            case 'application/x-www-form-urlencoded';
            $body = $_POST;
            break;
        
            default:
                $body = json_decode($body, true);
                $user = $body;
                if (json_last_error() != JSON_ERROR_NONE) {
                    $body = $_POST;
                }
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
    
    public static function getRawHeaders(array $server) {
        $headers = array();
        
        foreach ($server as $name => $value) {
            $tokens = (array)explode('_', strtolower($name));
            if (array_shift($tokens) == 'http') {
                $name = implode('-', array_map('ucfirst', $tokens));
                $headers[$name] = $value;;
            }
        }
        
        return $headers;
    }
}
