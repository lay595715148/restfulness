<?php
namespace Lay\Cgi\Kernel;

class App extends \Lay\Core\App {
    /**
     * App初始化
     * @return void
     */
    public function initialize() {
        $this->routers = self::get('app.cgi.routers', array());
    }
}
// PHP END