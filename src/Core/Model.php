<?php
namespace Lay\Core;

use Lay\Core\App;
use Lay\Core\Component;
use Lay\Core\Modelizable;
use Lay\Core\Asynchronous;
use Lay\DB\DataBase;
use Lay\Traits\Singleton;

abstract class Model extends Component implements Modelizable {
    use Singleton;
    const E_GET = 'model:event:get';
    const E_ADD = 'model:event:add';
    const E_DELETE = 'model:event:delete';
    const E_UPDATE = 'model:event:update';
    const E_COUNT = 'model:event:count';
    /**
     * 构造方法
     * @return Model
     */
    protected final function __construct() {
        foreach ($this->properties() as $name) {
            $this->$name = null;
        }
        $this->listen();
    }
    protected final function listen() {
        App::$_event->listen($this, self::E_GET, array($this, 'async'));
        App::$_event->listen($this, self::E_ADD, array($this, 'async'));
        App::$_event->listen($this, self::E_DELETE, array($this, 'async'));
        App::$_event->listen($this, self::E_UPDATE, array($this, 'async'));
        App::$_event->listen($this, self::E_COUNT, array($this, 'async'));
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


    /**
     * 返回对象属性名对属性值的数组
     * @return DataBase
     */
    public abstract function db();

    public function async($sign, $params = array()) {
        switch ($sign) {
            case 'get':
                break;
            case 'add':
                break;
            case 'del':
                break;
            case 'upd':
                break;
            case 'count':
                break;
            default:
                break;
        }
    }

    public final function get($id) {
        $ret = $this->db()->get($id);
        App::$_event->fire($this, self::E_GET, array('get', array()));
    }
    public final function add(array $info) {
        $ret = $this->db()->get($id);
        App::$_event->fire($this, self::E_ADD, array('add', array()));
    }
    public final function del($id) {
        $ret = $this->db()->get($id);
        App::$_event->fire($this, self::E_DELETE, array('del', array()));
    }
    public final function upd($id, array $info) {
        $ret = $this->db()->get($id);
        App::$_event->fire($this, self::E_UPDATE, array('upd', array()));
    }
    public final function count(array $info = array()) {
        App::$_event->fire($this, self::E_COUNT, array('count', array()));
    }
    public final function save() {
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
 