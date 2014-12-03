# Injection de dépendances et conteneur d'injection de dépendances

Dans cette partie nous allons voir par l'exemple les concepts d'*injection de dépendances* et de *conteneur d'injection de dépendances*. Ensuite nous regarderons l'implémentation de ces concepts par Pimple qui est le conteneur d'injection de dépendances utilisé par Tao.

Attention, vous vous attaquez à un gros pavé, vous devriez peut-être aller chercher un café...

## Injection de Dépendances

![](https://raw.githubusercontent.com/forxer/tao-tuto/master/book/assets/emblem-important.png) dans ce chapitre nous parlons de dépendances entre les objets, rien à voir avec les dépendances gérées par composer par exemple

L'injection de dépendances est un des [design pattern](http://fr.wikipedia.org/wiki/Patron_de_conception) les plus simple. Mais il est aussi l'un des plus difficile à expliquer clairement.

Selon Wikipedia :

>L'injection de dépendances (Dependency Injection) est un mécanisme qui permet d'implémenter le principe de l'inversion de contrôle. Il consiste à créer dynamiquement (injecter) les dépendances entre les différentes classes en s'appuyant sur une description (fichier de configuration ou métadonnées) ou de manière programmatique. Ainsi les dépendances entre composants logiciels ne sont plus exprimées dans le code de manière statique mais déterminées dynamiquement à l'exécution.

Je sais pas vous, mais moi ça me fait un peu mal à la tête.

Nous allons reprendre l'exemple concret le plus souvent utilisé pour présenter l'injection de dépendance dans un projet web en PHP.

![](https://raw.githubusercontent.com/forxer/tao-tuto/master/book/assets/text-html.png)   [Sources](http://fabien.potencier.org/article/11/what-is-dependency-injection)

Sur le web, les applications ont besoin de stocker des informations utilisateur entre deux requêtes. Pour cela on utilisent généralement les sessions.

Par exemple pour stocker la langue de l'utilisateur :

```php
$_SESSION['language'] = 'fr';
```

Et pour récupérer :

```php
$user_language = $_SESSION['language'];
```

Comme nous utilisons la programmation orientée objet, nous avons une classe PHP pour encapsuler ce mécanisme de sessions :

```php
class SessionStorage
{
    function __construct($cookieName = 'PHP_SESS_ID')
    {
        session_name($cookieName);
        session_start();
    }

    function set($key, $value)
    {
        $_SESSION[$key] = $value;
    }

    function get($key)
    {
        return $_SESSION[$key];
    }

    // ...
}
```

Et une classe utilissateur :

```php
class User
{
    protected $storage;

    function __construct()
    {
        $this->storage = new SessionStorage();
    }

    function setLanguage($language)
    {
        $this->storage->set('language', $language);
    }

    function getLanguage()
    {
        return $this->storage->get('language');
    }

    // ...
}
```

Ces classes sont simples et faciles à utiliser :

```php
$user = new User();
$user->setLanguage('fr');
$user_language = $user->getLanguage();
```

Tous va pour le mieux jusqu'à ce que vous ayez besoin de flexibilité. Par exemple, comment changer le nom du cookie des session ? Il y a plusieurs possibilités.

- Coder en dur dans la classe `User` :

```php
class User
{
    function __construct()
    {
        $this->storage = new SessionStorage('SESSION_ID');
    }

    // ...
}
```

Coder en dur le nom de session dans la classe `User` ne résout pas vraiment le problème car vous ne pouvez pas changer d'avis facilement plus tard sans modifier de nouveau la classe Utilisateur.

- Définir une constante en dehors de la classe `User` :

```php
class User
{
    function __construct()
    {
        $this->storage = new SessionStorage(STORAGE_SESSION_NAME);
    }

    // ...
}

define('STORAGE_SESSION_NAME', 'SESSION_ID');
```

Utiliser une constante est également une mauvaise idée car la classe `User` dépend désormais de la définition d'une constante.

- Ajouter le nom de session comme un argument du constructeur de la classe `User` :

```php
class User
{
    function __construct($sessionName)
    {
        $this->storage = new SessionStorage($sessionName);
    }

    // ...
}

$user = new User('SESSION_ID');
```

En passant le nom de session comme argument est probablement la meilleure solution, mais ça sent toujours mauvais. Cela encombre les arguments du constructeur de la classe `User` avec des choses qui ne sont pas pertinentes à l'objet lui-même.

Mais il ya encore un autre problème qui ne peut être résolu facilement : comment puis-je changer la classe SessionStorage ?

C'est impossible avec l'implémentation actuelle, sauf si vous modifiez encore la classe `User`.

C'est là qu'interviens l'injection de dépendance. Au lieu de créer l'objet SessionStorage l'intérieur de la classe `User`, nous allons injecter l'objet SessionStorage dans l'objet de  `User` en le passant comme argument du constructeur :

```php
class User
{
    function __construct($storage)
    {
        $this->storage = $storage;
    }

    // ...
}
```

Voilà, l'injection de dépendance c'est ça. Rien de plus !

```php
$sessionStorage = new SessionStorage('SESSION_ID');
$user = new User($sessionStorage);
```

Maintenant, la configuration de l'objet de SessionStorage est très simple, et le remplacement de la classe SessionStorage est également très facile.

Et tout est possible sans changer la classe de `User grâce à une meilleure séparation des problèmes.

L'injection de dépendance ne se limite pas à l'injection par le constructeur :

- Injection par le constructeur :

```php
class User
{
    function __construct($storage)
    {
        $this->storage = $storage;
    }

    // ...
}
```

- Injection par un setter :

```php
class User
{
    function setSessionStorage($storage)
    {
        $this->storage = $storage;
    }

    // ...
}
```

- Injection par une propriété :

```php
class User
{
    public $sessionStorage;
}

$user->sessionStorage = $storage;
```

![](https://raw.githubusercontent.com/forxer/tao-tuto/master/book/assets/dialog-information.png) En règle générale, l'injection par le constructeur est le mieux pour les dépendances requises, comme dans notre exemple, et l'injection par setter est le mieux pour les dépendances optionnelles, comme un objet de cache par exemple.

## Conteneur d'injection de dépendances

La plupart du temps, vous n'avez pas besoin de conteneur d'injection de dépendances pour  bénéficier des avantages de l'injection de dépendance.

Mais quand le projet commence à avoir beaucoup d'objets cela peut rendre service.

Un conteneur d'injection de dépendances est un objet qui sait comment instancier et configurer des objets. Et pour être en mesure de faire son travail, il doit connaître les arguments des constructeurs et les relations entre les objets.

```php
class Container
{
    public function getSessionStorage()
    {
        return new SessionStorage('SESSION_ID');
    }

    public function getUser()
    {
        return new User($this->getSessionStorage());
    }
}
```

L'utilisation de ce conteneur est assez simple :

```php
$container = new Container;
$user = $container->getUser();
```

Mais le conteneur lui-même à maintenant tout de coder en dur. Nous devons aller plus loin pour le rendre vraiment utile. Par exemple pour résoudre notre tout premier problème : changer le nom du cookie.

```php
class Container
{
    protected $parameters;

    public function __construct(array $parameters = [])
    {
        $this->parameters = $parameters;
    }

    public function getSessionStorage()
    {
        return new SessionStorage($this->parameters['session.cookie.name']);
    }

    public function getUser()
    {
        return new User($this->getSessionStorage());
    }
}
```

Maintenant nous pouvons faire ceci :

```php
$container = new Container([
    'session.cookie.name' => 'SESSION_ID'
]);

$user = $container->getUser();
```

Et si nous voulons changer la classe SessionStorage :

```php
class Container
{
    protected $parameters;

    public function __construct(array $parameters = [])
    {
        $this->parameters = $parameters;
    }

    public function getSessionStorage()
    {
        $class = $this->parameters['session.storage.class'];

        return new $class($this->parameters['session.cookie.name']);
    }

    public function getUser()
    {
        return new User($this->getSessionStorage());
    }
}
```

Maintenant nous pouvons faire ceci :

```php
$container = new Container([
    'session.cookie.name' => 'SESSION_ID',
    'session.storage.class' => 'SessionStorage'
]);

$user = $container->getUser();
```

Enfin, nous ne souhaitons pas instancier les objets à chaque fois que nous faisons appel à eux, seulement la première fois. Nous modifions donc notre containeur de cette façon :


```php
class Container
{
    static protected $shared = [];
    protected $parameters;

    public function __construct(array $parameters = [])
    {
        $this->parameters = $parameters;
    }

    public function getSessionStorage()
    {
        if (isset(self::$shared['sessionStorage'])) {
            return self::$shared['sessionStorage'];
        }

        $class = $this->parameters['session.storage.class'];

        self::$shared['sessionStorage'] =  new $class($this->parameters['session.cookie.name']);

        return self::$shared['sessionStorage'];
    }

    public function getUser()
    {
        if (isset(self::$shared['user'])) {
            return self::$shared['user'];
        }

        self::$shared['user'] = new User($this->getSessionStorage());

        return self::$shared['user']
    }
}
```

Voilà, maintenant tout est découplé et flexible. On peut tout modifier simplement en modifiant les paramètres passés au constructeur du conteneur.

Après, maintenir un conteneur à la main pour un gros projet peux vite devenir un cauchemar.

C'est là qu'interviennent les conteneur d'injection de dépendances clés en main. Dans Tao nous utilisons Pimple.

## Pimple

Pimple est un conteneur d'injection de dépendances pour PHP 5.3 ; il est au coeur de votre application Tao puisque votre classe `Application/Application` hérite de Pimple (en fait elle hérite de `Tao/Application` qui elle hérite de Pimple).

Mais reprenons notre précédent exemple en utilisant Pimple. Dans un premier temps il nous faut une instance du conteneur :

```php
use Pimple\Container;

$container = new Container();
```

Maintenant nous devons définir des services.

Dans Pimple cela peut se faire grâce aux fonctions anonymes.

```php
$container['session_storage'] = function($c) {
    return new SessionStorage('SESSION_ID');
};

$container['user'] = function($c) {
    return new User($c['session_storage']);
};
```

Notez que la fonction anonyme a accès à l'instance du conteneur courant (`$c`), permettant des références à d'autres services ou paramètres.

Pour appeller le service utilisateur c'est assez simple :

```php
$user = $container['user'];
```

Ce code ci-dessus est équivalent au code suivant :

```php
$sessionStorage = new SessionStorage('SESSION_ID');
$user = new User($sessionStorage);
```

Pour gagner en flexibilité nous allons définir des paramètres :

```php
$container['session.cookie.name'] = 'SESSION_ID';
$container['session.storage.class'] = 'SessionStorage';
```

Et modifier les définitions de service :
```php
$container['session_storage'] = function($c) {
    return new $container['session.storage.class']($container['session.cookie.name']);
};

$container['user'] = function($c) {
    return new User($c['session_storage']);
};
```

Vous pouvez maintenant changer facilement le nom du cookie ainsi que la classe SessionStorage.

Globalement nous avons :

```php
use Pimple\Container;

$container = new Container();

$container['session.cookie.name'] = 'SESSION_ID';
$container['session.storage.class'] = 'SessionStorage';

$container['session_storage'] = function($c) {
    return new $container['session.storage.class']($container['session.cookie.name']);
};

$container['user'] = function($c) {
    return new User($c['session_storage']);
};
```

Si vous utilisez les mêmes bibliothèques encore et encore, vous voudrez peut-être réutiliser certains services d'un projet au suivant ; encapsulez vos services dans un fournisseur (provider) en implémentant `Pimple\ServiceProviderInterface` :

```php
use Pimple\Container;

class SessionStorage implements Pimple\ServiceProviderInterface
{
    public function register(Container $container)
    {
        $container['session_storage'] = function() use ($container) {
            return new $container['session.storage.class']($container['session.cookie.name']);
        };
    }
}
```

Ensuite, il faut enregistrer le provider dans le conteneur :

```php
$container->register(new SessionStorage());
```

C'est ce que fait Tao pour différents services : `request`, `router`, `templating`, etc.

