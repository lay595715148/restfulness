<?php
namespace Lay\Web\Cache;

use Lay\Core\App;
use Lay\Core\Action;

use Assetic\Asset\AssetCollection;
use Assetic\Asset\FileAsset;
use Assetic\Asset\GlobAsset;

class Jquery extends Action {
	private $javascript;
	public function onGet() {
		$asset = new AssetCollection(array(
		    new FileAsset(App::$_docpath . '/lib/jquery/jquery-1.10.2.js'),
		    new GlobAsset(App::$_docpath . '/lib/jquery/*')
		));
		$this->javascript = $asset->dump();
		$this->template->push($this->javascript);
		App::$_event->listen(App::$_app, App::E_FINISH, array($this, 'cache'));
	}
	public function cache() {
		if($this->javascript) {
			$cachefile = App::$_docpath . '/cache/jquery.js';
			file_put_contents($cachefile, $this->javascript);
		}
	}
}
// PHP END