<?php
namespace Lay\Cgi\Service;

use Lay\Core\Service;

class People extends Service {
	public function basic() {
		return 'Lay\Cgi\Model\User';
	}
	public function associates() {
		return array();
	}
}
// PHP END