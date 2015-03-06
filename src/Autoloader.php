<?php
use util\Util;

class Autoloader {
	private static $_classpath = __DIR__;
	private static $_classes = array();
	private static $_cachefile = 'restfulness.classes.php';
	private static $_caches = array();
	private static $_cached = false;
    /**
     * 
     * @return void
     */
	public static function register() {
        // 使用自定义的autoload方法
        spl_autoload_register('Autoloader::autoload');
        // 注册异常句柄
        set_error_handler('Autoloader::handler');
        // 加载类文件路径缓存
        self::loadCache();


	}
    /**
     * 自定义添加类文件路径
     * @param string $classname
     * @param string $filepath
     * @return void
     */
    public static function custom($classname, $filepath, $force = false) {
        $classes = &self::$_classes;
        if(is_file($filepath) && (! array_key_exists($classname, $classes) || $force)) {
            self::setCache($classname, realpath($filepath));
        }
    }
    /**
     * @param string $classname
     * @return void
     */
	public static function autoload($classname) {
        if(empty(self::$_classpath)) {
            self::check($classname);
        } else {
        	$paths = explode(';', self::$_classpath);
            foreach($paths as $path) {
                self::load($classname, $path);
            }
            if(! self::exists($classname, false)) {
                self::check($classname);
            }
        }
	}
    /**
     * 
     * @return void
     */
	private static function load($classname, $classpath = '', $suffixes = array('.php', '.class.php')) {
		$classes = &self::$_classes;
        // 全名映射查找
        if(array_key_exists($classname, $classes)) {
            if(is_file($classes[$classname])) {
                require_once $classes[$classname];
            } else if(is_file($classpath . $classes[$classname])) {
                require_once $classpath . $classes[$classname];
            }
        }
        if(! self::exists($classname, false)) {
            $tmparr = explode("\\", $classname);
            // 通过命名空间查找
            if(count($tmparr) > 1) {
                $name = array_pop($tmparr);
                $path = $classpath . DIRECTORY_SEPARATOR . implode(DIRECTORY_SEPARATOR, $tmparr);
                $required = false;
                // 命名空间文件夹查找
                if(is_dir($path)) {
                    $tmppath = $path . DIRECTORY_SEPARATOR . $name;
                    foreach($suffixes as $i => $suffix) {
                        if(is_file($tmppath . $suffix)) {
                            $filepath = realpath($tmppath . $suffix);
                            self::setCache($classname, $filepath);
                            require_once $filepath;
                            break;
                        }
                    }
                }
            }
        }
        // 正则匹配后进行查找
        $reg = '/([A-Z]{1,}[a-z0-9]{0,}|[a-z0-9]{1,})_{0,1}/';
        if(! self::exists($classname, false) && preg_match_all($reg, $classname, $matches) > 0) {
            $tmparr = array_values($matches[1]);
            $prefix = array_shift($tmparr);
            // 直接以类名作为文件名查找
            foreach($suffixes as $i => $suffix) {
                $tmppath = $classpath . DIRECTORY_SEPARATOR . $classname;
                if(is_file($tmppath . $suffix)) {
                    $filepath = realpath($tmppath . $suffix);
                    self::setCache($classname, $filepath);
                    require_once $filepath;
                    break;
                }
            }
        }
        // 如果以上没有匹配，则使用类名递归文件夹查找，如使用小写请保持（如果第一递归文件夹使用了小写，即之后的文件夹名称保持小写）
        if(! self::exists($classname, false) && ! empty($matches)) {
            $path = $lowerpath = $classpath;
            foreach($matches[1] as $index => $item) {
                $path .= DIRECTORY_SEPARATOR . $item;
                $lowerpath .= DIRECTORY_SEPARATOR . strtolower($item);
                //Logger::info('$lowerpath:' . $lowerpath.':$classname:'.$classname);
                if(($isdir = is_dir($path)) || is_dir($lowerpath)) { // 顺序文件夹查找
                    $tmppath = ($isdir ? $path : $lowerpath) . DIRECTORY_SEPARATOR . $classname;
                    foreach($suffixes as $i => $suffix) {
                        if(is_file($tmppath . $suffix)) {
                            $filepath = realpath($tmppath . $suffix);
                            self::setCache($classname, $filepath);
                            require_once $filepath;
                            break 2;
                        }
                    }
                    continue;
                } else if($index == count($matches[1]) - 1) {
                    foreach($suffixes as $i => $suffix) {
                        if(($isfile = is_file($path . $suffix)) || is_file($lowerpath . $suffix)) {
                            $filepath = realpath(($isfile ? $path : $lowerpath) . $suffix);
                            self::setCache($classname, $filepath);
                            require_once $filepath;
                            break 2;
                        }
                    }
                    break;
                } else {
                    // 首个文件夹都已经不存在，直接退出loop
                    break;
                }
            }
        }
	}
    /**
     * 加载类路径缓存
     *
     * @return void
     */
    public static function loadCache() {
        $cachename = realpath(sys_get_temp_dir() . DIRECTORY_SEPARATOR . self::$_cachefile);
        if(is_file($cachename)) {
            self::$_caches = include $cachename;
        } else {
            self::$_caches = array();
        }
        if(is_array(self::$_caches) && ! empty(self::$_caches)) {
            self::$_classes = array_merge(self::$_classes, self::$_caches);
        }
    }
    /**
     * 更新类路径缓存
     *
     * @return boolean
     */
    public static function updateCache() {
        //Logger::info('self::$_cached:' . self::$_cached);
        if(! empty(self::$_cached)) {
            // 先读取，再merge，再存储
            $cachename = sys_get_temp_dir() . DIRECTORY_SEPARATOR . self::$_cachefile;
            if(is_file($cachename)) {
                $caches = include realpath($cachename);
                self::$_caches = array_merge($caches, self::$_caches);
            }
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
     * 设置新的类路径缓存
     *
     * @param string $classname
     *            类名
     * @param string $filepath
     *            类文件路径
     * @return void
     */
    private static function setCache($classname, $filepath) {
        self::$_cached = true;
        self::$_caches[$classname] = realpath($filepath);
    }
    /**
     * 获取某个类路径缓存或所有
     *
     * @param string $classname
     *            类名
     * @return mixed
     */
    public static function getCache($classname = '') {
        if(is_string($classname) && $classname && isset(self::$_caches[$classname])) {
            return self::$_caches[$classname];
        } else {
            return self::$_caches;
        }
    }
    /**
     * 判断是否还有其他自动加载函数，如没有则抛出异常
     *
     * @return void
     * @throws Exception
     */
    private static function check() {
        // 判断是否还有其他自动加载函数，如没有则抛出异常
        $funs = spl_autoload_functions();
        $count = count($funs);
        foreach($funs as $i => $fun) {
            if($fun[0] == 'App' && $fun[1] == 'autoload' && $count == $i + 1) {
                throw new Exception($classname.' not found by autoload function');
            }
        }
    }
    /**
     * 类或接口是否存在
     *
     * @param string $classname 类名或接口名
     * @param boolean $autoload 是否自动加载
     * @return boolean
     */
    public static function exists($classname, $autoload = true) {
        return class_exists($classname, $autoload) || interface_exists($classname, $autoload);
    }

    /**
     * 
     * @param int $errno 
     * @param string $errstr 
     * @param string $errfile 
     * @param int $errline 
     * @param mixed $errcontext 
     * @return void
     */
    public static function handler($errno, $errstr, $errfile, $errline, $errcontext) {
        throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
    }
}

// PHP END
