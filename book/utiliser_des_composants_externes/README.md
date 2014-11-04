# Utiliser des composants externes

Tao fournis les briques de base pour développer une application web.
Dans de nombreux cas de figure vous aurez besoin d'ajouter de nouveaux composants à votre projet.

Par exemple, pour valider les données du formulaire de contact, plutôt que de le faire "manuellement"
vous pourriez utiliser un moteur de validation. C'est ce que nous allons voir.

## Utiliser composer

Dans la mesure du possible nous vous conseillons d'utiliser composer pour ajouter de nouvelle bibliothèques à votre projet.
Cela vous simplifiera la vie pour la maintenance de ces bibliothèques et de votre projet. Aussi, cela permet de bénéficier
de l'autoload de composer, donc d'installer et d'utiliser les librairies dans le projet de façon très simple.

Nous allons utiliser [respect/validation](https://packagist.org/packages/respect/validation) ; mais cela pourrait-être tout autre chose.

Pour cela il faut l'ajouter au fichier `composer.json` :

```json
	...
	"require" : {
		"forxer/tao" : "0.8",
		"respect/validation" : "~0.6"
	}
	...
```

![](https://raw.githubusercontent.com/forxer/tao-tuto/master/book/assets/dialog-information.png) Remarquez le caractère ~ devant le numéro de version,
cela signifie de `>=0.6` à `<2.0` ; c'est particulièrement utile pour les projets qui respectent la [gestion sémantique de version](http://semver.org/lang/fr/).

Maintenant que nous avons déclaré une nouvelle dépendance à notre projet, il faut le mettre à jour grâce à la commande :

```
composer update
```

Cela a pour effet de télécharger les fichiers et de reconstruire l'autoload de composer.

## Utilisation

Comme nous utilisons l'autoload de composer il n'y a pas grand chose à faire pour utiliser directement la nouvelle bibliothèque,
simplement importer son espace de nom dans le fichier où nous voulons l'utiliser. En l'occurrence le contrôlleur de la page contact `/Application/Controllers/Contact.php`

```php
<?php
namespace Application\Controllers;

use Respect\Validation\Validator as v;

class Contact extends BaseController
{
//...
```

Ensuite nous pouvons transformer par exemple :

```php
//...
elseif (!filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
//...
```

en :

```php
//...
elseif (!v::email()->validate($this->email)) {
//...
```

![](https://raw.githubusercontent.com/forxer/tao-tuto/master/book/assets/text-html.png) [La documentation de Respect/Validation](http://documentup.com/Respect/Validation/)

## Conclusion

Nous venons de voir comme il est très simple d'ajouter des fonctionnalités à votre Application Tao grâce à composer et le très grand nombre de bibliothèques présentent sur Packagist.














