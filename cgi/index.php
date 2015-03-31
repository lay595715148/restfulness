<?php
require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'Autoloader.php';
Lay\Autoloader::register();
Lay\Cgi\Kernel\App::start();

// PHP END
