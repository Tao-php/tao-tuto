<?php
namespace Application;

use Tao\Application as TaoApplication;

class Application extends TaoApplication
{
	public function __construct($loader, array $config = [])
	{
		parent::__construct($loader, $config, __DIR__);
	}
}
