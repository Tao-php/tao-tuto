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
