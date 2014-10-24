<?php
namespace Application\Controllers;

class Contact extends BaseController
{
	public function form()
	{
		return $this->render('Contact');
	}
}
