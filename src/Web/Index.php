<?php
namespace Lay\Web;

use Lay\Core\App;
use Lay\Core\Action;

use Assetic\Asset\AssetCollection;
use Assetic\Asset\FileAsset;
use Assetic\Asset\GlobAsset;

class Index extends Action {
	public function onGet() {
		$js = new AssetCollection(array(
		    new FileAsset(App::$_docpath . '/lib/html5shiv.js'),
		    new GlobAsset(App::$_docpath . '/js/*')
		));
		$this->template->push('js', $js->dump());
		$this->template->file('index.php');
		//$this->template->display();
	}
	public function onPost() {
		
	}
	public function onPut() {
		
	}
	public function onDelete() {
		
	}
}
// PHP END