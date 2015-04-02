<?php
namespace Lay\Web\Cache;

use Lay\Core\App;
use Lay\Core\Action;

use Assetic\Asset\AssetCollection;
use Assetic\Asset\FileAsset;
use Assetic\Asset\GlobAsset;

class MainCss extends Action {
	private $css;
	public function onGet() {
		App::$_event->listen(App::$_app, App::E_FINISH, array($this, 'cache'));

		$asset = new AssetCollection(array(
		    new GlobAsset(App::$_docpath . '/css/*')
		));
		$this->css = $asset->dump();
		$this->template->push($this->css);
	}
	public function cache() {
		if($this->css) {
			$cachefile = App::$_docpath . '/cache/main.css';
			file_put_contents($cachefile, $this->css);
		}
	}
}
// PHP END