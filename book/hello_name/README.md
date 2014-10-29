# Hello Name!

Nous allons reprendre notre projet pour lui ajouter un peu de dynamisme, sinon quel intérêt d’utiliser un langage de script comme PHP ?

Nous allons donc modifier le code de la précédente partie afin que le résultat affiche un "Hello Name!" où la chaine de caractères "Name" est dynamique en fonction de l’URL demandée.

## Paramètre de substitution

Reprenons notre fichier de définition de routes `/Application/Config/routes.yml` de cette façon :

```yml
hello:
path: /hello/{name}
defaults:
	_controller: 'Application\Controllers\Hello::world'
```
Nous venons de modifier la définition de la route en lui ajoutant un paramètre de substitution {name}

## Parenthèse débogage

Si nous affichons la page `http://votre-projet/app.php/hello` dans le navigateur... il ne se passe rien : nous avons toujours notre "Hello World!" qui s’affiche dans une page HTML. Rien a changé.

![](https://raw.githubusercontent.com/forxer/tao-tuto/master/book/assets/emblem-important.png) C’est normal car les résolutions de routes sont mises en cache afin de limiter l’impact du composant de routage sur les performances.

Il suffit donc de supprimer les fichiers cache présents dans `/Application/Storage/Cache/Router`. Cela fait et près rafraichissement de la page, nous avons maintenant une belle page blanche (ce qui signifie que quelque chose à bien changé dans le comportement de l’application).

![](https://raw.githubusercontent.com/forxer/tao-tuto/master/book/assets/dialog-information.png) Pour ne pas avoir à supprimer le cache manuellement à chaque modification des routes, nous allons passer en "mode debug". Dans ce mode les fichiers cache seront régénérés dès que leurs sources seront modifiées contrairement au mode par défaut où ils ne sont générés que s’ils sont absents.

Pour passer en "mode debug" il faut modifier le fichier front-controller `/web/app.php` afin de passer en second argument un tableau de configuration au constructeur de l’application de cette façon :

```php
//...

# Initialisation de l'application
$app = new Application\Application($loader, ['debug' => true]);

//...
```

![](https://raw.githubusercontent.com/forxer/tao-tuto/master/book/assets/dialog-information.png) Remarquez la notation PHP 5.4 du tableau de configuration : `['debug' => true]` qui aurait habituellement été `array('debug' => true)` ; cela apporte du confort au développeur et une certaine consistance avec d’autres langages, l’ancienne notation fonctionnant évidement toujours de la même façon.

Maintenant, si vous rafraichissez la page, en lieu et place de la page blanche, vous obtenez un magnifique message d’erreur détaillé.

Prenez le temps d’étudier cette interface d’affichage des erreurs. Cela vous servira forcément à un moment ou à un autre par la suite.

Rapidement, à gauche vous avez la "backtrace" interactive, à droite le message d’erreur, puis le focus du fichier où s’est produite l’erreur et en dessous des informations sur l’environnement de la requête.

![](https://raw.githubusercontent.com/forxer/tao-tuto/master/book/assets/dialog-information.png) Lorsque le mode debug n’est pas activé, les erreurs PHP sont "loguées" dans `/Application/Storage/Logs/php_errors.log`

## Retour aux affaires

Donc !

> The template "Errors/404" does not exist.

Et oui, en effet, nous n’avons jamais créé de template Errors/404. Allons-y, créons le fichier `Application/Views/Errors/404.php` et mettons-y dedans ceci :

```php
<?php $view->extend('Layout') ?>

<?php $view['slots']->set('title', 'Document non trouvé') ?>

<h1>Document non trouvé</h1>

<p>Le document que vous cherchez n’existe pas.</p>
```

Après rafraichissement de la page, le message de page non-trouvée doit apparaitre correctement.

Mais pourquoi ce template est appelé ? Car nous essayons de joindre l’URL `/hello` alors qu’il n’y a aucune route de définie pour cette adresse, en effet notre route c’est maintenant `/hello/{name}`. Le paramètre de substitution est obligatoire. Pour rendre ce paramètre optionnel il faut lui donner une valeur par défaut en modifiant la définition de la façon suivante :

```yml
hello:
path: /hello/{name}
defaults:
	_controller: 'Application\Controllers\Hello::world'
	name: world
```

Lorsqu’on rafraichi la page, on retrouve bien notre page initiale.

Maintenant si vous allez à l’adresse `http://votre-projet/app.php/hello/toto` cela ne change rien.

Il faut récupérer la valeur de {name} dans le contrôleur, passer cette valeur au template et finalement l’afficher dans ce dernier.

Modifions donc le contrôleur de cette façon :

```php
<?php
namespace Application\Controllers;

use Tao\Controller\Controller as BaseController;

class Hello extends BaseController
{
	public function world()
	{
		$name_from_request = $this->app['request']->attributes->get('name');

		return $this->render('Hello', [
			'name_from_controller' => $name_from_request
		]);
	}
}
```
On récupère la valeur de `{name}` depuis la requête HTTP et on la passe au template.

`$this->app['request']` est une instance de `Symfony\Component\HttpFoundation\Request` qui est une classe du composant HttpFoundation de Symfony2.

Cet objet permet d’accéder dans un style orienté objet aux traditionnels super-globales `$_GET`, `$_POST`, `$_SERVER`, etc.

![](https://raw.githubusercontent.com/forxer/tao-tuto/master/book/assets/text-html.png) [Documentation du composant HttpFoundation](http://symfony.com/fr/doc/current/components/http_foundation/introduction.html)

Après avoir récupéré la valeur de `{name}` on la passe au template à travers l’appel de la méthode `render()`.

Cette méthode reçoit toujours en premier argument le nom du template et on ajoute en second argument le tableau de variable à lui passer. Les clés de ce tableau seront les noms de variables dans le template.

Ainsi, il nous reste à afficher `$name_from_controller` dans le template :

```html+php
<?php $view->extend('Layout') ?>

<?php $view['slots']->set('title', 'Hello') ?>

<p>Hello <?php echo $view->e($name_from_controller) ?>!</p>
```
![](https://raw.githubusercontent.com/forxer/tao-tuto/master/book/assets/emblem-important.png) La variable est échappée à l’aide de la fonction `$view->e()` Ceci pour des raisons de sécurité car la valeur de cette variable est une entrée utilisateur.
**Pensez à toujours échapper les données envoyées par les utilisateurs.**

Maintenant si nous naviguons à l’adresse `http://votre-projet/app.php/hello/toto` notre page affiche bien "Hello toto!" ; et si nous naviguons à l’adresse `http://votre-projet/app.php/hello` notre page affiche "Hello world!" car c’est la valeur par défaut.

## Conclusion

Nous avons vu comment modifier la définition d’une route pour lui ajouter des masques de substitution.

Nous avons fait une parenthèse en ce qui concerne le débogage.

Puis nous avons vu comment passer des données de l’URL au contrôleur et du contrôleur au template.

