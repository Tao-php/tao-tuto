# Blogy - mise en place

Dans cette partie nous allons essayer d'utiliser tous ce que nous avons précédement vu et même aller un peu plus loin en créant un mini-blog.

## Installation

Doctrine DBAL met à disposition un puissant gestionnaire de shéma. Nous allons l'utiliser et créer une page d'installation.


Ajoutez tout dans le fichier `/Application/Config/routes.yml` deux nouvelles routes :

```yml
install:
  path: /install
  defaults:
    _controller: 'Install::form'
  requirements:
    _method:  GET

install_process:
  path: /install
  defaults:
    _controller: 'Install::process'
  requirements:
    _method:  POST
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

<h1>Bienvenue !</h1>
```

