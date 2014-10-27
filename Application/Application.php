<?php
namespace Application;

use Tao\Application as TaoApplication;

use Symfony\Component\Templating\Asset\PathPackage;

class Application extends TaoApplication
{
	public function __construct($loader, array $config = [], array $classMap = [])
	{
		parent::__construct($loader, $config, __DIR__, $classMap);

		$this['session']->start();

		$this['templating']->get('assets')->addPackage('css',
			new PathPackage($this['app_url'] . $this['assets_url'] . '/css'));

		$this['templating']->get('assets')->addPackage('js',
			new PathPackage($this['app_url'] . $this['assets_url'] . '/js'));

		$this['templating']->get('assets')->addPackage('img',
			new PathPackage($this['app_url'] . $this['assets_url'] . '/img'));
	}
}
