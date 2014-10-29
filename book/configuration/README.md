# Configuration

Nous avons vu dans la précédente partie qu’il est possible de passer des paramètres de configuration au constructeur de l’application.

Dans cette partie nous allons voir comment améliorer le système de chargement de la configuration dans notre projet.

## Configuration de base

Voici ce que nous avons précédemment modifié en terme de configuration, en l’occurrence le paramètre `debug` à `true` :

```php
//...

# Initialisation de l’application
$app = new Application\Application($loader, ['debug' => true]);

//...
```

En fait, ici nous avons écrasé le paramètre de configuration par défaut. En effet, Tao définit un ensemble de paramètres de configurations par défaut qui lui permettent de fonctionner correctement.

![](https://raw.githubusercontent.com/forxer/tao-tuto/master/book/assets/emblem-important.png) Ces paramètres de base se trouvent dans le fichier `vendor/forxer/tao/src/Tao/Configuration.php`.

Ouvrez donc ce fichier `vendor/forxer/tao/src/Tao/Configuration.php` pour voir quels sont les paramètres de configuration de base dans Tao, paramètres que vous pouvez modifier dans votre propre configuration.

![](https://raw.githubusercontent.com/forxer/tao-tuto/master/book/assets/emblem-important.png) Ne modifiez jamais ce fichier de configuration par défaut, à chaque mise à jour vous perdriez vos modifications, utilisez les techniques décrites ci-dessous.

![](https://raw.githubusercontent.com/forxer/tao-tuto/master/book/assets/dialog-information.png) Évidement, vous pouvez définir vos propres paramètres de configuration en plus de ceux définit par défaut.

## Un fichier de configuration

Au lieu de passer les paramètres de configuration directement au constructeur de l’application dans un tableau, il serait intéressant de les placer dans un fichier.

* cela permettra de sécuriser un peu plus la configuration en mettant ce fichier en dehors de l’espace web
* nous gagnerons en souplesse en le mettant dans un fichier dédié au lieu de le mettre au milieu du front-controller
* il sera plus facile de passer d’une configuration à une autre en chargeant tel ou tel autre fichier de configuration

Pour commencer nous allons créer le fichier `/Application/Config/config.php` :

```php
<?php

return [
	# The application identifier (used internally)
	'app_id' => 'tuto',

	# Enable/disable debug mode
	'debug' => true
];
```

Ce fichier se contente de retourner le tableau de configuration avec nos valeurs personnalisées selon nos besoins.

Maintenant, nous allons modifier le front-controller pour que ce fichier soit chargé à la place du tableau passé directement au constructeur.

Fichier `/web/app.php`

```php
<?php

# Chargement de l'autoload de composer
$loader = require __DIR__ . '/../vendor/autoload.php';

# Chargement de la configuration
$config = require __DIR__ . '/../Application/Config/config.php';

# Initialisation de l'application
$app = new Application\Application($loader, $config);

# Exécution de l'application
$app->run();
```

## Plusieurs fichiers de configuration

Maintenant, il serait souhaitable d’avoir plusieurs fichiers de configuration de façon à avoir différents profil de configuration selon l’environnement.

Renommez `/Application/Config/config.php` en `/Application/Config/prod.php` et modifiez le ainsi :
```php
<?php

return [
	# The application identifier (used internally)
	'app_id' => 'tuto',

	# Enable/disable debug mode
	'debug' => false
];
```

On passe le paramètre `debug` à `false` en production.

Maintenant on créer un fichier `/Application/Config/dev.php` qui contient :

```php
<?php

$devConfig = [

	# Enable/disable debug mode
	'debug' => true
];

$config = require __DIR__ . '/prod.php';

return $devConfig + $config;
```

Ce fichier définit un nouveau tableau de paramètres de configuration et le fusionne aux paramètre de l’environnement de production. Ainsi dans l’environnement de développement, on passe la valeur paramètre `debug` à `true`.

Reste à modifier le fichier front-controller `web/app.php`

```php
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
```
Ici pour passer d’un environnement à un autre il suffit de commenter/dé-commenter l’une et l’autre ligne de chargement de la configuration.

On peut imaginer d’autres façons de passer d’un environnement à un autre de façon plus ou moins automatique, par exemple un test sur les variables d’environnement, un fichier app_dev.php, etc.

## Conclusion

Nous avons vu comment la configuration de l’application était chargée et comment on pouvaient gagner en flexibilité en créant plusieurs fichiers de configuration.
