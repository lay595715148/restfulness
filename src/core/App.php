<?php
namespace core;

use util\Util;
use core\AbstractSingleton;
use core\Configuration;
use util\RESTful;

class App extends AbstractSingleton {
	public static $_rootpath;
    /**
     * @return core\App
     */
    public static function getInstance() {
    	return parent::getInstance();
    }
	public static function start() {
		//echo php_strip_whitespace(__FILE__);
		//highlight_string(Util::array2PHPContent(array('A' => 'a')));
		App::$_rootpath = dirname(dirname(__DIR__));
		$app = self::getInstance();
		$app->brfore();
		$app->run();
		$app->after();
		$app->finish();
	}
	public function brfore() {
		$rootpath = App::$_rootpath;
		$configfile = $rootpath . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'env.php';
		//Configuration::configure($configfile);
	}
	public function run() {
		global $_PUT, $_DELETE;
		$pathname = preg_replace('/^(.*)(\?)(.*)$/', '$1', $_SERVER['REQUEST_URI']);
		$pathinfo = pathinfo($pathname);
		extract($pathinfo);
		$extension = $pathinfo['extension'];
		$classname = trim(preg_replace('/\//', '\\', $dirname . DIRECTORY_SEPARATOR . $filename), '\\ ');
		$config = Configuration::get('routers.' . $pathname);
		//$classname = preg_replace('/\//', '\\', subject);
		//highlight_string(file_get_contents(__FILE__));
		switch ($extension) {
			case 'src':
				$res = RESTful::request('http','cgi.restfulness.laysoft.cn', '/a', '.json', 'PUT', array('post' => 1), array('ascii' => 'E:/lli/ascii.art.txt'));
				echo '<pre>';print_r($res);echo '</pre>';
				break;
			case 'json':
				/*parse_str(file_get_contents('php://input'), $_PUT);
				echo json_encode(array($_SERVER, $_PUT));*/
				$xmethod = empty($_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE']) ? false : strtoupper($_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE']);
				$method = empty($xmethod) ? strtoupper($_SERVER['REQUEST_METHOD']) : $xmethod;
				switch ($method) {
					case 'GET':
						parse_str(file_get_contents('php://input'), $_PUT);
						echo json_encode(array($_SERVER, $_PUT, $_GET, 'GET'));
						break;
					case 'POST':
						parse_str(file_get_contents('php://input'), $_PUT);
						echo json_encode(array($_SERVER, $_PUT, $_POST, 'POST', $_FILES));
						break;
					case 'PUT':
						//$_PUT = file_get_contents('php://input');
						parse_str(file_get_contents('php://input'), $_PUT);
						//print_r(http_get_request_body_stream());
						echo json_encode(array($_SERVER, $_PUT, 'PUT', $_FILES));
						break;
					case 'DELETE':
						parse_str(file_get_contents('php://input'), $_PUT);
						echo json_encode(array($_SERVER, $_PUT, 'DELETE'));
						break;
					default:
						echo "string";
						//highlight_string(Util::array2PHPContent($_SERVER));
						break;
				}
				break;
			default:
				echo "string";
				//highlight_string(Util::array2PHPContent($_SERVER));
				break;
		}
	}
	public function after() {
		
	}
	public function finish() {
		
	}
}

// PHP END
