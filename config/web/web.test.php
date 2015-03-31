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
		)
	)
);
// PHP END