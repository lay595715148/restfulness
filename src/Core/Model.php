<?php
namespace Lay\Core;

use Lay\Core\Base;
use Lay\Core\InterfaceModel;

use Lay\Traits\Singleton;

abstract class Model extends Base implements InterfaceModel {
    use Singleton;
    protected $db = array();
    /**
     * 构造方法
     * @return Model
     */
    protected function __construct() {
    }
    /**
     * @see Base::properties()
     */
    public function properties() {
        return array();
    }
    /**
     * @see Base::rules()
     */
    public function rules() {
        return array();
    }
    /**
     * @see Base::format()
     */
    public function format($val, $options = array()) {
        return $val;
    }
    /**
     * 返回对象属性名对属性值的数组
     * @return array
     */
    public final function toArray() {
        $ret = array();
        foreach ($this->properties() as $name) {
            $ret[$name] = strval($this[$name]);
        }
        return $ret;
    }

    public function save() {
        // TODO
        $pk = $this->primary();
        $data = $this->toArray();
        /*$data = array_filter($data, function ($var) {
            return $var !== null;
        });*/
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
 