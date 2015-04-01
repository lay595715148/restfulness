<?php
namespace Lay\Cgi;

use Lay\Core\Action;

class A extends Action {
	public function onCreate() {
		//headers_sent() || header("Content-type: text/html; charset=utf-8");
	}
	public function onGet() {
		$this->template->push(array('_SERVER' => $_SERVER, '_GET' => $_GET, 'GET' => 'GET'));
	}
	public function onPost() {
		$this->template->push(array('_SERVER' => $_SERVER, '_POST' => $_POST, 'POST' => 'POST', '_FILES' => $_FILES));
	}
	public function onPut() {
		global $_PUT, $_DELETE, $_PATCH, $_HEAD, $_OPTIONS;
		parse_str(file_get_contents('php://input'), $_PUT);
		$_PUT = array_merge($_POST, $_PUT);
		$_POST = array();
		//print_r(http_get_request_body_stream());
		$this->template->push(array('_SERVER' => $_SERVER, '_PUT' => $_PUT, 'PUT' => 'PUT', '_FILES' => $_FILES));
		//$this->template->json();
	}
	public function onDelete() {
		global $_PUT, $_DELETE, $_PATCH, $_HEAD, $_OPTIONS;
		parse_str(file_get_contents('php://input'), $_DELETE);
		$_DELETE = array_merge($_POST, $_DELETE);
		$_POST = array();
		$this->template->push(array('_SERVER' => $_SERVER, '_DELETE' => $_DELETE, 'DELETE' => 'DELETE', '_FILES' => $_FILES));
	}
	public function onHead() {
		global $_PUT, $_DELETE, $_PATCH, $_HEAD, $_OPTIONS;
		parse_str(file_get_contents('php://input'), $_HEAD);
		$_HEAD = array_merge($_POST, $_HEAD);
		$_POST = array();
		$this->template->push(array('_SERVER' => $_SERVER, '_HEAD' => $_HEAD, 'HEAD' => 'HEAD', '_FILES' => $_FILES));
	}
	public function onPatch() {
		global $_PUT, $_DELETE, $_PATCH, $_HEAD, $_OPTIONS;
		parse_str(file_get_contents('php://input'), $_PATCH);
		$_PATCH = array_merge($_POST, $_PATCH);
		$_POST = array();
		$this->template->push(array('_SERVER' => $_SERVER, '_PATCH' => $_PATCH, 'PATCH' => 'PATCH', '_FILES' => $_FILES));
	}
	public function onOptions() {
		global $_PUT, $_DELETE, $_PATCH, $_HEAD, $_OPTIONS;
		parse_str(file_get_contents('php://input'), $_OPTIONS);
		$_OPTIONS = array_merge($_POST, $_OPTIONS);
		$_POST = array();
		$this->template->push(array('_SERVER' => $_SERVER, '_OPTIONS' => $_OPTIONS, 'OPTIONS' => 'OPTIONS', '_FILES' => $_FILES));
	}
}