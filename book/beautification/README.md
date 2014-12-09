# Beautification

Dans cette partie nous allons nous pencher sur la question de la mise en page et des outils qui sont à notre disposition.

## Templating Assets Helper

Nous avons déjà vu que le composant Templating fournissait différents Helpers. Pour inclure dans les templates les fichiers qui se trouvent dans le répertoire `web/Assets` nous allons utiliser le "Assets Helper".

![](https://raw.githubusercontent.com/forxer/tao-tuto/master/book/assets/emblem-important.png) Selon wikipédia :
>Un Asset est défini comme une ressource basique devant être affichée dans un navigateur web.

En général, les assets sont les fichiers CSS, Javascript et images ; d’où l’arborescence mise en place dans la première partie.

L’objectif principal du "Assets Helper" est de rendre votre application plus portable en générant les chemins des assets :

```html
<link href="<?php echo $view['assets']->getUrl('css/style.css') ?>" rel="stylesheet">

<img src="<?php echo $view['assets']->getUrl('images/logo.png') ?>">
```

Dans un premier temps nous allons configurer le chemin des Assets dans le fichier `/Application/Application.php`

```php
<?php
namespace Application;

use Symfony\Component\Templating\Asset\PathPackage;
use Tao\Application as TaoApplication;

class Application extends TaoApplication
{
	public function __construct($loader, array $config = [], array $classMap = [])
	{
		parent::__construct($loader, $config, __DIR__, $classMap);

		$this['session']->start();

		$this['templating']->get('assets')->addPackage('assets',
			new PathPackage('/Assets/'));
	}
}
```

Ici on as définit un "package d’assets" nommé "assets" pour le répertoire "/Assets/".

![](https://raw.githubusercontent.com/forxer/tao-tuto/master/book/assets/dialog-information.png) N’oubliez pas d’ajouter la ligne `use Symfony\Component\Templating\Asset\PathPackage;`

Tao fournis deux variables de configuration par défaut :
* `'app_url' => '/'` : représente le chemin relatif de l'application depuis le nom de domaine
* `'assets_url' => 'Assets'` : représente le chemin relatif du répertoire assets depuis la configuration de `app_url`

Nous allons utiliser ces paramètres de configuration dans la définition de notre package d’assets :

```php
<?php
namespace Application;

use Symfony\Component\Templating\Asset\PathPackage;
use Tao\Application as TaoApplication;

class Application extends TaoApplication
{
	public function __construct($loader, array $config = [], array $classMap = [])
	{
		parent::__construct($loader, $config, __DIR__, $classMap);

		$this['session']->start();

		$this['templating']->get('assets')->addPackage('assets',
			new PathPackage($this['app_url'] . $this['assets_url']));
	}
}
```

Ainsi, si nous déplaçons les répertoires, il n’y aura plus que le fichier de configuration de l’application à modifier.

Il est possible de créer autant de "packages d’assets" que l’on veux. Plutôt qu’un unique package "Assets" nous allons créer 3 packages : "css", "js" et "img".

```php
<?php
namespace Application;

use Symfony\Component\Templating\Asset\PathPackage;
use Tao\Application as TaoApplication;

class Application extends TaoApplication
{
	public function __construct($loader, array $config = [], array $classMap = [])
	{
		parent::__construct($loader, $config, __DIR__, $classMap);

		$this['session']->start();

		$this['templating']->get('assets')->addPackage('css',
			new PathPackage($this['app_url'] . $this['assets_url'] . '/css'));

		$this['templating']->get('assets')->addPackage('js',
			new PathPackage($this['app_url'] . $this['assets_url'] . '/js'));

		$this['templating']->get('assets')->addPackage('img',
			new PathPackage($this['app_url'] . $this['assets_url'] . '/img'));
	}
}
```

Maintenant, nous allons créer deux fichiers `/web/Assets/css/app.css` et  `/web/Assets/js/app.js`. Et les appeler dans le fichier `/Application/Views/Layout.php` :

```html
<!doctype html>
<html>
	<head>
		<title>
			<?php $view['slots']->output('title', 'Titre par défaut') ?>
		</title>
		<link rel="stylesheet" type="text/css" href="<?php echo $view['assets']->getUrl('app.css', 'css') ?>">
	</head>
	<body>
		<?php echo $view->render('Common/messages') ?>

		<?php $view['slots']->output('_content') ?>

		<?php if ($app['debug']) : ?>
			<?php echo $view->render('Common/DebugInfos') ?>
		<?php endif ?>

		<script src="<?php echo $view['assets']->getUrl('app.js', 'js') ?>"></script>
	</body>
</html>
```

La méthode `$view['assets']->getUrl()` prend deux arguments :
1. le nom du fichier
2. le package dans lequel le fichier se trouve

On sortie, nous aurons quelque chose comme ça :

```html
<!doctype html>
<html>
	<head>
		<title>Titre par défaut</title>
		<link rel="stylesheet" type="text/css" href="/Assets/css/app.css">
	</head>
	<body>
		<!-- ...... -->
		<script src="/Assets/js/app.js"></script>
	</body>
</html>
```

Dans un projet conséquent, avec beaucoup de pages, il sera alors très facile de déplacer les répertoires simplement en modifiant la configuration.

## Bower

Partons du postulat suivant : dans la plupart de nos projets nous utilisons jQuery. De plus en plus souvent nous utilisons aussi Bootstrap.

Plutôt que d’installer et mettre à jours manuellement ces outils, nous allons utiliser [Bower](http://bower.io/).

Bower est un gestionnaire de packages pour le développement web comme Composer est un gestionnaire de packages PHP.

En fait ce chapitre n’est absolument pas lié à Tao, mais c’est une bonne pratique qui se démocratise et pour laquelle il est intéressant de voir comment la mettre en œuvre dans un contexte Tao.

![](https://raw.githubusercontent.com/forxer/tao-tuto/master/book/assets/text-html.png) [Site web de Bower](http://bower.io/)

Nous allons donc commencer par créer un fichier `bower.json` dans lequel nous allons lister les packages que nous souhaitons utiliser dans notre projet.

```json
{
	"name": "tao-tuto",
	"dependencies": {
		"bootstrap" : "3.3.*",
		"jquery" : "1.*"
	}
}
```

Ce fichier indique que le projet "tao-tuto" exige les bibliothèques Bootstrap et jQuery.

Vous pouvez indiquer beaucoup d’autres choses à propos de votre projet dans le fichier bower.json comme son nom, sa description, etc.

![](https://raw.githubusercontent.com/forxer/tao-tuto/master/book/assets/text-html.png) [Spécification bower.json](https://github.com/bower/bower.json-spec)

Avant d’installer ces dépendances, nous allons configurer notre projet pour que les packages soient installés dans le répertoire `/web/Components` ; pour cela nous allons créer un fichier `.bowerrc` dans lequel nous mettons :

```json
{
	"directory": "web/Components"
}
```

Il existe de nombreux paramètres de configuration de Bower.

![](https://raw.githubusercontent.com/forxer/tao-tuto/master/book/assets/text-html.png) [Configuration de Bower](http://bower.io/docs/config/)

Il est maintenant temps d’installer les dépendances grâce à la commande suivante :

```dos
bower install
```

Si vous regardez dans vos fichiers, cette commande doit avoir créé les répertoires `/web/Components/bootstrap` et `/web/Components/jquery` avec les fichiers dedans.

Avant d'utiliser ces bibliothèques dans notre projet nous allons créer un "package d’assets" Components en ajoutant ce qui suit à notre application :

```php
		$this['templating']->get('assets')->addPackage('components',
			new PathPackage($this['app_url'] . $this['components_url']));
```

Ainsi dans les templates nous pourrons accéder aux composants Bower via le Assets Helper et le package "components".

Pour la prise en charge des éléments HTML5 et les media queries par Internet Explorer, nous allons ajouter deux autres composants : `html5shiv` et `respond` en modifiant notre fichier `bower.json`

```json
{
	"name": "tao-tuto",
	"dependencies": {
		"bootstrap" : "3.3.*",
		"html5shiv" : "3.7.*",
		"jquery" : "1.*",
		"respond" : "1.4.*"
	}
}
```

Nous pouvons ensuite lancer la commande suivante pour mettre à jour nos dépendances :

```dos
bower update
```

## Boostrap

Pour illustrer l'utilisation des composants Bower, nous allons inclure Bootstrap dans notre projet.

Prenons modèle sur le [Template de base de Bootstrap](http://getbootstrap.com/getting-started/#template) pour modifier notre fichier `/Application/Views/Layout.php` :

```html
<!doctype html>
<html>
	<head>
		<title>
			<?php $view['slots']->output('title', 'Titre par défaut') ?>
		</title>

		<!-- Bootstrap -->
		<link rel="stylesheet" type="text/css" href="<?php echo $view['assets']->getUrl('bootstrap/dist/css/bootstrap.min.css', 'components') ?>">
		<link rel="stylesheet" type="text/css" href="<?php echo $view['assets']->getUrl('bootstrap/dist/css/bootstrap-theme.min.css', 'components') ?>">

		<link rel="stylesheet" type="text/css" href="<?php echo $view['assets']->getUrl('app.css', 'css') ?>">

		<!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
		<!--[if lt IE 9]>
			<script src="<?php echo $view['assets']->getUrl('html5shiv/dist/html5shiv.min.js', 'components') ?>"></script>
			<script src="<?php echo $view['assets']->getUrl('respond/dest/respond.min.js', 'components') ?>"></script>
		<![endif]-->
	</head>
	<body>
		<?php echo $view->render('Common/messages') ?>

		<?php $view['slots']->output('_content') ?>

		<?php if ($app['debug']) : ?>
			<?php echo $view->render('Common/DebugInfos') ?>
		<?php endif ?>

		<script src="<?php echo $view['assets']->getUrl('jquery/dist/jquery.min.js', 'components') ?>"></script>
		<script src="<?php echo $view['assets']->getUrl('bootstrap/dist/js/bootstrap.min.js', 'components') ?>"></script>

		<script src="<?php echo $view['assets']->getUrl('app.js', 'js') ?>"></script>
	</body>
</html>
```

Voilà, le nécessaire est chargé sur toutes nos pages, nous avons utilisé Bower pour simplifier les mises à jours futures et le Assets Helper pour la portabilité de notre application.

Maintenant que les outils sont en place, jouons un peu avec Bootstrap pour rendre tous cela plus beau.

Nous allons ajouter un menu de navigation à notre `/Application/Views/Layout.php` :

```html
<!doctype html>
<html>
	<head>
		<!-- ... -->
	</head>
	<body>
		<?php echo $view->render('Common/Navbar') ?>

		<div class="container">
			<?php echo $view->render('Common/messages') ?>

			<?php $view['slots']->output('_content') ?>

			<?php if ($app['debug']) : ?>
				<?php echo $view->render('Common/DebugInfos') ?>
			<?php endif ?>
		</div>

		<!-- scripts -->
	</body>
</html>
```

Ici nous avons ajouté le rendu de la vue `Common/Navbar` et nous avons encadré le contenu principal dans une `div.container`

Créons le fichier `/Application/Views/Common/Navbar.php` :

```html
<div class="navbar navbar-inverse navbar-static-top" role="navigation">
	<div class="container">
		<div class="navbar-header">
			<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target=".navbar-collapse">
				<span class="sr-only">Menu</span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			</button>
			<a class="navbar-brand" href="<?php echo $view['router']->generate('home') ?>">Tutoriel Tao</a>
		</div>
		<div class="collapse navbar-collapse">
			<ul class="nav navbar-nav">
				<li<?php if ($app['request']->attributes->get('_route') == 'home') : ?> class="active"<?php endif ?>><a href="<?php echo $view['router']->generate('home') ?>">Accueil</a></li>
				<li<?php if ($app['request']->attributes->get('_route') == 'hello') : ?> class="active"<?php endif ?>><a href="<?php echo $view['router']->generate('hello') ?>">Hello</a></li>
				<li<?php if (in_array($app['request']->attributes->get('_route'), ['contact', 'contact_process'])) : ?> class="active"<?php endif ?>><a href="<?php echo $view['router']->generate('contact') ?>">Contact</a></li>
			</ul>
		</div>
	</div>
</div>
```

Ici nous avons créé le markup d'une [navbar bootstrap](http://getbootstrap.com/components/#navbar) et ajouté quelques liens. Nous utilisons la méthode `$view['router']->generate()` pour générer les URL comme nous l'avions déjà vu. La particularité c'est que que nous réalisons des tests sur les identifiants des routes pour rendre actif tel ou tel autre lien.

Maintenant, et pour finir nous allons modifier le templates du formulaire de contact pour utiliser bootstrap ; modifier le fichier `/Application/View/contact.php` de la façon suivante :

```html
<?php $view->extend('Layout') ?>

<?php $view['slots']->set('title', 'Contact') ?>

<form action="<?php echo $view['router']->generate('contact_process') ?>" class="form-horizontal" method="post" role="form">

	<div class="form-group">
		<label for="mail" class="col-sm-2 control-label">Adresse email</label>
		<div class="col-sm-10">
			<input name="email" id="email" type="email" size="50" maxlength="255" value="<?php
			echo $view->e($email) ?>" placeholder="Saisissez votre adresse email" class="form-control" required>
		</div>
	</div>

	<div class="form-group">
		<label for="subject" class="col-sm-2 control-label">Sujet</label>
		<div class="col-sm-10">
			<input name="subject" id="subject" type="text" size="50" maxlength="255" value="<?php
			echo $view->e($subject) ?>" placeholder="Saisissez un sujet" class="form-control">
		</div>
	</div>

	<div class="form-group">
		<label for="message" class="col-sm-2 control-label">Message</label>
		<div class="col-sm-10">
			<textarea name="message" id="message" cols="37" rows="7" placeholder="Saisissez un message" class="form-control" required><?php
			echo $view->e($message) ?></textarea>
		</div>
	</div>

	<div class="form-group">
		<div class="col-sm-offset-2 col-sm-10">
			<button type="submit" class="btn btn-primary">envoyer</button>
		</div>
	</div>
</form>
```

Ici nous avons utilisé le markup de bootstrap et saupoudré de HTML5. Bon, voilà c'était pour le fun...

Bootstrap fournis une très grande quantité de classes CSS, de composants et de plugins JS. A vous de piocher dedans selon vos besoins.

![](https://raw.githubusercontent.com/forxer/tao-tuto/master/book/assets/text-html.png) [Bootstrap CSS](http://getbootstrap.com/css/)
![](https://raw.githubusercontent.com/forxer/tao-tuto/master/book/assets/text-html.png) [Bootstrap Components](http://getbootstrap.com/components/)
![](https://raw.githubusercontent.com/forxer/tao-tuto/master/book/assets/text-html.png) [Bootstrap Javascript](http://getbootstrap.com/javascript/)

## Font Awesome

Maintenant, histoire d'en remettre une petite couche, nous allons ajouter des icônes à notre barre de navigation. Pour cela nous allons utiliser [Font Awesome](http://fontawesome.io/).

Modification du fichier `bower.json` :

```json
{
	"name": "tao-tuto",
	"dependencies": {
		"bootstrap" : "3.2.*",
		"fontawesome" : "4.*",
		"html5shiv" : "3.7.*",
		"jquery" : "1.*",
		"respond" : "1.4.*"
	}
}
```

Mise à jour des packages :

```dos
bower update
```

Modification de `/Application/Views/Layout.php` en ajoutant ce qui suit là où il faut :

```html
<link rel="stylesheet" type="text/css" href="<?php echo $view['assets']->getUrl('fontawesome/css/font-awesome.min.css', 'components') ?>">
```

Ensuite nous ajoutons les icônes à `/Application/Views/Common/Navbar.php` comme indiqué dans les exemples de [Font Awesome](http://fontawesome.io/examples/)

```html

<div class="navbar navbar-inverse" role="navigation">
	<div class="container">
		<div class="navbar-header">
			<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target=".navbar-collapse">
				<span class="sr-only">Menu</span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			</button>
			<a class="navbar-brand" href="<?php echo $view['router']->generate('home') ?>">Tutoriel Tao</a>
		</div>
		<div class="collapse navbar-collapse">
			<ul class="nav navbar-nav">
				<li<?php if ($app['request']->attributes->get('_route') == 'home') : ?> class="active"<?php endif ?>><a href="<?php echo $view['router']->generate('home') ?>"><i class="fa fa-home fa-fw"></i>&nbsp;Accueil</a></li>
				<li<?php if ($app['request']->attributes->get('_route') == 'hello') : ?> class="active"<?php endif ?>><a href="<?php echo $view['router']->generate('hello') ?>"><i class="fa fa-comment fa-fw"></i>&nbsp;Hello</a></li>
				<li<?php if (in_array($app['request']->attributes->get('_route'), ['contact', 'contact_process'])) : ?> class="active"<?php endif ?>><a href="<?php echo $view['router']->generate('contact') ?>"><i class="fa fa-envelope fa-fw"></i>&nbsp;Contact</a></li>
			</ul>
		</div>
	</div>
</div>
```

Il y a une très [grande quantité d’icônes](http://fontawesome.io/icons/) disponibles. Comme c’est une police de caractère il est très facile d’appliquer des styles CSS.

## Conclusion

Cette partie était un peu à part dans la mesure où -mis à part le Assets Helper- elle n'était pas directement liée à Tao.

Mais il nous semblait intéressant de voir comment intégrer de tels outils avec Tao.
