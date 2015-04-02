<?php
namespace Lay\Core;
//核心类
//use Core\AbstractSingleton;
use Lay\Core\Configuration;
use Lay\Core\EventEmitter;
use Lay\Core\Action;
use Lay\Util\Utility;
use Lay\Util\RESTful;
use Lay\Util\Logger;
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

use Exception;
use ErrorException;

abstract class App extends AbstractSingleton {
    const E_BEFORE = 'app:event:before';
    const E_RUN = 'app:event:run';
    const E_AFTER = 'app:event:after';
    const E_FINISH = 'app:event:finish';
	public static $_rootpath;
    public static $_docpath;
    public static $_action;
    public static $_config;
    public static $_logger;
    public static $_event;
    public static $_app;
    /**
     * 运行，创建App并启动App的生命同期
     * @return void
     */
	public static function start() {
        //ob start
        ob_start();
        error_reporting(E_ALL ^ E_STRICT);
        ini_set('output_buffering', 'on');
        ini_set('implicit_flush', 'off');
        try {
            // initialize root path
            self::$_rootpath = dirname(dirname(__DIR__));
            // initialize document path
            self::$_docpath = $_SERVER['DOCUMENT_ROOT'];
            // initialize Logger
            self::$_logger = Logger::getInstance();
            self::$_logger->initialize();
            // initialize EventEmitter
            self::$_event = EventEmitter::getInstance();
            self::$_event->initialize();
            // initialize Configuration
            self::$_config = Configuration::getInstance();
            self::$_config->initialize();
            // initialize App
            self::$_app = self::getInstance();
            self::$_app->initialize();
            self::$_app->lifecycle();
        } catch (Exception $err) {
            $log = '[' . date('Y-m-d H:i:s') . "]==============================================================>\n";
            $log .= $err->getMessage() . '(' . $err->getCode() . ")\n";
            $log .= $err->getFile() . '(' . $err->getLine() . ")\n";
            $log .= $err->getTraceAsString() . "\n";
            $log .= "<===================================================================================\n";
            self::$_logger->error($log);
        }
	}
	/**
	 *
	 * @see https://github.com/chriso/klein.php
	 */
	protected $klein;
    protected $routers = array();
    /**
     * App初始化
     * @return void
     */
    public function initialize() {
    }
    /**
     * App生命同期
     * @return void
     */
    public function lifecycle() {
        // before
        $this->brfore();
        self::$_event->fire($this, self::E_BEFORE, array($this));
        // run
        $this->run();
        self::$_event->fire($this, self::E_RUN, array($this));
        // after
        $this->after();
        self::$_event->fire($this, self::E_AFTER, array($this));
        // finish
        $this->finish();
        self::$_event->fire($this, self::E_FINISH, array($this));
    }
	protected function brfore() {
	}
	protected function run() {
		global $_PUT, $_DELETE, $_PATCH, $_HEAD, $_OPTIONS;
		$request = Request::getInstance();
        if(strtoupper(php_sapi_name()) == 'CLI') {
            $pathinfo = $request->getPathinfo();
            extract($pathinfo);
            $classname = '\\Lay\\Cli\\' . implode('\\', array_map('ucfirst', explode('\\', $classname)));
            if(class_exists($classname) && self::$_action = $classname::getInstance()) {
                self::$_action->initialize();
                self::$_action->lifecycle();
            } else {
                throw new Exception($classname . ' not found!');
            }
        } else {
            $this->klein = $klein = new Klein();
            
            foreach ($this->routers as $k => $config) {
                $klein->respond($k, function($req, $res) use ($klein, $config, $request) {
                    $classname = $config['class'];
                    if(class_exists($classname) && self::$_action = $classname::getInstance()) {
                        self::$_action->initialize();
                        self::$_action->lifecycle();
                    } else {
                        throw new Exception($classname . ' not found!');
                    }
                });
            }
            $klein->dispatch();
        }
	}
	protected function after() {
	}
	protected function finish() {
		if(function_exists('fastcgi_finish_request')) {
            fastcgi_finish_request();
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
        return Configuration::get($keystr, $default);
    }
}

// PHP END
