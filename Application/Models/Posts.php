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
