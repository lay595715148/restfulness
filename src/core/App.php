<?php
namespace core;

use Autoloader;
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
		$pathname = preg_replace('/^(.*)(\?)(.*)$/', '$1', $_SERVER['REQUEST_URI']);
		$pathinfo = pathinfo($pathname);
		extract($pathinfo);
		$extension = $pathinfo['extension'];
		$classname = trim(preg_replace('/\//', '\\', $dirname . DIRECTORY_SEPARATOR . $filename), '\\ ');
		$config = Configuration::get('routers.' . $pathname);
		//$classname = preg_replace('/\//', '\\', subject);
		//highlight_string(file_get_contents(__FILE__));
		highlight_string(Util::array2PHPContent($_SERVER));
		RESTful::put('/a.json');
	}
	public function after() {
		
	}
	public function finish() {
		
	}
	public function __destruct() {
    	Autoloader::updateCache();
	}
}

// PHP END
