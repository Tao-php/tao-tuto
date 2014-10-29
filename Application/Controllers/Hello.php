<?php
namespace Application\Controllers;

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
