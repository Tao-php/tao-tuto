# Contact

Comme vous l’avez déjà deviné, dans cette partie nous allons créer un formulaire de contact.

##  Route, contrôleur et vue

Dans un premier temps nous allons préparer le terrain en créant une nouvelle page. Cela va devenir une habitude : une route, un contrôleur et une vue.

Ajoutez dans le fichier `Application/Config/routes.yml` une nouvelle route :

```yml
contact:
  path: /contact
  defaults:
    _controller: 'Contact::form'
  requirements:
    _method:  GET
```

Rien de particulier par rapport à ce que nous avons déjà vu si ce n’est l’ajout de la contrainte spéciale `_method:  GET` pour restreindre la résolution de cette route à la méthode HTTP GET.

Créez un fichier de contrôleur correspondant `Application/Controllers/Contact.php`

```php
<?php
namespace Application\Controllers;

class Contact extends BaseController
{
    public function form()
    {
        return $this->render('Contact');
    }
}

```

Ici vraiment rien de particulier, le minimum syndical.

Enfin, créez un fichier vue `Application/Views/Contact.php` pour afficher un formulaire

```html+php
<?php $view->extend('Layout') ?>

<?php $view['slots']->set('title', 'Contact') ?>

<form action="" method="post">
	<p><label for="mail">Adresse email</label><br>
	<input name="mail" id="mail" type="text" size="50" maxlength="255" value=""></p>

	<p><label for="subject">Sujet</label><br>
	<input name="subject" id="subject" type="text" size="50" maxlength="255" value=""></p>

	<p><label for="message">Message</label><br>
	<textarea name="message" id="message" cols="37" rows="7"></textarea></p>

	<p><input type="submit" class="submit" value="envoyer"></p>
</form>

```

A ce stade si vous naviguez à l’adresse `http://votre-projet/contact` la page doit afficher le formulaire.

Maintenant nous allons envoyer le formulaire... il ne se passe rien, ou une page blanche, et c'est normal.

##  Envoi du formulaire

Définition d'une nouvelle route pour la méthode HTTP POST :

```yml
contact_process:
  path: /contact
  defaults:
    _controller: 'Contact::process'
  requirements:
    _method:  POST
```

Cette nouvelle route a le même chemin que la précédente (`/contact`) mais avec une contrainte de méthode HTTP différente (POST). Et du coup, l'appel d'une nouvelle méthode du contrôleur.

On va utiliser cette nouvelle route comme destination du formulaire en modifiant la vue de cette façon :

```html+php
...
<form action="<?php echo $view['router']->generate('contact_process') ?>" method="POST">
...
```

Ici, plutôt que d'écrire l'URL en dur, on fait appel au Helper de template "router" pour générer l'URL à partir de l'identifiant de la route, en l’occurrence la route que nous venons de créer.

Ceci sera particulièrement salvateur le jour où nous aurons besoin de modifier la définition de la route, il n’y aura alors pas besoin d'aller modifier tous les endroits où l'URL est utilisée.

Enfin, il faut définir la méthode du contrôleur correspondante `process()` qui sera donc appelée lorsqu’on soumettra le formulaire.

Cette méthode va récupérer les données et les traiter.

```php
// ...
	public function process()
	{
		$email = $this->app['request']->request->get('email');
		$subject = $this->app['request']->request->get('subject', 'email depuis la page contact');
		$message = $this->app['request']->request->get('message');

		// envoi de l'email ...

		$this->app['flashMessages']->success('Votre email a été envoyé.');

		return $this->redirectToRoute('contact');
	}
```

Les données du formulaire sont récupérées grâce au service 'request'.

`$this->app['request']` est une instance de `Symfony\Component\HttpFoundation\Request` qui est une classe du composant HttpFoundation de Symfony2.

Cet objet permet d’accéder dans un style orienté objet aux traditionnels super-globales `$_GET`, `$_POST`, `$_SERVER`, etc.

