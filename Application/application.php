<?php
namespace Application;

use Tao\Application as TaoApplication;

class Application extends TaoApplication
{
	public function __construct($loader, array $config = [], array $classMap = [])
	{
		parent::__construct($loader, $config, __DIR__, $classMap);

		$this['session']->start();
	}
}
