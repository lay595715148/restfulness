<?php
/**
 * 配置数据访问类
 *
 * @author Lay Li
 */
namespace Lay\Core;

//use core\AbstractSingleton;

/**
 * 配置数据访问类
 *
 * @author Lay Li
 */
class Configuration extends AbstractSingleton {
    /**
     * 获取配置数据访问类实例
     * 
     * @return Configuration
     */
    public static function getInstance() {
        return parent::getInstance();
    }
    /**
     * 获取某个app的节点值
     *
     * @param mixed $keystr
     *            要获取的节点键名
     * @param string $appname
     *            app名称
     * @param string $classname
     *            类名
     * @return mixed
     */
    public static function get($keystr) {
        return self::getInstance()->getter($keystr);
    }
    /**
     * 设置某个app的节点值
     *
     * @param mixed $keystr
     *            要设置的节点键名
     * @param mixed $value
     *            要设置的节点值
     * @param string $appname
     *            app名称
     * @param string $classname
     *            类名
     * @return void
     */
    public static function set($keystr, $value) {
        self::getInstance()->setter($keystr, $value);
    }
    
    /**
     * 数据根节点
     *
     * @var array
     */
    private $configuration = array();
    /**
     * 获取节点的值
     *
     * @param string $keystr
     *            要获取的节点键名
     * @return mixed
     */
    public function getter($keystr, $default = null) {
        if($this->checkKey($keystr)) {
            if(is_array($keystr) && $keystr) {
                $node = array();
                foreach($keystr as $i => $key) {
                    $node[$i] = $this->getter($key);
                }
            } else if(is_string($keystr) && $keystr) {
                $node = &$this->configuration;
                $keys = explode('.', $keystr);
                foreach($keys as $key) {
                    if(isset($node[$key])) {
                        $node = &$node[$key];
                    } else {
                        return $default;
                    }
                }
            } else {
                $node = &$this->configuration;
            }
            return $node;
        } else {
            return $default;
        }
    }
    /**
     * 设置节点的值
     *
     * @param array|string|int $keystr
     *            要设置的节点键名
     * @param array|string|number|boolean $value
     *            要设置的节点值
     * @return void
     */
    public function setter($keystr, $value) {
        if(! $this->checkKey($keystr)) {
            //Logger::warn('given key isnot supported;string,int is ok.', 'CONFIGURATION');
        } else {
            if(! $this->checkValue($value)) {
                //Logger::warn('given value isnot supported;string,number,boolean is ok.', 'CONFIGURATION');
            } else {
                if(! $this->checkKeyValue($keystr, $value)) {
                    //Logger::warn('given key and value isnot match;if key is array,value must be array.', 'CONFIGURATION');
                } else {
                    $node = &$this->configuration;
                    if(is_array($keystr) && $keystr) {
                        foreach($keystr as $i => $key) {
                            $this->setter($key, isset($value[$i]) ? $value[$i] : false);
                        }
                    } else if(is_string($keystr) && $keystr) {
                        $keys = explode('.', $keystr);
                        $count = count($keys);
                        foreach($keys as $index => $key) {
                            if(isset($node[$key]) && $index === $count - 1) {
                                // warning has been configured by this name
                                //Logger::warn('$configuration["' . implode('"]["', $keys) . '"] has been configured.', 'CONFIGURATION');
                                $node[$key] = $value;
                            } else if(isset($node[$key])) {
                                $node = &$node[$key];
                            } else if($index === $count - 1) {
                                $node[$key] = $value;
                            } else {
                                $node[$key] = array();
                                $node = &$node[$key];
                            }
                        }
                    }
                }
            }
        }
    }
    /**
     * 检测是否符合规定的格式，支持array,string,int,且数组中也必须符合此格式
     *
     * @param array|string|int $key
     *            节点键名
     * @return boolean
     */
    private function checkKey($key) {
        if(is_array($key)) {
            foreach($key as $i => $k) {
                if(! $this->checkKey($k)) {
                    return false;
                }
            }
            return true;
        } else if(is_string($key) || is_int($key)) {
            return true;
        } else {
            return false;
        }
    }
    /**
     * 检测是否符合规定的格式，支持array,string,number,boolean,且数组中也必须符合此格式
     *
     * @param array|string|number|boolean $value
     *            节点值
     * @return boolean
     */
    private function checkValue($value) {
        if(is_array($value)) {
            foreach($value as $i => $var) {
                if(! $this->checkValue($var)) {
                    return false;
                }
            }
            return true;
        } else if(is_bool($value) || is_string($value) || is_numeric($value)) {
            return true;
        } else {
            return false;
        }
    }
    /**
     * 检测节点键名与节点值是否是对应类型
     * 
     * @param array $key
     *            节点键名
     * @param array $value
     *            节点值
     * @return boolean
     */
    private function checkKeyValue($key, $value) {
        if(is_array($key)) {
            if(is_array($value)) {
                return true;
            } else {
                return false;
            }
        } else {
            return true;
        }
    }
}
?>
