# Base de données

Une application qui n’utilise pas de base de données, c’est rare. Nous allons voir ici comment communiquer avec une base de données dans Tao.

## Préparation et configuration

Dans premier temps, nous allons créer une base de données avec une simple table.

J’ai choisi d’utiliser une base de donnée MySQL nommée "tutotao" dans laquelle j’ai créé la table suivante :

```
CREATE TABLE IF NOT EXISTS `posts` (

  `id`          int(11)         NOT NULL,
  `title`       varchar(255)    NOT NULL,
  `content`     text            NOT NULL,

  PRIMARY KEY (`id`)

) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
```

Après avoir créé cette table, ouvrez le fichier de configuration `/Application/Config/prod.php` et ajoutez les paramètres suivant :

```php
    'database.connection' => [
        'driver' => 'pdo_mysql',
        'host' => 'localhost',
        'dbname' => 'tutotao',
        'user' => 'root',
        'password' => '',
        'charset' => 'utf8'
    ],
```

Évidement vous devez personnaliser ces paramètres selon vos besoins.

Reste maintenant à ajouter la bibliothèque de gestion de base de données.
Nous utiliseront Doctrine DBAL parce qu’un service est prévu dans Tao
et que les paramètres de connexion sont aussi prévus pour elle.

Nous allons donc ré-utiliser ce que nous avons précédement vu :
l’ajout d’un composant externe et l’utilisation d’un nouveau service.

Ajoutez au fichier `composer.json` la dépendance suivante :

```json
    ...
        "doctrine/dbal" : "~2.5",
    ...
```

Lancez composer (mais pas trop loin)

```
composer update
```

Et finalement, enregistrez le service dans le constructeur de l’application :


```php
//...

use Tao\Provider\DatabaseServiceProvider;

//...
        $this->register(new DatabaseServiceProvider());
//...
```

Vous avez maintenant à disposition le service `$app['db']` qui est une instance de connexion à la base de données.

## Utilisation simple

La façon la plus simple pour executer une requête est la suivante :

```php
$stmt = $app['db']->query('SELECT * FROM posts');

while ($row = $stmt->fetch()) {
    echo $row['title'];
}
```

Mais ce n’est pas la meilleure façon de procéder pour plusieurs raisons que vous trouverez dans la documentation de Doctrine DBAL.

Il vaux mieux utiliser les requêtes préparées et les paramètres dynamiques, exemple de requête préparée :

```php
$stmt = $app['db']->prepare('SELECT * FROM posts');
$stmt->execute();

while ($row = $stmt->fetch()) {
    echo $row['title'];
}
```

La même avec un paramètre dynamique :

```php
$stmt = $app['db']->prepare('SELECT * FROM posts WHERE id = ?');
$stmt->bindValue(1, $id);
$stmt->execute();
```

Il est aussi possible d’utiliser des paramètres dynamiques nommés :

```php
$stmt = $app['db']->prepare('SELECT * FROM posts WHERE id = :post_id');
$stmt->bindValue('post_id', $id);
$stmt->execute();
```

Utiliser la méthode `prepare()` est utile si vous devez utiliser le même "statement" pour plusieurs requêtes,
sinon il existe deux autres méthodes pour simplifier l’écriture :

- `executeQuery($sql, $params, $types)` sera utilisée pour les SELECT
- `executeUpdate($sql, $params, $types)` sera utilisée pour les UPDATE, DELETE et INSERT

Ainsi, le dernier exemple devient :

```php
$stmt = $app['db']->executeQuery('SELECT * FROM posts WHERE id = :post_id', ['post_id' => $id]);
```

## Utilisation sympa

Pour que ce soit encore plus sympa, on peux écrire ceci :

```php
$posts = $app['db']->fetchAll('SELECT * FROM posts');

foreach ($posts as $post) {
    echo $post['title'];
}
```

Ou bien encore cela :

```php
$post = $app['db']->fetchAssoc('SELECT * FROM posts WHERE id = :post_id', ['post_id' => $id]);

echo $post['title'];
```

Pour ajouter, modifier et supprimer des lignes vous pouvez utilisez `executeUpdate()` ; par exemple :

```php
$app['db']->executeQuery('DELETE FROM posts WHERE id = :post_id', ['post_id' => $id]);
```

Mais il y a plus simple :

