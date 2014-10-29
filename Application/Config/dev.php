<?php

$devConfig = [

	# Enable/disable debug mode
	'debug' => true
];

$config = require __DIR__ . '/prod.php';

return $devConfig + $config;
