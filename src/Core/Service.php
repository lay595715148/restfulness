<?php
namespace Lay\Core;

use Lay\Core\Component;
use Lay\Core\Model;
use Lay\Traits\Singleton;
use Lay\Util\Logger;
use Exception;

abstract class Service extends Component implements Serviceable {
	use Singleton;
    /**
     * 构造方法
     * @return Service
     */
    protected final function __construct() {
        //初始化
        foreach ($this->properties() as $name => $class) {
            if(is_subclass_of($class, 'Lay\Core\Model')) {
                $this->$name = $class::getInstance();
            } else {
                throw new Exception("$class is not subclass of Lay\Core\Model");
            }
        }
    }
    /**
     * 主Model类名
     * @return string
     */
    public abstract function basic();
    /**
     * 其他Model类名数组
     * array('other' => 'Lay\Model\Other')
     * @return array
     */
    public abstract function associates();
    /**
     * @see Component::properties()
     * @return array array('model' => 'Lay\Model\Class')
     */
    public final function properties() {
        $properties  = array();
        //basic model
        $properties['model'] = $this->basic();
        foreach ($this->associates() as $name => $value) {
            $properties[$name] = $value;
        }
        return $properties;
    }
    /**
     * @see Component::rules()
     */
    public final function rules() {
        $rules = array();
        foreach ($this->properties() as $name => $class) {
            $rules[$name] = array(Component::TYPE_FORMAT, array('class' => $class));
        }
        return $rules;
    }
    /**
     * @return Model
     */
    public final function format($val, $option = array()) {
        $class = empty($option['class']) ? 'Lay\Core\Model' : $option['class'];
        if(is_a($val, $class)) {
            return $val;
        } else {
            Logger::error('given value is not class ' . $class);
            return null;
        }
    }
    /**
     * 获取某条记录
     * 
     * @param int|string $id
     *            ID
     * @return array
     */
    public function get($id) {
        return $this->model->get($id);
    }
    /**
     * 增加一条记录
     * 
     * @param array $info
     *            数据数组
     * @return boolean
     */
    public function add(array $info) {
        return $this->model->add($info);
    }
    /**
     * 删除某条记录
     * 
     * @param int|string $id
     *            ID
     * @return boolean
     */
    public function del($id) {
        return $this->model->del($id);
    }
    /**
     * 更新某条记录
     * 
     * @param int|string $id
     *            ID
     * @param array $info
     *            数据数组
     * @return boolean
     */
    public function upd($id, array $info) {
        return $this->model->upd($id, $info);
    }
    /**
     * 某些条件下的记录数
     * 
     * @param array $info
     *            数据数组
     * @return int
     */
    public function count(array $info = array()) {
        return $this->model->count($info);
    }
}

// PHP END