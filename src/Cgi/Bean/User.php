<?php
namespace Lay\Cgi\Bean;

use Lay\Core\Bean;

class User extends Bean {
	//public $name = '';
	//public $value = '';
	public function properties() {
		return array(
			'id' => 0,
			'name' => ''
		);
	}
}
// PHP END