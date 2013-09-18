<?php

class An_Eccube_PageContext {
    /**
     * @var string
     */
    public $id;
    
    /**
     * @var array
     */
    public $session = array();
    
    /**
     * @var int
     */
    public $expiration;
    
    /**
     * @var bool
     */
    public $disposed = false;
    
    /**
     * @var bool
     */
    public $prepared = false;
    
    public function __construct($id) {
        $this->id = $id;
        $this->expiration = self::getDefaultExpiration() + time();
    }
    
    public static function & getStorage() {
        if (!isset($_SESSION['plg_AnCoupon']['page_context'])) {
            $_SESSION['plg_AnCoupon']['page_context'] = array();
        }
        
        return $_SESSION['plg_AnCoupon']['page_context'];
    }
    
    public static function runGc() {
        $now = time();
        $storage =& self::getStorage();
        foreach ($storage as $id => $context) {
            if ($context->disposed || $context->expiration < $now) {
                unset($storage[$id]);
            }
        }
    }

    public static function getDefaultExpiration() {
        return 60 * 60 * 24 * 2;
    }
    
    public static function load($id) {
        self::runGc();
        
        $context = new self($id);
        $context->restore();
        return $context;
    }
    
    public function save() {
        $storage =& self::getStorage();
        $expired = $this->expiration < time();
        if ($this->disposed || $expired) {
            unset($storage[$this->id]);
            return;
        }
        
        $storage[$this->id] = $this;
    }
    
    public function allocate() {
        $storage =& self::getStorage();
        do {
            $id = sha1(mt_rand());
        } while (isset($storage[$id]));
        
        $this->id = $id;
        $storage[$id] = $this;
    }
    
    public function restore() {
        $storage =& self::getStorage();
        $stored = isset($storage[$this->id]) ? $storage[$this->id] : null;
        $now = time();
        if (!$stored || $stored->disposed || $stored->expiration < $now) {
            $this->allocate();
            return;
        }
        
        $this->session = $stored->session;
        $this->expiration = $stored->expiration;
        $this->prepared = $stored->prepared;
    }
    
    public function updateExpiration($time = null) {
        if ($time === null) {
            $this->expiration = self::getDefaultExpiration();
        } else {
            $this->expiration = $time;
        }
    }
    
    public function dispose() {
        $storage =& self::getStorage();
        unset($storage[$this->id]);

        $this->disposed = true;
    }
    
    public function isPrepared() {
        return $this->prepared;
    }
    
    public function prepare() {
        $this->prepared = true;
    }
}
