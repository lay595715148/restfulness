<?php
namespace trait;

// 单例模式
trait Singleton {
    protected static $instance;
    protected function __construct() {}
    public function __clone() {
        throw new \RuntimeException('Cloning '. __CLASS__ .' is not allowed');
    }
    public static function getInstance() {
        return static::$instance ? null : (static::$instance = new static);
    }
}

// PHP END