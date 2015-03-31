<?php
return array(
	'routers' => array(
		'/ass.[src|xml|json:format]?' => array(
			'class' => '\Lay\Cgi\Ass'
		),
		'/a.[src|xml|json:format]?' => array(
			'class' => '\Lay\Cgi\A'
		)
	)
);
// PHP END