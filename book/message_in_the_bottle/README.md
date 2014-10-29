# Message in the bottle

Une application doit pouvoir communiquer des messages à l’utilisateur final. En tant que développeur il vous faut un outil pour pouvoir réaliser cela.

Cette partie a pour but de vous montrer comment préparer et afficher des messages à destination de l’utilisateur.

## Des piles de messages

Au cours de l’exécution de votre application vous aurez à communiquer un ou plusieurs messages. Ceux-ci devront êtres enregistrés dans une "pile de messages".

Vous avez à votre disposition 3 sortes de piles de messages :

* `$app['instantMessages']` les messages instantanés : ces messages sont affichés lors de la requête en cours, ou perdu s’ils ne sont pas affichés.
* `$app['flashMessages']` les messages flash : ces messages ne seront disponibles que durant l’affichage de la prochaine page. Ces messages vont expirer de manière automatique suivant s’ils ont été récupérés ou non.
* `$app['persistentMessages']` les messages persistants : ces messages vont rester dans la session jusqu’à ce qu'ils soient explicitement récupérés ou supprimés.

![](https://raw.githubusercontent.com/forxer/tao-tuto/master/book/assets/emblem-important.png) Les messages flash et persistants sont stockés en session, il faut donc explicitement démarrer les sessions PHP à l'aide du service dédié.

Généralement on fait ça dans le constructeur de l'application `/Application/Application.php` :

```php
<?php
namespace Application;

use Tao\Application as TaoApplication;

class Application extends TaoApplication
{
	public function __construct($loader, array $config = [], array $classMap = [])
	{
		parent::__construct($loader, $config, __DIR__, $classMap);

		$this['session']->start();
	}
}
```

## Empiler les messages

Pour chaque piles de messages vous avez accès à la même interface :

* `info($message)` : ajoute un message de type "info" ;
* `success($message)` : ajoute un message de type "success" ;
* `warning($message)` : ajoute un message de type "warning" ;
* `error($message)` : ajoute un message de type "error" ;

Par exemples :

```php
//...
$app['instantMessages']->info('une information sur la page en cours');
$app['instantMessages']->error('une erreur est survenue');
//...
$app['flashMessages']->info('une information sur la page suivante');
$app['flashMessages']->success('félicitations');
//...
$app['persistentMessages']->info('une information qui sera affichée quoi qu’il arrive');
$app['persistentMessages']->warning('attention');
//...
```

Dans la plupart des cas vous utiliserez ces piles de messages dans les contrôleurs, comme on accède différemment à l’application depuis les contrôleurs alors un message sera enregistré de cette façon :

```php
//...
$this->app['instantMessages']->info('une information sur la page en cours');
//...
```

Dans les classes des contrôleurs on accèdent à l’instance de l’application via `$this->app`.

## Afficher les messages

Enregistrer des messages c’est bien, encore faut-il les afficher. Pour nous aider, pour chaque type de message il y a des méthodes has*() et get*()

Par exemple :

```php
if ($app['instantMessages']->hasInfo())
{
	foreach ($app['instantMessages']->getInfo() as $message) {
		echo $message;
	}
}
```

Comme il y a 3 sortes de piles de messages et 4 types pour chacune d'elles, pour raccourcir le code dans les templates, on as un service `$app['messages']` qui regroupe les trois sortes de messages en vue de les afficher.

Nous allons ajouter dans le fichier `/Application/Views/Layout.php` le code suivant là où nous souhaitons afficher les messages :

```php
<?php echo $view->render('Common/messages') ?>
```

Et créer le fichier `/Application/Views/Common/Messages.php` correspondant :

```php
	<?php # affichage des éventuels messages d'erreurs
	if ($app['messages']->hasError()) : ?>
		<ul class="messages error">
			<?php foreach ($app['messages']->getError() as $message) : ?>
			<li><?php echo $message ?></li>
			<?php endforeach; ?>
		</ul>
	<?php endif; ?>

	<?php # affichage des éventuels messages d'avertissements
	if ($app['messages']->hasWarning()) : ?>
		<ul class="messages warning">
			<?php foreach ($app['messages']->getWarning() as $message) : ?>
			<li><?php echo $message ?></li>
			<?php endforeach; ?>
		</ul>
	<?php endif; ?>

	<?php # affichage des éventuels messages de confirmation
	if ($app['messages']->hasSuccess()) : ?>
		<ul class="messages success">
			<?php foreach ($app['messages']->getSuccess() as $message) : ?>
			<li><?php echo $message ?></li>
			<?php endforeach; ?>
		</ul>
	<?php endif; ?>

	<?php # affichage des éventuels messages d'information
	if ($app['messages']->hasInfo()) : ?>
		<ul class="messages info">
			<?php foreach ($app['messages']->getInfo() as $message) : ?>
			<li><?php echo $message ?></li>
			<?php endforeach; ?>
		</ul>
	<?php endif; ?>
```

Ce template boucle sur les piles si il y a des messages à afficher.

Nous pouvons maintenant comment afficher des messages depuis n'importe quel endroit de notre application et ce de façon contextuelle.
