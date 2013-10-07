<?php

abstract class An_Eccube_Api {
    /**
     * @var SC_Plugin_Base
     */
    public $plugin;
    
    /**
     * @var bool
     */
    protected $authenticationRequired;
    
    public function __construct(SC_Plugin_Base $plugin) {
        $this->plugin = $plugin;
        
        $this->initialize();
    }
    
    protected function initialize() {
    }
    
    public function invoke(An_Eccube_ApiRequest $request, An_Eccube_ApiResponse $response) {
        if ($this->authenticationRequired && !$request->authorized) {
            $response->setError('Unauthorized request.', 401, 'Unauthorized');
            return;
        }
        
        $this->execute($request, $response);
    }
    
    protected function execute(An_Eccube_ApiRequest $request, An_Eccube_ApiResponse $response) {
        switch ($request->method) {
            case 'get':
                $this->get($request, $response);
                break;
        
            case 'post':
                $this->post($request, $response);
                break;
        
            case 'put':
                $this->put($request, $response);
                break;
        
            case 'delete':
                $this->delete($request, $response);
                break;
        
            default:
                throw new BadMethodCallException();
        }
    }
    
    protected function get(An_Eccube_ApiRequest $request, An_Eccube_ApiResponse $response) {
        throw new BadMethodCallException();
    }
    
    protected function post(An_Eccube_ApiRequest $request, An_Eccube_ApiResponse $response) {
        throw new BadMethodCallException();
    }
    
    protected function put(An_Eccube_ApiRequest $request, An_Eccube_ApiResponse $response) {
        throw new BadMethodCallException();
    }
    
    protected function delete(An_Eccube_ApiRequest $request, An_Eccube_ApiResponse $response) {
        throw new BadMethodCallException();
    }
    
    public function isAuthenticationRequired() {
        return $this->authenticationRequired;
    }
    
    public static function getApiClass($resource) {
        $path = preg_replace('#[^A-Za-z0-9/_]#u', '', $resource);
        $path = implode('', array_map('ucfirst', (array)explode('_', $path)));
        $path = implode('_', array_map('ucfirst', (array)explode('/', $path)));
        $class = "An_Eccube_Api_{$path}Api";
        return $class;
    }
}
