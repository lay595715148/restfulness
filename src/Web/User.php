<?php
namespace Lay\Web;

use Lay\Core\Action;

class User extends Action {
	public function onGet() {
		$this->template->file('user.php');
		$this->template->display();
	}
	public function onPost() {
		
	}
	public function onPut() {
		
	}
	public function onDelete() {
		
	}
}
// PHP END