```php
$app['db']->delete('posts', ['post_id' => $id]);
```

Idem pour ajouter :

```php
$app['db']->insert('posts', [
    'title' => 'bla bla',
    'content' => 'Lorem ipsum...'
]);
```

Et mettre à jour :

```php
$app['db']->update('posts', [
    'title' => 'bla bla',
    'content' => 'Lorem ipsum...'
], ['id' => $id]);
```

Voilà, Doctrine DBAL permet d’executer des requêtes préparées de façon très simple.


![](https://raw.githubusercontent.com/forxer/tao-tuto/master/book/assets/text-html.png)
[Documentation de Doctrine DBAL sur la récupération et la manipulation des données](http://doctrine-dbal.readthedocs.org/en/latest/reference/data-retrieval-and-manipulation.html)

## Constructeur de requêtes SQL (SQL Query Builder)

Le constructeur de requêtes SQL est un service qui est appellé de cette façon : `$app['qb']`

Vous pouvez l'utiliser par exemple de cette façon :

```php
$queryBuilder = $app['qb'];
$queryBuilder
    ->select('title', 'content')
    ->from('posts')
    ->where('id = :post_id')
    ->setParameter('post_id', $id)
;
```

Cet outil est très puissant et utile pour construire une requête au fur
et à mesure de l'execution du programme et en fonction du contexte.

```php
$queryBuilder = $app['qb'];
$queryBuilder
    ->select('title', 'content')
    ->from('posts')
    ->where('id = :post_id')
    ->setParameter('post_id', $id)
    ->orderBy('id', 'ASC')
;

if ($critere['foo'])
{
    $queryBuilder
        ->AndWhere('foo = :foo')
        ->setParameter('foo', $critere['foo'])
        ->addOrderBy('foo', 'ASC NULLS FIRST')
    ;
}

if ($critere['bar'])
{
    $queryBuilder
        ->AndWhere('bar = :bar')
        ->setParameter('bar', $critere['bar'])
        ->addOrderBy('bar', 'DESC')
    ;
}
```

Je ne vais pas détailler toutes les méthodes existantes ici, la documentation est suffisament détaillée.

![](https://raw.githubusercontent.com/forxer/tao-tuto/master/book/assets/text-html.png)
[Documentation du Query Builder de Doctrine DBAL](http://doctrine-dbal.readthedocs.org/en/latest/reference/query-builder.html)

## Model

Afin de simplifier certaines opérations, Tao propose une classe "Model" qui peut être étendue.

Malheureusement ce système a été développé à la hâte et souffre d'importants défauts. Par exemple
l'impossibilité de définir une clé primaire sur deux champs (oooouuuuuh).

Mais survolons quand même rapidement ce qu'il est possible de faire.

Créons donc une classe dans `/Application/Models/Posts.php`

```php
<?php
namespace Application\Models;

use Tao\Database\Model;

class Posts extends Model
{
    public function init()
    {
        $this->setTable('posts');

        $this->setAlias('p');

        $this->setColumns([
            'id',
            'title',
            'content'
        ]);

        $this->setPrimaryKey('id');
    }
}

```

Maintenant il est possible de l’utiliser soit en l’instanciant directement,
soit en utilisant l’utilitaire `$app->getModel('Posts')`.

Différentes méthodes utilitaires seront alors disponibles.


```php
$Posts = $app->getModel('Posts');

$Posts->insert([
    'title' => $title,
    'content' => $content
]);

$Posts->update([
    'title' => $title,
    'content' => $content
], $id);

if ($Posts->has($id)) {
    $Posts->delete($id);
}
```

Aussi, il est possible d'utiliser les modèles dans le query builder étendu de Tao.

```php
$Posts = $app->getModel('Posts');

$queryBuilder = $app['qb'];
$queryBuilder
    ->selectModel($Posts)
    ->where('id = :post_id')
    ->setParameter('post_id', $id)
;

$pager = $queryBuilder->getPager($Posts, $iMaxPerPage, $iCurrentPage);

```

## Conclusion

Nous avons survolé les trois possibilités pour communiquer avec une base de données.
- DBAL
- QueryBuilder
- Model

Les plus sûrs sont résolument les deux premières, la dernière peut offrir de la souplesse
et de la simplicité, mais induit parfois des limitations.
