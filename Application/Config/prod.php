<?php

return [
    # The application identifier (used internally)
    'app_id' => 'tuto',

    # Enable/disable debug mode
    'debug' => false,

    'database.connection' => [
        'driver' => 'pdo_mysql',
        'host' => 'localhost',
        'dbname' => 'tutotao',
        'user' => 'root',
        'password' => '',
        'charset' => 'utf8'
    ],

    'routing.controllers_namespace' => 'Application\Controllers'

];
