<?php
namespace Lay\Web\Cache;

use Lay\Core\App;
use Lay\Core\Action;

use Assetic\Asset\AssetCollection;
use Assetic\Asset\FileAsset;
use Assetic\Asset\GlobAsset;

class MainJs extends Action {
	private $javascript;
	public function onGet() {
		App::$_event->listen(App::$_app, App::E_FINISH, array($this, 'cache'));

		$asset = new AssetCollection(array(
		    new GlobAsset(App::$_docpath . '/js/*')
		));
		$this->javascript = $asset->dump();
		$this->template->push($this->javascript);
	}
	public function cache() {
		if($this->javascript) {
			$cachefile = App::$_docpath . '/cache/main.js';
			file_put_contents($cachefile, $this->javascript);
		}
	}
}
// PHP END