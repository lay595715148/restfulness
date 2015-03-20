<?php
namespace Lay\Cgi;

use Lay\Core\Action;
use Lay\Util\RESTful;
use Lay\Util\Util;

use __;
use Respect\Validation\Validator as v;

use Overtrue\Pinyin\Pinyin;

class Ass extends Action {
	public function onCreate() {
		headers_sent() || header("Content-type: text/html; charset=utf-8");
	}
	public function onGet() {
		if($this->request->getExtension() == 'src') {
			highlight_string(file_get_contents(__FILE__));
			$ret = v::not(v::int())->validate('DSDSD');;echo "<br>$ret<br>";
			__::each(array(1, 2, 3), function($num) { echo $num . ','; }); // 1,2,3,
		} else {
			$string = Pinyin::parse('第二个参数随意设置', array('accent' => false));
			//print_r($string);
			//$u = new User();
			$rest = new RESTful();
			//$cgi = CgiIndex::getInstance();
			//$cli = CliIndex::getInstance();
			$res = $rest->send('http','cgi.restfulness.laysoft.cn', '/a', 'json', 'PUT', array('post' => 1), array('ascii' => 'E:/lli/ascii.art.txt'));//
			headers_sent() || header("Content-type: text/html; charset=utf-8");
			echo '<pre>';print_r(array($string, $res['body']));echo '</pre>';
		}
		//break;
	}
	public function onPost() {
		
	}
	public function onPut() {
		
	}
	public function onDelete() {
		
	}
}