<?php
namespace Lay\Cgi;

use Lay\Core\Action;
use Lay\Util\RESTful;
use Lay\Util\Utility;
use Lay\Util\Logger;

use __;
use Respect\Validation\Validator as v;

use Overtrue\Pinyin\Pinyin;

use Lay\Cgi\Bean\User;

class Ass extends Action {
	public function onCreate() {
		headers_sent() || header("Content-type: text/html; charset=utf-8");
	}
	public function onGet() {
		$model = \Lay\Cgi\Model\User::getInstance();
		$model->name = 'Lay Li';
		$service = \Lay\Cgi\Service\People::getInstance();
		$eq = $service->get(1);
		//$eq = \Lay\Cgi\Model\User::getInstance() === $model ? 'true' : 'false';
		/*if($this->request->getExtension() == 'src') {
			highlight_string(file_get_contents(__FILE__));
			$ret = v::not(v::int())->validate('DSDSD');;echo "<br>$ret<br>";
			__::each(array(1, 2, 3), function($num) { echo $num . ','; }); // 1,2,3,
		} else {*/
			//__::each(array(1, 2, 3), function($num) { echo $num . ','; }); // 1,2,3,
			$pinyin = Pinyin::parse('第二个参数随意设置', array('accent' => false));
			//print_r($string);
			//$u = new User();
			$rest = RESTful::getInstance();
			//$cgi = CgiIndex::getInstance();
			//$cli = CliIndex::getInstance();
			$res = $rest->send('http','cgi.restfulness.laysoft.cn', '/a', 'json', 'POST', array('post' => 1), array('ascii' => 'E:/lli/ascii.art.txt'));//
			//headers_sent() || header("Content-type: text/html; charset=utf-8");
			//echo '<pre>';print_r(array($string, $res['body']));echo '</pre>';
			$bean = new User();
			//$model = \Lay\Cgi\Model\User::getInstance();
			$bean->name = array('first' => 'Lay', 'last' => 'Li' , 'self' => new User());
			$bean->nick = 'nick';
			$this->template->push(array('parse' => $service, 'pinyin' => $pinyin, 'body' => $res['body']));
			//$this->template->header('Server: restfulness');
			$this->template->file('ass.php');
			//echo '<pre>';print_r($this->template);echo '</pre>';
			//$this->template->display();
		//}
		//break;
	}
}