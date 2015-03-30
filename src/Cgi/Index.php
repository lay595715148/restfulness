<?php
namespace Lay\Cgi;

use Lay\Core\Action;

class Index extends Action {
	public function onGet() {
		$this->template->file('404.php');
		$this->template->display();
	}
	public function onPost() {
		
	}
	public function onPut() {
		
	}
	public function onDelete() {
		
	}
}