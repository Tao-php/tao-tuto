<?php
namespace Application\Controllers;

class Home extends BaseController
{
	public function show()
	{
		return $this->render('Home');
	}
}
