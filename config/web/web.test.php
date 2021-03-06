<?php
return array(
	'routers' => array(
		'/' => array(
			'class' => '\Lay\Web\Index'
		),
		'/index.[html|htm:format]?' => array(
			'class' => '\Lay\Web\Index'
		),
		'/user.[html|htm:format]?' => array(
			'class' => '\Lay\Web\User'
		),
		'/cache/jquery.[js:format]?' => array(
			'class' => '\Lay\Web\Cache\Jquery'
		),
		'/cache/main.[js:format]?' => array(
			'class' => '\Lay\Web\Cache\MainJs'
		),
		'/cache/main.[css:format]?' => array(
			'class' => '\Lay\Web\Cache\MainCss'
		),
		'/cache/clean' => array(
			'class' => '\Lay\Web\Cache\Clean'
		)
	)
);
// PHP END