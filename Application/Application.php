<?php
namespace Application;

use Application\Provider\FinderServiceProvider;
use Symfony\Component\Templating\Asset\PathPackage;
use Tao\Application as TaoApplication;

class Application extends TaoApplication
{
	public function __construct($loader, array $config = [], array $classesMap = [])
	{
		parent::__construct($loader, $config, __DIR__, $classesMap);

		$this->register(new FinderServiceProvider());

		$this['session']->start();

		$this['templating']->get('assets')->addPackage('css',
			new PathPackage($this['app_url'] . $this['assets_url'] . '/css'));

		$this['templating']->get('assets')->addPackage('js',
			new PathPackage($this['app_url'] . $this['assets_url'] . '/js'));

		$this['templating']->get('assets')->addPackage('img',
			new PathPackage($this['app_url'] . $this['assets_url'] . '/img'));

		$this['templating']->get('assets')->addPackage('components',
			new PathPackage($this['app_url'] . $this['components_url']));
	}
}
