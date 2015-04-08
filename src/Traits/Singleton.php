<?php
namespace Lay\Traits;

// 单例模式
trait Singleton {
    protected static $instance;
    protected function __construct() {}
    public function __clone() {
        throw new \RuntimeException('Cloning '. get_called_class() .' is not allowed');
    }
    public static function getInstance() {
        return static::$instance ? static::$instance : (static::$instance = new static);
    }
}

// PHP END