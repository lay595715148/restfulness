<?php
$env = Lay\Core\Configuration::get('env', 'test');

return array(
	'app' => array(
		'cgi' => include __DIR__ . DIRECTORY_SEPARATOR . 'cgi' . DIRECTORY_SEPARATOR . "cgi.$env.php",
		'cli' => include __DIR__ . DIRECTORY_SEPARATOR . 'cli' . DIRECTORY_SEPARATOR . "cli.$env.php",
		'web' => include __DIR__ . DIRECTORY_SEPARATOR . 'web' . DIRECTORY_SEPARATOR . "web.$env.php"
	)
);
// PHP END