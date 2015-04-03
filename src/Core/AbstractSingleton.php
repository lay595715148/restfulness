<?php
namespace Lay\Core;

use RuntimeException;

// 单例模式
abstract class AbstractSingleton {
    protected static $_singletonStack = array();
    protected function __construct() {}
    public function __clone() {
        throw new RuntimeException('Cloning '. __CLASS__ .' is not allowed');
    }
    public static function getInstance() {
        $classname = get_called_class();
        if (empty(self::$_singletonStack[$classname])){
            self::$_singletonStack[$classname] = new $classname();
        }
        return self::$_singletonStack[$classname];
    }
}
// PHP END