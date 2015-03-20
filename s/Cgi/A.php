<?php
namespace Lay\Cgi;

use Lay\Core\Action;

class A extends Action {
	public function onCreate() {
		headers_sent() || header("Content-type: text/html; charset=utf-8");
	}
	public function onGet() {
		echo json_encode(array($_SERVER, $_GET, 'GET'));
	}
	public function onPost() {
		echo json_encode(array($_SERVER, $_POST, 'POST', $_FILES));
	}
	public function onPut() {
		global $_PUT, $_DELETE, $_PATCH, $_HEAD, $_OPTIONS;
		parse_str(file_get_contents('php://input'), $_PUT);
		$_PUT = array_merge($_POST, $_PUT);
		$_POST = array();
		//print_r(http_get_request_body_stream());
		echo json_encode(array($_SERVER, $_PUT, 'PUT', $_FILES));
	}
	public function onDelete() {
		global $_PUT, $_DELETE, $_PATCH, $_HEAD, $_OPTIONS;
		parse_str(file_get_contents('php://input'), $_DELETE);
		$_DELETE = array_merge($_POST, $_DELETE);
		$_POST = array();
		echo json_encode(array($_SERVER, $_DELETE, 'DELETE', $_FILES));
	}
	public function onHead() {
		global $_PUT, $_DELETE, $_PATCH, $_HEAD, $_OPTIONS;
		parse_str(file_get_contents('php://input'), $_HEAD);
		$_HEAD = array_merge($_POST, $_HEAD);
		$_POST = array();
		echo json_encode(array($_SERVER, $_HEAD, 'HEAD', $_FILES));
	}
	public function onPatch() {
		global $_PUT, $_DELETE, $_PATCH, $_HEAD, $_OPTIONS;
		parse_str(file_get_contents('php://input'), $_PATCH);
		$_PATCH = array_merge($_POST, $_PATCH);
		$_POST = array();
		echo json_encode(array($_SERVER, $_PATCH, 'PATCH', $_FILES));
	}
	public function onOptions() {
		global $_PUT, $_DELETE, $_PATCH, $_HEAD, $_OPTIONS;
		parse_str(file_get_contents('php://input'), $_OPTIONS);
		$_OPTIONS = array_merge($_POST, $_OPTIONS);
		$_POST = array();
		echo json_encode(array($_SERVER, $_OPTIONS, 'OPTIONS', $_FILES));
	}
}