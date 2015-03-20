<?php
namespace Lay\Core;

abstract class AbstractSingleton {
    protected static $_singletonStack = array();

    public static function getInstance(){
        $classname = get_called_class();
        if (empty(self::$_singletonStack[$classname])){
            self::$_singletonStack[$classname] = new $classname();
        }
        return self::$_singletonStack[$classname];
    }

    protected function __construct() {
    }
}
// PHP END