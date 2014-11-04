# Créer un service

Nous avons vu dans la partie précédente les concepts d'*injection de dépendances* et de *conteneur d'injection de dépendances* ainsi que leur implémentation dans Tao grâce à Pimple.

Tous cela était bien théorique, nous allons maintenant passer à la pratique par un simple exemple.

## Ajouter et utiliser une nouvelle dépendance

Imaginons que, pour une raison ou une autre, nous ayons besoin de lister les fichiers CSS présents dans notre projet.

Pour trouver ces fichiers nous voudrions utiliser la bibliothèque [symfony/finder](https://packagist.org/packages/symfony/finder).

Dans un premier temps nous allons procéder de la même façon que lorsque nous avons précédement ajouté le *validator*.

Nous allons donc ajouter une dépendance au fichier `composer.json` :

```json
...
	"require" : {
		"forxer/tao" : "0.8",
		"respect/validation" : "~0.6",
		"symfony/finder" : "~2.5"
	},
...
```

Lancer la commande :

```
composer update
```

Maintenant nous pourrions utiliser le finder de cette façon :

```php
<?php
use Symfony\Component\Finder\Finder;

$cssFiles = new Finder();
$cssFiles->files()->in(__DIR__)->name('*.css');
```

Voilà, cela fonctionne parfaitement.

![](https://raw.githubusercontent.com/forxer/tao-tuto/master/book/assets/text-html.png) [Documentation du composant Finder](http://symfony.com/fr/doc/current/components/finder.html)

## "Serveur ! Un *finder* s'il vous plais."

Notre exemple précédent est simple et efficace. Mais vous pourriez avoir besoin du finder dans bien des endroits de votre projet. Aussi, l'initialisation du finder est simple, mais pour d'autres objets cela pourrait prendre bien plus de ligne de code, qu'il faudrait recopier à chaque fois qu'on en as besoin.

Mais attendez. Nous avons vu l'injection de dépendance et la création de fournisseur de service dans la partie précédente. Alors allons-y, créons un fournisseur de service.

Pour cela nous allons ajouter un répertoire "Provider" à notre application et y créer un fichier `/Application/Provider/FinderServiceProvider.php` :

```php
<?php
namespace Application\Provider;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Symfony\Component\Finder\Finder;

class FinderServiceProvider implements ServiceProviderInterface
{
	public function register(Container $app)
	{
		$app['finder'] = function() {
			return new Finder();
		};
	}
}
```

Ensuite, on enregistre ce service dans le constructeur de l'application `/Application/Application.php` :

```php
//...

use Application\Provider\FinderServiceProvider;

//...

	public function __construct($loader, array $config = [], array $classesMap = [])
	{
		parent::__construct($loader, $config, __DIR__, $classesMap);

		$this->register(new FinderServiceProvider());

//...
```

Et voilà, maintenant nous pouvons appeller très facilement le finder dans notre application `$app['finder]` ; l'exemple devient :

```php
<?php

$cssFiles = $app['finder']->files()->in(__DIR__)->name('*.css');

```

Néanmoins il demeure un problème, si je fait de nouveaux appel à `$app['finder]`, de par l'implémentation de Pimple je retrouverais la même instance du finder. Ce n'est pas ce que je veux pour le Finder. Pour le finder je veux une nouvelle instance à chaque appel de `$app['finder]`. Mais encore une fois nous en avons parlé, Pimple fournit une méthode `factory()` pour nous aider à cela. Modifions donc le fichier `FinderServiceProvider.php` :

```php
<?php
namespace Application\Provider;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Symfony\Component\Finder\Finder;

class FinderServiceProvider implements ServiceProviderInterface
{
	public function register(Container $app)
	{
		$app['finder'] = $app->factory(function() {
			return new Finder();
		});
	}
}
```

Et voilà le tour est joué. Pour aller au bout de l'injection de dépendance il faut rendre paramètrable le nom de la classe Finder. Par exemple pour étendre cette dernière ou faire appel à une autre implémentation de Finder sans toucher au service provider.

## Le mapping de classes

Nous avons vu que Tao fonctionne avec un fichier de configuration par défaut, le fichier  `vendor/forxer/tao/src/Tao/Configuration.php`

Il existe un second fichier de "configuration" par défaut, celui du *mapping de classe*.

Ce fichier dit, par exemple, que la classe 'logger' dans l'application sera 'Monolog\Logger'

Aussi, si vous retournez voir votre fichier `/Application/Application.php`, vous remarquerez un troisième argument au construteur : `$classesMap`.

En effet, comme nous avons personnalisé la configuration en passant un tableau au deuxième argument du constructeur, ici nous pouvons passer au troisième argument du constructeur un tableau pour modifier les noms des classes à instancier.

Pour notre exemple de service nous voulons ajouter une ligne au "classesMap" de façon à pouvoir changer de classe de Finder facilement.

Pour commencer nous ajoutons simplement cette nouvelle valeur à l'initialisation de l'application dans `/web/app.php` :

```php
//...
# Initialisation de l'application
$app = new Application\Application($loader, $config, [
    'finder' => 'Symfony\Component\Finder\Finder'
]);
//...
```

Nous ajoutons directement le tableau ici, si le projet prend de l'envergure il faudrait créer un fichier dédié comme nous l'avons fait pour la configuration.

Ensuite nous modifions notre déclaration de service `/Application/Provider/FinderServiceProvider.php` :

```php
<?php
namespace Application\Provider;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

class FinderServiceProvider implements ServiceProviderInterface
{
	public function register(Container $app)
	{
		$app['finder'] = $app->factory(function() {
			return new $app['class']['finder']();
		});
	}
}
```

Voilà, maintenant vous pouvez très facilement changer de classe Finder.

![](https://raw.githubusercontent.com/forxer/tao-tuto/master/book/assets/dialog-information.png) En fait, inutile d'implémenter dans votre application ce service FinderServiceProvider, nous l'avons trouvé tellement pratique qu'il a été inclus dans la version 0.8.1 de Tao.


## Conclusion

Vous pouvez maintenant créer vos propres services pour gagner en agilité dans votre projet.

Aussi avec le mécanisme de mapping de classes et l'injection de dépendances vous pouvez  modifier et étendre le comportement de l'application simplement par de la configuration et même modifier le comportement de Tao lui-même si besoin.


