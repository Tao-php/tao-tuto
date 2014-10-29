# Hello World!

Hé oui, vous n’y échapperez pas : nous allons réaliser le fameux "Hello World!" avec notre application Tao. Je sais que ce n’est pas passionnant, mais c’est utile pour présenter quelques principes fondamentaux.

De façon simplifiée le Workflow d’une application Tao peut être représenté de la façon suivante :

Requête HTTP
:arrow_right:
Résolution de la route
:arrow_right:
Appel du contrôleur
:arrow_right:
Réponse HTTP

Dans cette partie, nous allons donc :
* Créer une route
* Créer un contrôleur
* Créer une vue (qui sera le contenu de la réponse HTTP)

## Route

Le `composer.json` de Tao exige la dépendance `symfony/routing` ; nous allons donc utiliser le composant de routage de Symfony2.

> Le Composant de Routage fait correspondre une requête HTTP à un ensemble de variables de configuration.

Notre application va définir une collection de routes. Cette collection sera définie dans le fichier `/Application/Config/routes.yml` au format YAML.

![](https://raw.githubusercontent.com/forxer/tao-tuto/master/book/assets/text-html.png) [Le format YAML](http://symfony.com/fr/doc/current/components/yaml/yaml_format.html)

Voici la définition d’une route basique :

```yml
identifiant_de_la_route:
path: /url-de-la-route
defaults:
	_controller: 'Controler::method'
```

Ici on définit que, si la requête HTTP pointe vers l’URL `/url-de-la-route`, alors la route `identifiant_de_la_route` correspondante indiquera qu’il faut invoquer le contrôleur `Controler::method`.

Ceci est une route basique, mais il est possible d’indiquer des paramètres (optionnels ou non), des valeurs par défaut, des contraintes, des options, etc. ; bref, il est possible de configurer bien plus finement notre route.

Voici par exemple la définition de la même route, mais de façon un peu plus complexe :

```yml
identifiant_de_la_route:
path: /url-de-la-route/{page}
defaults:
	_controller: 'Controler::method'
	page: 1
requirements:
	page: \d+
```

Ici on ajoute à notre route un paramètre de substitution `{page}` qui prendra par défaut la valeur `1` et devra être composé d’un ou de plusieurs caractères décimaux.

![](https://raw.githubusercontent.com/forxer/tao-tuto/master/book/assets/dialog-information.png) C’est le modèle de définition de route généralement utilisé pour la pagination de liste d’éléments.

![](https://raw.githubusercontent.com/forxer/tao-tuto/master/book/assets/text-html.png) [Définition des routes avec le composant de routage de Symfony2](http://symfony.com/fr/doc/current/components/routing/introduction.html#definition-des-routes)

Nous allons donc créer un fichier `/Application/Config/routes.yml` et y définir une route de la façon suivante :

```yml
hello:
path: /hello
defaults:
	_controller: 'Application\Controllers\Hello::world'
```

Si, dans votre navigateur, vous naviguez sur votre projet à l’adresse : `http://votre-projet/app.php` vous obtiendrez une page blanche.

Maintenant si vous allez à l’adresse `http://votre-projet/app.php/hello` vous obtiendrez un message d’erreur :

> Class "Application\Controllers\Hello" does not exist.

Cela montre que le routeur a bien interprété l’URL de la requête et que le contrôleur a bien été invoqué.

"Oui mais j’ai une erreur !" C’est tout à fait normal car nous avons définit une route pour le chemin `/hello` correspondante au contrôleur `Application\Controllers\Hello::world` qui lui n’existe pas.

## Contrôleur

Nous allons donc créer le fichier `Application/Controllers/Hello.php` avec le contenu suivant :

```php
<?php
namespace Application\Controllers;

use Tao\Controller\Controller as BaseController;
use Symfony\Component\HttpFoundation\Response;

class Hello extends BaseController
{
	public function world()
	{
		return new Response('Hello world!');
	}
}
```

Maintenant si vous allez à l’adresse `http://votre-projet/app.php/hello` vous aurez bien le message "Hello world!" d’affiché.

![](https://raw.githubusercontent.com/forxer/tao-tuto/master/book/assets/emblem-important.png) Cette classe étend et **doit étendre** le contrôleur de base de Tao.

![](https://raw.githubusercontent.com/forxer/tao-tuto/master/book/assets/emblem-important.png) La méthode du contrôleur `world()` appelée par le routeur, retourne et **doit retourner** une réponse HTTP.

La réponse HTTP est une instance de `Symfony\Component\HttpFoundation\Response` (ou dérivée) qui est une classe du composant HttpFoundation de Symfony2.

![](https://raw.githubusercontent.com/forxer/tao-tuto/master/book/assets/text-html.png) [Documentation du composant HttpFoundation](http://symfony.com/fr/doc/current/components/http_foundation/introduction.html)

Le fait d’étendre le contrôleur de base de Tao permet d’hériter de ses méthodes, notamment :

* `generateUrl()` permet de générer une URL à partir d’une route
* `redirect()` permet de rediriger vers une URL
* `redirectToRoute()` permet de rediriger vers une route
* `render()` retourne une réponse HTTP contenant le rendu d’une vue
* et d’autres...

Aussi, l’instance de l’application est injectée au contrôleur lors de son instanciation, on peux donc y accéder dans la classe du contrôleur via `$this->app`

Maintenant, dans la réalité le contrôleur ne retournera pas une simple chaine de caractère mais le rendu d’une vue.

Nous allons faire en sorte que notre contrôleur retourne le rendu d’un modèle de mise en page comme réponse en le modifiant de la façon suivante :

```php
<?php
namespace Application\Controllers;

use Tao\Controller\Controller as BaseController;

class Hello extends BaseController
{
	public function world()
	{
		return $this->render('Hello');
	}
}
```

Maintenant, si vous rafraichissez la page dans votre navigateur, vous aurez une erreur :

> The template "Hello" does not exist.

Encore une fois, c’est normal puisque nous n’avons pas créé de fichier de vue (ou "template").

## Vue

Nous allons donc créer le fichier `Application/Views/Hello.php` avec le contenu suivant :

```HTML
<!DOCTYPE html>
<html>
	<head>
		<title>Hello</title>
	</head>
	<body>
		<p>Hello world!</p>
	</body>
</html>
```

La méthode `world()` du contrôleur `Hello` retourne `$this->render('Hello');` du coup le template `Application/Views/Hello.php` est parsé et retourné dans une instance de réponse HTTP.

Le moteur de template PHP utilisé est le composant Templating de Symfony2.

![](https://raw.githubusercontent.com/forxer/tao-tuto/master/book/assets/text-html.png) [Documentation composant Templating Symfony2](http://symfony.com/doc/current/components/templating/index.html)

Aujourd’hui nous n’avons qu’un seul fichier. Mais demain nous en aurons plusieurs. Il sera alors très intéressant de mutualiser les parties communes de notre template.

Pour se faire nous allons diviser notre templates en deux. Créons d’abord le fichier `Application/Views/Layout.php` avec le contenu suivant :

```HTML
<!doctype html>
<html>
	<head>
		<title>
			<?php $view['slots']->output('title', 'Titre par défaut') ?>
		</title>
	</head>
	<body>
		<?php $view['slots']->output('_content') ?>
	</body>
</html>
```
La variable `$view` est l’instance du moteur de templates lui-même.

Aussi, nous utilisons ici 2 "slots" : 'title' et '_content'

![](https://raw.githubusercontent.com/forxer/tao-tuto/master/book/assets/text-html.png) [Documentation sur le Slots Helper](http://symfony.com/doc/current/components/templating/helpers/slotshelper.html)

Nous allons modifier notre fichier `Application/Views/Hello.php` de la façon suivante :

```html
<?php $view->extend('Layout') ?>

<?php $view['slots']->set('title', 'Hello') ?>

<p>Hello world!</p>
```

Maintenant le template `Hello.php` étend  le template `Layout.php` ; ceci grâce à `<?php $view->extend('Layout') ?>`

Juste après on définit le slot "title" (qui est appelé dans le layout)

Le `Layout.php` sera mutualisé entre toutes les pages du projet.

## Conclusion

Nous savons maintenant définir une route, lui associer un contrôleur et faire le rendu d’un template afin de retourner une réponse proprement formalisée.

Le cheminement parait compliqué pour un si peu de choses, c’est le revers de l’exemple par le "Hello world!"

Nous allons maintenant ajouter un peu de dynamisme à tous cela avec le "Hello Name!"

