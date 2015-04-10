<?php
namespace Lay\Core;

use Lay\Core\Component;
use Lay\Core\Modelizable;

use Lay\Traits\Singleton;

abstract class Model extends Component implements Modelizable {
    use Singleton;
    /**
     * 构造方法
     * @return Model
     */
    protected function __construct() {
        foreach ($this->properties() as $name) {
            $this->$name = null;
        }
    }
    /**
     * @see Component::properties()
     */
    public function properties() {
        return array();
    }
    /**
     * @see Component::rules()
     */
    public function rules() {
        return array();
    }
    /**
     * @see Component::format()
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
            if(isset($this->$name)) {
                $ret[$name] = $this->$name;
            }
        }
        return $ret;
    }

    public function get($id) {
        
    }
    public function add(array $info) {

    }
    public function del($id) {

    }
    public function upd($id, array $info) {

    }
    public function count(array $info = array()) {

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
 