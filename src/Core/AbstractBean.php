<?php
namespace Lay\Core;

use ArrayAccess;
use ReflectionObject;

abstract class AbstractBean extends ArrayAccess {
	protected $properties = array();
    protected function propDefault($key) {
        return null;
    }

    public final function __set($key, $value) {
        $this->properties[$key] = $value;
    }
    public final function __get($key) {
        if (!isset($this->properties[$key])) {
            $this->properties[$key] = $this->propDefault($key);
        }
        return $this->properties[$key];
    }
    /**
     * 检测属性是否设置
     *
     * @param string $name
     *            属性名
     * @return boolean
     */
    public final function __isset($name) {
        return isset($this->properties[$name]);
    }
    /**
     * 将某个属性去除
     *
     * @param string $name
     *            属性名
     * @return void
     */
    public final function __unset($name) {
    	return unset($this->properties[$name]);
    }

    public function offsetExists($index) {
        return property_exists($this, $index);
    }
    public function offsetGet($index) {
        return $this->$index;
    }
    public function offsetSet($index, $value) {
        $this->$index = $value;
    }
    public function offsetUnset($index) {
        unset($this->$index);
    }
    public function toArray() {
        $ref = new ReflectionObject($this);
        $props = $ref->getProperties(ReflectionProperty::IS_PUBLIC);
        $ret = array();
        foreach ($props as $prop) {
            $ret[$prop->name] = $this[$prop->name];
        }
        return $ret;
    }
}

// PHP END