![](https://raw.githubusercontent.com/forxer/tao-tuto/master/book/assets/text-html.png) [Documentation du composant HttpFoundation](http://symfony.com/fr/doc/current/components/http_foundation/introduction.html)

![](https://raw.githubusercontent.com/forxer/tao-tuto/master/book/assets/dialog-information.png) Remarquez comment il est possible de donner une valeur par défaut à `$subject` en passant un second argument à `$this->app['request']->request->get()`

Ensuite on imagine qu'on envoient l’email pour finalement, enregistrer un message de confirmation et rediriger vers le formulaire.

Évidement, dans cette implémentation, rien ne se passe vraiment à part rediriger vers le formulaire et afficher un message.

![](https://raw.githubusercontent.com/forxer/tao-tuto/master/book/assets/dialog-information.png) A noter l’utilisation de la méthode `redirectToRoute()` du contrôleur de base pour réaliser une redirection vers une route donnée.

Penchons-nous maintenant sur la validation des données.

## Validations des données

L’organisation du contrôleur pour la validation des données peut revêtir bien des formes, ici nous en proposons une, mais rien n’est figé, à vous d’adapter selon vos besoins et habitudes.

Dans un premier temps nous allons ajouter les tests de validation dans la méthode `process()`

```php
// ...

	public function process()
	{
		$email = $this->app['request']->request->get('email');
		$subject = $this->app['request']->request->get('subject', 'email depuis la page contact');
		$message = $this->app['request']->request->get('message');

		if (empty($email)) {
			$this->app['instantMessages']->error('Vous devez saisir une adresse email.');
		}
		elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
			$this->app['instantMessages']->error('Vous devez saisir une adresse email valide.');
		}

		if (empty($message)) {
			$this->app['instantMessages']->error('Vous devez saisir un message.');
		}

		# si on as une erreur, on ré-affiche le formulaire
		if ($this->app['instantMessages']->hasError()) {
			return $this->form();
		}

		// envoi de l'email ...

		$this->app['flashMessages']->success('Votre email a été envoyé.');

		return $this->redirectToRoute('contact');
	}
```

On effectuent les tests, si une erreur survient on l’ajoutent à une pile de message. Une fois les tests effectués on vérifie si la pile de message est vide, dans le cas contraire on ré-affiche le formulaire.

La validation est effectuée, mais les données ne sont pas ré-affichées en cas d’erreur. Modifions donc globalement notre contrôleur de la façon suivante (encore une fois c’est une proposition d’implémentation, il existe pleins d’autres façons de faire, à vous de voir)

```php
<?php
namespace Application\Controllers;

class Contact extends BaseController
{
	protected $email;
	protected $subject;
	protected $message;

	public function form()
	{
		return $this->render('Contact', [
			'email' => $this->email,
			'subject' => $this->subject,
			'message' => $this->message
		]);
	}

	public function process()
	{
		$this->email = $this->app['request']->request->get('email');
		$this->subject = $this->app['request']->request->get('subject', 'email depuis la page contact');
		$this->message = $this->app['request']->request->get('message');

		if (empty($this->email)) {
			$this->app['instantMessages']->error('Vous devez saisir une adresse email.');
		}
		elseif (!filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
			$this->app['instantMessages']->error('Vous devez saisir une adresse email valide.');
		}

		if (empty($this->message)) {
			$this->app['instantMessages']->error('Vous devez saisir un message.');
		}

		# si on as une erreur, on ré-affiche le formulaire
		if ($this->app['instantMessages']->hasError()) {
			return $this->form();
		}

		// envoi de l'email ...

		$this->app['flashMessages']->success('Votre email a été envoyé.');

		return $this->redirectToRoute('contact');
	}
}

```
Je vous laisse analyser le code, c’est assez simple, rien de particulier par rapport à Tao, ici ce n’est que de l’algorithmique.

Pour finir, nous allons modifier la vue de cette façon :

```html+php
<?php $view->extend('Layout') ?>

<?php $view['slots']->set('title', 'Contact') ?>

<form action="" method="post">
	<p><label for="mail">Adresse email</label><br>
	<input name="email" id="email" type="text" size="50" maxlength="255" value="<?php echo $view->e($email) ?>"></p>

	<p><label for="subject">Sujet</label><br>
	<input name="subject" id="subject" type="text" size="50" maxlength="255" value="<?php echo $view->e($subject) ?>"></p>

	<p><label for="message">Message</label><br>
	<textarea name="message" id="message" cols="37" rows="7"><?php echo $view->e($message) ?></textarea></p>

	<p><input type="submit" class="submit" value="envoyer"></p>
</form>

```

## Conclusion

Nous avons vu comment envoyer un formulaire, récupérer les données, les valider et rediriger là où faut.
