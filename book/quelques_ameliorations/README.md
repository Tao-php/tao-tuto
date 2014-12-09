# Quelques améliorations

Apportons quelques améliorations bienvenues à notre projet.

## Ré-écriture d’URL

Avoir /app.php dans l’URL n’est pas souhaité, utilisons la ré-écriture d’URL d’Apache pour cela. Évidement si vous utilisez Apache, sinon reportez vous à la documentation de votre serveur.

Vous allez créer un fichier `/web/.htaccess ` dans lequel vous allez mettre :

```apache
<IfModule mod_rewrite.c>
RewriteEngine on
RewriteCond %{REQUEST_FILENAME} !-d
RewriteCond %{REQUEST_FILENAME} !-f
RewriteRule ^(.*)$ app.php?q=$1 [L,QSA]
</IfModule>
```

C’est la ré-écriture d’URL pour supprimer le nom du fichier front-controller dans sa plus simple expression.
 Elle fonctionnera très bien dans la majorité des cas.

![](https://raw.githubusercontent.com/forxer/tao-tuto/master/book/assets/dialog-information.png) Personnellement j’aime utiliser le [.htaccess de Symfony 2](https://raw.githubusercontent.com/symfony/symfony-standard/master/web/.htaccess)

## Espace de noms des contrôleurs

Si nos contrôleurs se trouvent tous dans le même espace de nom, à savoir `Application\Controller\` alors il pourrait être intéressant de ne pas l’écrire systématiquement dans le fichier de définition des routes.

Pour cela, dans le fichier `/Application/Config/prod.php` nous allons ajouter le paramètre de configuration suivant :

```php
	'routing.controllers_namespace' => 'Application\Controllers'
```

Maintenant il faut modifier le fichier `/Application/Config/routes.yml`

```yaml
hello:
  path: /hello/{name}
  defaults:
    _controller: 'Hello::world'
    name: world
```

Et voilà, le tour est joué.

## Contrôleur de base

Notre contrôleur étend la classe `Tao\Controller\Controller` :

```php
namespace Application\Controllers;

use Tao\Controller\Controller as BaseController;

class Hello extends BaseController
{
//...
```

Créez un fichier `/Application/Controllers/BaseController.php` et mettez-y ceci :

```php
<?php
namespace Application\Controllers;

use Tao\Controller\Controller;

class BaseController extends Controller
{

}
```

Cette nouvelle classe ne fait rien qu’étendre le contrôleur de base, comme elle se trouve dans l’espace de nom des contrôleurs de notre application nous pouvons modifier notre contrôleur Hello de cette façon :

```php
namespace Application\Controllers;

class Hello extends BaseController
{
//...
```

Cela simplifie la déclaration de nos futurs contrôleurs.

![](https://raw.githubusercontent.com/forxer/tao-tuto/master/book/assets/dialog-information.png) Cette nouvelle classe BaseController sera un endroit idéal pour déclarer des méthodes communes à l’ensemble des contrôleurs de notre application.

## Home page

Notre application n’a même pas de page d’accueil. Même si nous n’avons pas grand chose à mettre dedans, il est temps de remédier à cela. Nous allons donc créer une route, un contrôleur et une vue.

Ajoutez tout en haut du fichier `/Application/Config/routes.yml` une nouvelle route :

```yml
home:
  path: /
  defaults:
    _controller: 'Home::show'
```

Créez un fichier de contrôleur correspondant `Application/Controllers/Home.php`

```php
<?php
namespace Application\Controllers;

class Home extends BaseController
{
    public function show()
    {
        return $this->render('Home');
    }
}
```

Enfin, créez un fichier vue `Application/Views/Home.php`

```html
<?php $view->extend('Layout') ?>

<h1 class="page-header">Bienvenue !</h1>
```

Voilà, maintenant, au lieu d’une erreur 404 nous avons une page d’accueil.

![](https://raw.githubusercontent.com/forxer/tao-tuto/master/book/assets/dialog-information.png) Remarquez ci-dessus comme c’est simple finalement de créer une page avec Tao : une route, un contrôleur et une vue ; trois fichiers bien rangés et quelques lignes de code explicites.

## Debug infos

Et si on ajoutaient quelques informations sur l’exécution de l’application ? Bonne idée ! Dans un premier temps on va afficher le temps d’exécution de l’application et la mémoire consommée par l’application.

Nous allons afficher ces informations en bas de nos pages, de toutes nos pages. Tao fournis quelques méthodes utilitaires pour cela.

Modifiez donc dans le fichier `/Application/Views/Layout.php` de la façon suivante :

```html
<!doctype html>
<html>
	<head>
		<title>
			<?php $view['slots']->output('title', 'Titre par défaut') ?>
		</title>
	</head>
	<body>
		<?php $view['slots']->output('_content') ?>

		<?php if ($app['debug']) : ?>
			<?php echo $view->render('Common/DebugInfos') ?>
		<?php endif ?>

	</body>
</html>
```

On as ajouté l’affichage du template `Common/DebugInfos` si l’application est en mode debug.

Nous ne l’avions pas encore vu, mais il est effectivement possible d’afficher dans un template un autre template.

On peux parler d’inclusion de template.

Cela est particulièrement utile pour partager un template entre plusieurs autres templates, pour mutualiser le rendu d’un template.

![](https://raw.githubusercontent.com/forxer/tao-tuto/master/book/assets/dialog-information.png) Bon, ce n’est pas vraiment utile dans le cas présent. En effet, si nous affichons le template `Common/DebugInfos` dans le `Layout` il y a très peu de chances pour que nous l’affichions dans un autre template. Le côté mutualisation perd de son intérêt dans ce cas de figure. Pour des questions de performance et de logique, nous aurions pus/dus mettre directement le contenu du template `DebugInfos` dans le `Layout`. Mais je voulais introduire cette notion d'inclusion de templates. Et puis j'aime avoir un fichier `Layout` le plus clair et léger possible, avec le minimum de PHP possible, or le template `DebugInfos` va réaliser quelques traitements et je ne souhaitais pas les voir dans le `Layout`.

Maintenant nous allons donc créer un fichier `/Application/Views/Common/DebugInfos.php` :

```html
<div class="debug">
	<?php echo $view['modifier']->number($app->utilities->getExecutionTime(), 4) ?> s -
	<?php echo $app->utilities->getMemoryUsage() ?>
</div>
```

Ici on utilise deux méthodes utilitaires `$app->utilities->getExecutionTime()` et `$app->utilities->getMemoryUsage() ?>` qui permettent de récupérer respectivement le temps d’exécution de l’application et la mémoire consommée par l’application.

Les données de la première sont formatées à l’aide du "helper de templates" `modifier` qui correspond à la fonction PHP `number_format()`.

Vous pouvez aussi utiliser ce fichier pour afficher en bas de page ce que contient une variable.

Souvent en phase de développement, pour voir le contenu d’une variable on utilisent `var_dump()`, `print_r()`, etc. Tao a dans ses dépendances l'outil Kint, vous n’êtes pas obligés de l'utiliser mais ce serait dommage de s'en priver.

Par exemple, modifier le de cette façon :

```html
<div class="debug">
	<?php echo $view['modifier']->number($app->utilities->getExecutionTime(), 4) ?> s -
	<?php echo $app->utilities->getMemoryUsage() ?>
</div>

<?php d($view['modifier']) ?>
```

Kint fournis la fonction `d()` pour dumper le contenu d'une variable. Si vous rafraichissez la page, vous verrez des informations sur le "helper de templates" `modifier`

![](https://raw.githubusercontent.com/forxer/tao-tuto/master/book/assets/text-html.png) [Documentation de Kint](http://raveren.github.io/kint/)

Familiarisez-vous avec cet outil, il peut vous rendre de grands services.
