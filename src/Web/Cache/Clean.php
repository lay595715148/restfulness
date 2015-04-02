<?php
namespace Lay\Web\Cache;

use Lay\Core\App;
use Lay\Core\Action;
use Lay\Autoloader;
use Lay\Core\Configuration;

use Assetic\Asset\AssetCollection;
use Assetic\Asset\FileAsset;
use Assetic\Asset\GlobAsset;

class Clean extends Action {
	public function onGet() {
		// 类加载路径缓存
		Autoloader::cleanCache();
		// 配置信息缓存
		Configuration::cleanCache();
		// 前端缓存
		self::cleanCache();
		//Action::cleanCache();
		$this->template->push('isok', true);
	}
}
// PHP END