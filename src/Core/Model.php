<?php
namespace Lay\Core;

use Lay\Core\AbstractModel;
use Lay\Core\InterfaceModel;

use Lay\Traits\Singleton;

abstract class Model extends Bean implements InterfaceModel {
    protected $db;
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
    public function save() {
        // TODO
        $pk = $this->primary();
        $data = $this->toArray();
        $data = array_filter($data, function ($var) {
            return $var !== null;
        });
        if ($this->$pk) {
            unset($data[$pk]);
            $this->upd($this->$pk, $data);
        } else {
            $last_id = $this->add($data);
            if ($last_id) {
                $this->$pk = $last_id;
            }
        }
    }
}
// PHP END
 