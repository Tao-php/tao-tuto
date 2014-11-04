# Mise en place

Dans cette première partie, nous allons mettre en place les fondations de notre projet :
* télécharger les sources
* créer l’arborescence des dossiers
* créer les principaux fichiers
* initialiser l’autoload PHP

## Téléchargement des sources

Tao utilise [Composer](https://getcomposer.org/) pour définir les dépendances dont il a besoin pour fonctionner. Les dépendances sont des bibliothèques logiciel disponibles sur [Packagist](https://packagist.org/).

![](https://raw.githubusercontent.com/forxer/tao-tuto/master/book/assets/text-html.png) [Documentation de Composer](https://getcomposer.org/doc)

La première chose à faire, est évidement de mettre en place Tao dans notre projet. Pour se faire vous allez créer un fichier `composer.json` à la racine du projet :

```json
{
	"require": {
		 "forxer/tao": "0.8"
	 }
}
```

Ce fichier indique que le nouveau projet exige pour fonctionner la bibliothèque `forxer/tao` en version 0.8
Autrement dit "Tao est une dépendance de notre projet" ; ou sa réciproque : "notre projet dépend de Tao".

Vous pouvez indiquer beaucoup d’autres choses à propos de votre projet dans le fichier `composer.json` comme son nom, son auteur, etc.

![](https://raw.githubusercontent.com/forxer/tao-tuto/master/book/assets/text-html.png) [Documentation du fichier `composer.json`](https://getcomposer.org/doc/04-schema.md)

Ensuite il faut lancer la ligne de commande suivante pour installer les dépendances :

```dos
composer install
```

Ceci a pour effet de créer un répertoire `/vendor` dans lequel sont téléchargés les sources de Tao ainsi que celles de ses dépendances.

Vous pouvez regarder dans le répertoire `/vendor` pour voir tous ce qui a été installé par cette simple commande.

![](https://raw.githubusercontent.com/forxer/tao-tuto/master/book/assets/dialog-information.png) Vous noterez notamment la présence d’un fichier `autoload.php` que nous utiliserons par la suite.

Aussi, un fichier `composer.lock` est généré à la racine ; ce fichier ne nous intéresse pas particulièrement pour ce tutoriel, je n’y ferais donc plus référence.

![](https://raw.githubusercontent.com/forxer/tao-tuto/master/book/assets/text-html.png) [Documentation du fichier `composer.lock`](https://getcomposer.org/doc/01-basic-usage.md#composer-lock-the-lock-file)

## Arborescence

Nous allons maintenant définir une arborescence pour notre projet.

### A la racine

A la racine il y a déjà un répertoire `/vendor` nous allons créer deux autres dossiers :

* `/Application` : accueillera tous les fichiers PHP de notre application
* `/web` : accueillera tous les fichiers accessibles depuis le web, les "assets" (images, css, etc.) mais aussi et surtout le front-controller `app.php`

![](https://raw.githubusercontent.com/forxer/tao-tuto/master/book/assets/emblem-important.png) Comprenez bien que le répertoire `/web` est le répertoire accessible depuis l’extérieur par les internautes ; si vous avez un nom de domaine alors ce dernier pointe vers ce répertoire `/web` ; chez certains hébergeur ce répertoire est `/www`

A ce stade nous avons donc l’arborescence suivante :

    /projet
        /Application
        /vendor
            ...
            autoload.php
        /web
        composer.json

### Dans l’application

Ensuite, nous allons créer l’arborescence du répertoire de l’application de la façon suivante :

    /Application
        /Config
        /Controllers
        /Storage
        /Views
        Application.php

Nous venons donc de créer les emplacements pour la configuration, les contrôleurs, les vues et un espace de stockage (pour le cache, les logs, etc..) et enfin, un fichier PHP pour l’application.

Dans la foulée, nous allons créer deux autres répertoires dans `/Application/Storage` : `/Cache` et `/Logs` Répertoires auxquels _vous devez_ donner à PHP la permission d’écrire des fichiers dedans.

A ce stade nous avons donc l’arborescence suivante :

    /projet
        /Application
            /Config
            /Controllers
            /Storage
                /Cache
                /Logs
            /Views
            Application.php
        /vendor
            ...
            autoload.php
        /web
        composer.json

Maintenant, nous allons créer le premier contenu du fichier application `/Application/Application.php` :

```php
<?php
namespace Application;

use Tao\Application as TaoApplication;

class Application extends TaoApplication
{
	public function __construct($loader, array $config = [], array $classesMap = [])
	{
		parent::__construct($loader, $config, __DIR__, $classesMap);
	}
}
```

Ce fichier ne fait pas grand chose pour le moment. Il se contente essentiellement d’étendre la classe `Tao\Application` et d’appeler son constructeur en lui passant quelques paramètres.

![](https://raw.githubusercontent.com/forxer/tao-tuto/master/book/assets/dialog-information.png) Vous noterez l’espace de nom (namespace) choisi : `Application` C’est l’espace de nom que nous utiliserons partout pour la suite de ce tutoriel.

Cela pourrait être tout autre chose. En fait, ce que vous voulez qui n’existe pas déjà dans notre projet ; par exemple `Tuto` ou `Toto`, mais pas `Tao` car c’est déjà utilisé.

![](https://raw.githubusercontent.com/forxer/tao-tuto/master/book/assets/emblem-important.png) Vous _devez_ définir un espace de nom pour votre projet afin d’éviter d’éventuels conflits de nommage et pour garder une application organisée. Aussi, l’espace de nom a son importance pour l’autoload.

![](https://raw.githubusercontent.com/forxer/tao-tuto/master/book/assets/text-html.png) [Documentation PHP sur les espaces de noms](http://php.net/manual/fr/language.namespaces.php)

### Côté public

Notre application utilisera des images, des CSS, du Javascript etc. ; nous allons donc créer dans le répertoire `/web` l’arborescence suivante :

    /web
        /Assets
            /css
            /img
            /js

Nous venons donc de créer les emplacements pour les fichiers CSS, images et JS.

Enfin, nous allons mettre en place notre [Front Controller](http://en.wikipedia.org/wiki/Front_Controller_pattern) dans `/web/app.php` qui contiendra le code PHP suivant :

```php
<?php

# Chargement de l'autoload de composer
$loader = require __DIR__ . '/../vendor/autoload.php';

# Initialisation de l'application
$app = new Application\Application($loader);

# Exécution de l'application
$app->run();
```

Le contenu de ce fichier est assez explicite :
- on charge l’autoload de composer
- on initialise l’application
- on exécute l’application

## Autoload

Nous allons utiliser l’autoload fournis par composer.

Comme nous avons définit l’espace de nom `Application` pour le répertoire `/Application` il faut indiquer à composer comment construire son autoloader avec ces informations. Pour cela nous allons modifier le fichier `composer.json` de la façon suivantes :

```json
{
	"require": {
		"forxer/tao": "0.8"
	},

	"autoload" : {
		"psr-4" : {
			"Application\\" : "Application"
		}
	}
}
```

Nous avons donc indiqué que l’espace de nom `Application\` sera physiquement présent dans le répertoire "/Application" (qui se trouve au même niveau que `/vendor`)

Si nous avions définit l’espace de nom `Toto\` dans le répertoire `/src/Toto` nous aurions mis dans notre `composer.json` ceci :

```json
	"autoload" : {
		"psr-4" : {
			"Toto\\" : "src/Toto"
		}
	}
```

Composer fournis plusieurs types d’autoload : PSR-0, PSR-4, classmap et fichier. Nous utilisons ici PSR-4 qui est l’autoload recommandé et qui offre l’utilisation la plus simple et intuitive.

Ainsi une classe `Application\Foo\Bar` devra se trouver dans le fichier `/Application/Foo/Bar.php`

![](https://raw.githubusercontent.com/forxer/tao-tuto/master/book/assets/text-html.png) [Documentation sur l’autoload de composer](https://getcomposer.org/doc/04-schema.md#autoload).

Maintenant, il faut reconstruire l’autoload de composer soit en relançant la commande d’installation soit en utilisant la commande dédiée :

```dos
composer dump-autoload
```

## Conclusion

Nous avons définit une arborescence rigoureuse pour que chaque élément du projet soit rangé à un endroit logique par rapport à sa fonction.

![](https://raw.githubusercontent.com/forxer/tao-tuto/master/book/assets/dialog-information.png) En ce qui concerne l’arborescence, vous pouvez la modifier, rien ne vous obliges à suivre impérativement celle que nous vous proposons ici. Mais c’est cette organisation que nous suivrons tout au long de ce tutoriel. Nous avons déjà réalisé des applications avec une arborescence tout à fait différente. Par exemple "tout à la racine" et cela fonctionne parfaitement (moyennant quelques ajustements de chemins, cela va de soit). Mais nous vous recommandons de garder ce modèle, au moins dans un premier temps. D’autant qu’il propose une organisation logique des répertoires et des fichiers suivant leurs fonctions respectives.

Nous avons mis en place le nécessaire pour utiliser Tao et afficher le fameux "Hello World !" notre prochaine étape.
