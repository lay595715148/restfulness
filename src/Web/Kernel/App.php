<?php
namespace Lay\Web\Kernel;

class App extends \Lay\Core\App {
    /**
     * App初始化
     * @return void
     */
    public function initialize() {
        $this->routers = self::get('app.web.routers', array());
    }
}
// PHP END