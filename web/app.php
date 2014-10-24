<?php

# Chargement de l'autoload de composer
$loader = require __DIR__ . '/../vendor/autoload.php';

# Chargement de la configuration
//$config = require __DIR__ . '/../Application/Config/prod.php';
$config = require __DIR__ . '/../Application/Config/dev.php';

# Initialisation de l'application
$app = new Application\Application($loader, $config);

# Exécution de l'application
$app->run();
