<?php
namespace Lay\Core;

use Lay\Core\Base;
use Lay\Util\Logger;
use Lay\Util\Utility;
use Lay\Core\InterfaceBean;
use ReflectionObject;
use ReflectionProperty;
use Iterator;
use ArrayAccess;
use stdClass;

abstract class Bean extends Base implements InterfaceBean {
    public final function __construct() {
        //初始化值
        foreach ($this->properties() as $name => $value) {
            $this->$name = $value;
        }
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
     * 清空对象所有属性值
     * @return InterfaceBean
     */
    public final function restore() {
        // 恢复默认值
        foreach ($this->properties() as $name => $value) {
            $this->$name = $value;
        }
        return $this;
    }
    /**
     * 返回对象属性名对属性值的数组
     * @return array
     */
    public final function toArray() {
        $ret = array();
        foreach ($this->properties() as $name => $def) {
            $ret[$name] = $this->_toArray($this[$name]);
        }
        return $ret;
    }
    /**
     * 迭代返回对象属性名对属性值的数组
     * @param mixed $val            
     * @return mixed
     */
     protected final function _toArray($val) {
        if(is_array($val)) {
            $var = array();
            foreach($val as $k => $v) {
                $var[$k] = $this->_toArray($v);
            }
            return $var;
        } else if(is_object($val) && method_exists($val, 'toArray')) {
            return $val->toArray();
        } else if(is_object($val)) {
            return $this->_toArray(get_object_vars($val));
        } else {
            return $val;
        }
    }
    /**
     * 返回对象转换为stdClass后的对象
     * @return stdClass
     */
    public final function toStandard() {
        $ret = new stdClass();
        foreach ($this->properties() as $name => $value) {
            $ret->$name = $this->_toStandard($this[$name]);
        }
        return $ret;
    }
    /**
     * 迭代返回对象转换为stdClass后的对象
     * @param mixed $var            
     * @return mixed
     */
    protected function _toStandard($val) {
        if(is_array($val) && Utility::isAssocArray($val)) {
            $var = new stdClass();
            foreach($val as $k => $v) {
                $var->$k = $this->_toStandard($v);
            }
            return $var;
        } else if(is_array($val)) {
            $var = array();
            foreach($val as $k => $v) {
                $var[$k] = $this->_toStandard($v);
            }
            return $var;
        } else if(is_object($val) && method_exists($val, 'toStandard')) {
            return $val->toStandard();
        } else if(is_object($val)) {
            return $this->_toArray(get_object_vars($val));
        } else {
            return $val;
        }
    }

    /**
     * json serialize function
     * 
     * @return stdClass
     */
    public function jsonSerialize() {
        return $this->toStandard();
    }
}

// PHP END
