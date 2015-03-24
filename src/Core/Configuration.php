<?php
/**
 * 配置数据访问类
 *
 * @author Lay Li
 */
namespace Lay\Core;

use Lay\Core\AbstractSingleton;
use Lay\Util\Util;

/**
 * 配置数据访问类
 *
 * @author Lay Li
 */
class Configuration extends AbstractSingleton {
    private static $_config = array();
    private static $_cachefile = 'restfulness.config.php';
    private static $_caches = array();
    private static $_cached = false;
    /**
     * 获取配置数据访问类实例
     * 
     * @return Configuration
     */
    public static function getInstance() {
        return parent::getInstance();
    }
    /**
     * 获取某个配置节点的值
     *
     * @param mixed $keystr
     *            要获取的节点键名
     * @param string $default
     *            默认值
     * @return mixed
     */
    /*public static function get($keystr, $default = NULL){
        $node = &self::$_config;
        if ($keystr){
            $keys = explode('.', $keystr);
            foreach ($keys as $key){
                if (isset($node[$key])){
                    $node = &$node[$key];
                }else{
                    return $default;
                }
            }
        }
        return $node;
    }*/
    /**
     * 设置某个配置节点的值
     *
     * @param mixed $keystr
     *            要设置的节点键名
     * @param mixed $value
     *            要设置的节点值
     * @return void
     */
    /*public static function set($name, $value){
        self::$_config[$name] = $value;
    }*/

    /**
     * 初始化配置项
     * @return void
     */
    public static function initialize() {
        $rootpath = App::$_rootpath;
        // 加载配置缓存
        $config = self::loadCache();
        // 没有缓存，加载配置
        if(empty($config)) {
            $envfile = $rootpath . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'env.php';
            self::configure($envfile);
            $env = self::get('env');
            $configfile = $rootpath . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'main.' . $env . '.php';
            self::configure($configfile);
        }
        // 注册shutdown事件
        register_shutdown_function(array('Lay\Core\Configuration', 'updateCache'));
    }
    /**
     * 加载并设置配置
     *
     * @param string|array $configuration
     *            配置文件或配置数组
     * @param boolean $isFile
     *            标记是否是配置文件
     * @return void
     */
    public static function configure($configuration, $isFile = true) {
        $_ROOTPATH = &App::$_rootpath;
        if(is_array($configuration) && ! $isFile) {
            foreach($configuration as $key => $item) {
                if(is_string($key) && $key) { // key is not null
                    self::set($key, $item);
                    self::setCache($key, $item);
                }
            }
        } else if(is_array($configuration)) {
            if(! empty($configuration)) {
                foreach($configuration as $index => $configfile) {
                    self::configure($configfile);
                }
            }
        } else if(is_string($configuration)) {
            //Logger::info('configure file:' . $configuration, 'CONFIGURE');
            if(is_file($configuration)) {
                $tmparr = include_once $configuration;
            } else if(is_file($_ROOTPATH . $configuration)) {
                $tmparr = include_once $_ROOTPATH . $configuration;
            } else {
                //Logger::warn($configuration . ' is not a real file', 'CONFIGURE');
                $tmparr = array();
            }
            
            if(empty($tmparr)) {
                self::configure($tmparr);
            } else {
                self::configure($tmparr, false);
            }
        } else {
            //Logger::warn('unkown configuration type', 'CONFIGURE');
        }
    }
    /**
     * 加载配置信息缓存
     *
     * @return void
     */
    private static function loadCache() {
        $rootpath = App::$_rootpath;
        $cachename = realpath(sys_get_temp_dir() . DIRECTORY_SEPARATOR . self::$_cachefile);
        if(is_file($cachename)) {
            self::$_caches = include $cachename;
        } else {
            return false;
        }
        if(is_array(self::$_caches) && ! empty(self::$_caches)) {
            self::$_config = array_merge(self::$_config, self::$_caches);
        }
        return self::$_config;
    }
    /**
     * 更新配置信息缓存
     *
     * @return boolean
     */
    public static function updateCache() {
        //Logger::info('self::$_cached:' . self::$_cached);
        if(! empty(self::$_cached)) {
            // 先读取，再merge，再存储
            $cachename = sys_get_temp_dir() . DIRECTORY_SEPARATOR . self::$_cachefile;
            //$caches = include realpath($cachename);
            //self::$_caches = array_merge(self::$_config, self::$_caches);
            // 写入
            $content = Util::array2PHPContent(self::$_caches);
            $handle = fopen($cachename, 'w');
            $result = fwrite($handle, $content);
            $return = fflush($handle);
            $return = fclose($handle);
            self::$_cached = false;
            return $result;
        } else {
            return false;
        }
    }
    /**
     * 设置新的配置项缓存
     *
     * @param string $classname
     *            类名
     * @param string $filepath
     *            类文件路径
     * @return void
     */
    private static function setCache($key, $value) {
        self::$_cached = true;
        self::$_caches[$key] = $value;
    }
    /**
     * 获取某个配置项的缓存或所有
     *
     * @param string $classname
     *            类名
     * @return mixed
     */
    public static function getCache($key = '') {
        if(is_string($key) && $key && isset(self::$_caches[$key])) {
            return self::$_caches[$key];
        } else {
            return self::$_caches;
        }
    }
    
    /**
     * 获取节点的值
     *
     * @param string $keystr
     *            要获取的节点键名
     * @return mixed
     */
    public static function get($keystr = '', $default = null) {
        if(self::checkKey($keystr)) {
            if(is_array($keystr) && $keystr) {
                $node = array();
                foreach($keystr as $i => $key) {
                    $node[$i] = self::get($key);
                }
            } else if(is_string($keystr) && $keystr) {
                $node = &self::$_config;
                $keys = explode('.', $keystr);
                foreach($keys as $key) {
                    if(isset($node[$key])) {
                        $node = &$node[$key];
                    } else {
                        return $default;
                    }
                }
            } else {
                $node = &self::$_config;
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
    public static function set($keystr, $value) {
        if(! self::checkKey($keystr)) {
            //Logger::warn('given key isnot supported;string,int is ok.', 'CONFIGURATION');
        } else {
            if(! self::checkValue($value)) {
                //Logger::warn('given value isnot supported;string,number,boolean is ok.', 'CONFIGURATION');
            } else {
                if(! self::checkKeyValue($keystr, $value)) {
                    //Logger::warn('given key and value isnot match;if key is array,value must be array.', 'CONFIGURATION');
                } else {
                    $node = &self::$_config;
                    if(is_array($keystr) && $keystr) {
                        foreach($keystr as $i => $key) {
                            self::set($key, isset($value[$i]) ? $value[$i] : false);
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
    private static function checkKey($key) {
        if(is_array($key)) {
            foreach($key as $i => $k) {
                if(! self::checkKey($k)) {
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
    private static function checkValue($value) {
        if(is_array($value)) {
            foreach($value as $i => $var) {
                if(! self::checkValue($var)) {
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
    private static function checkKeyValue($key, $value) {
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
// PHP END
