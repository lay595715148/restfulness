<?php
namespace Lay\Core;
//核心类
//use Core\AbstractSingleton;
use Lay\Core\Configuration as Config;
use Lay\Util\Util;
use Lay\Util\RESTful;
//use Lay\Autoloader;
//第3方类
use Illuminate\Database\Capsule\Manager as Capsule;
use Klein\Klein;
use Overtrue\Pinyin\Pinyin;

//use Aura\Router\Map;
//use Aura\Router\DefinitionFactory;
//use Aura\Router\RouteFactory;

use Respect\Validation\Validator as v;
//App类
use Lay\Web\User;
use Lay\Http\Request;
use Lay\Cgi\Index as CgiIndex;
use Lay\Cli\Index as CliIndex;

class App extends AbstractSingleton {
	public static $_rootpath;
    /**
     * @return core\App
     */
    public static function getInstance() {
    	return parent::getInstance();
    }
	public static function start() {
		static::$_rootpath = dirname(dirname(__DIR__));

		$app = static::getInstance();
		$app->brfore();
		$app->run();
		$app->after();
		$app->finish();
	}
	/**
	 *
	 * @see https://github.com/chriso/klein.php
	 */
	private $klein;
	public function brfore() {
		$rootpath = App::$_rootpath;
		$configfile = $rootpath . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'env.php';
		static::configure($configfile);
	}
	public function run() {
		global $_PUT, $_DELETE, $_PATCH, $_HEAD, $_OPTIONS;
		$request = Request::getInstance();
		$this->klein = $klein = new Klein();
		$routers = Config::get('routers') ? : array();

		foreach ($routers as $k => $config) {
			$klein->respond($k, function($req, $res) use ($klein, $config, $request) {
				$classname = $config['class'];
				if(class_exists($classname) && $action = $classname::getInstance()) {
					$method = $request->getMethod();
					$fnname = 'on' . ucfirst(strtolower($method));
					$action->{$fnname}();
					$action->onStop();
				} else {
					throw new \Exception($classname . ' not found!');
				}
			});
		}
		$klein->dispatch();

		/*$pathinfo = $request->getPathinfo();
		extract($pathinfo);
		$classname = trim(preg_replace('/\//', '\\', $dirname . DIRECTORY_SEPARATOR . $filename), '\\ ');
		if(strtoupper(php_sapi_name()) == 'CLI') {
			$classname = '\\Lay\\Cli\\' . implode('\\', array_map('ucfirst', explode('\\', $classname)));
		} else {
			$classname = '\\Lay\\Cgi\\' . implode('\\', array_map('ucfirst', explode('\\', $classname)));
		}
		if(class_exists($classname)) {
			$action = $classname::getInstance();
		} else {
			throw new \Exception($classname . ' not found!');
		}


		$action->onCreate();
		$method = $request->getMethod();
		$fnname = 'on' . ucfirst(strtolower($method));
		$action->{$fnname}();
		$action->onStop();*/
		return;
	}
	public function route() {

	}
	public function after() {
		
	}
	public function finish() {
		
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
    public function configure($configuration, $isFile = true) {
        $_ROOTPATH = &static::$_rootpath;
        if(is_array($configuration) && ! $isFile) {
            foreach($configuration as $key => $item) {
                if(is_string($key) && $key) { // key is not null
                    switch($key) {
                        case 'actions':
                        case 'services':
                        case 'stores':
                        case 'beans':
                        case 'models':
                        case 'templates':
                            if(is_array($item)) {
                                $actions = static::get($key);
                                foreach($item as $name => $conf) {
                                    if(is_array($actions) && array_key_exists($name, $actions)) {
                                        //Logger::warn('$configuration["' . $key . '"]["' . $name . '"] has been configured', 'CONFIGURE');
                                    } else if(is_string($name) || is_numeric($name)) {
                                        static::set($key . '.' . $name, $conf);
                                    }
                                }
                            } else {
                                //Logger::warn('$configuration["' . $key . '"] is not an array', 'CONFIGURE');
                            }
                            break;
                        case 'files':
                            if(is_array($item)) {
                                foreach($item as $file) {
                                    static::configure($file);
                                }
                            } else if(is_string($item)) {
                                $this->configure($item);
                            } else {
                                //Logger::warn('$configuration["files"] is not an array or string', 'CONFIGURE');
                            }
                            break;
                        case 'logger':
                            // update Logger
                            //Logger::initialize($item);
                        default:
                            static::set($key, $item);
                            break;
                    }
                } else {
                    static::set($key, $item);
                }
            }
        } else if(is_array($configuration)) {
            if(! empty($configuration)) {
                foreach($configuration as $index => $configfile) {
                    $this->configure($configfile);
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
                $this->configure($tmparr);
            } else {
                $this->configure($tmparr, false);
            }
        } else {
            //Logger::warn('unkown configuration type', 'CONFIGURE');
        }
    }
    /**
     * 设置某个配置项
     *
     * @param string|array $keystr
     *            键名
     * @param string|boolean|int|array $value
     *            键值
     * @return void
     */
    public static function set($keystr, $value) {
        Configuration::set($keystr, $value);
    }
    /**
     * 获取某个配置项
     *
     * @param string $keystr
     *            键名，子键名配置项使用.号分割
     * @param mixed $default
     *            不存在时的默认值，默认null
     * @return mixed
     */
    public static function get($keystr = '', $default = null) {
        if(($ret = Configuration::get($keystr)) === null) {
            return $default;
        } else {
            return $ret;
        }
    }
}

// PHP END
