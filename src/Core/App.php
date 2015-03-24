<?php
namespace Lay\Core;
//核心类
//use Core\AbstractSingleton;
use Lay\Core\Configuration;
use Lay\Core\EventEmitter;
use Lay\Core\Action;
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
    const E_BEFORE = 'app:event:before';
    const E_RUN = 'app:event:run';
    const E_AFTER = 'app:event:after';
    const E_FINISH = 'app:event:finish';
	public static $_rootpath;
    public static $_config;
    public static $_event;
    public static $_app;
    /**
     * @return core\App
     */
    public static function getInstance() {
    	return parent::getInstance();
    }
	public static function start() {
		self::$_rootpath = dirname(dirname(__DIR__));

        self::$_event = $event = EventEmitter::getInstance();

		self::$_app = $app = self::getInstance();

        Configuration::initialize();
        // before
        $event->fire($this, self::E_BEFORE, array($this));
		$app->brfore();
        // run
        $event->fire($this, self::E_RUN, array($this));
		$app->run();
        // after
        $event->fire($this, self::E_AFTER, array($this));
		$app->after();
        // finish
        $event->fire($this, self::E_FINISH, array($this));
		$app->finish();
	}
	/**
	 *
	 * @see https://github.com/chriso/klein.php
	 */
	private $klein;
	public function brfore() {
	}
    private function lifecycle($action) {
        $request = Request::getInstance();
        self::$_event->fire($action, Action::E_CREATE, array($action));
        $action->onCreate();
        $method = $request->getMethod();
        switch (strtoupper($method)) {
            case 'POST':
                $event = Action::E_POST;
                break;
            case 'PUT':
                $event = Action::E_PUT;
                break;
            case 'DELETE':
                $event = Action::E_DELETE;
                break;
            case 'PATCH':
                $event = Action::E_PATCH;
                break;
            case 'HEAD':
                $event = Action::E_HEAD;
                break;
            case 'OPTIONS':
                $event = Action::E_OPTIONS;
                break;
            case 'GET':
            default:
                $event = Action::E_GET;
                break;
        }
        $fnname = 'on' . ucfirst(strtolower($method));
        self::$_event->fire($action, $event, array($action));
        $action->{$fnname}();
        self::$_event->fire($action, Action::E_STOP, array($action));
        $action->onStop();
    }
	public function run() {
		global $_PUT, $_DELETE, $_PATCH, $_HEAD, $_OPTIONS;
		$request = Request::getInstance();
        if(strtoupper(php_sapi_name()) == 'CLI') {
            $pathinfo = $request->getPathinfo();
            extract($pathinfo);
            $classname = '\\Lay\\Cli\\' . implode('\\', array_map('ucfirst', explode('\\', $classname)));
            if(class_exists($classname) && $action = $classname::getInstance()) {
                $this->lifecycle($action);
            } else {
                throw new \Exception($classname . ' not found!');
            }
        } else {
            $this->klein = $klein = new Klein();
            $routers = self::get('routers', array());
            
            foreach ($routers as $k => $config) {
                $klein->respond($k, function($req, $res) use ($klein, $config, $request) {
                    $classname = $config['class'];
                    if(class_exists($classname) && $action = $classname::getInstance()) {
                        $this->lifecycle($action);
                    } else {
                        throw new \Exception($classname . ' not found!');
                    }
                });
            }
            $klein->dispatch();
        }
	}
	public function after() {
	}
	public function finish() {
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
        if(($ret = Configuration::get($keystr)) === null) {
            return $default;
        } else {
            return $ret;
        }
    }
}

// PHP END
