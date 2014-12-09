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

Créez un fichier de contrôleur correspondant `Application/Controllers/Install.php`

```php
<?php
namespace Application\Controllers;

class Install extends BaseController
{
    public function form()
    {
        return $this->render('Install');
    }

    public function process()
    {


        $this->app['flashMessages']->success('La base de données à été modifiée.');

        return $this->redirectToRoute('install');
    }
}

```

Enfin, créez un fichier vue `Application/Views/Install.php`

```html
<?php $view->extend('Layout') ?>

<?php $view['slots']->set('title', 'Installation / Mise à jour') ?>

<h1 class="page-header">Installation / Mise à jour</h1>

<form action="<?php echo $view['router']->generate('install_process') ?>" class="form" method="post" role="form">


    <button type="submit" class="btn btn-primary">installer / mettre à jour</button>

</form>

```

