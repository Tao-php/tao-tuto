<div class="navbar navbar-inverse" role="navigation">
	<div class="container">
		<div class="navbar-header">
			<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target=".navbar-collapse">
				<span class="sr-only">Menu</span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			</button>
			<a class="navbar-brand" href="<?php echo $view['router']->generate('home') ?>">Tutoriel Tao</a>
		</div>
		<div class="collapse navbar-collapse">
			<ul class="nav navbar-nav">
				<li<?php if ($app['request']->attributes->get('_route') == 'home') : ?> class="active"<?php endif ?>><a href="<?php echo $view['router']->generate('home') ?>"><i class="fa fa-home fa-fw"></i>&nbsp;Accueil</a></li>
				<li<?php if ($app['request']->attributes->get('_route') == 'hello') : ?> class="active"<?php endif ?>><a href="<?php echo $view['router']->generate('hello') ?>"><i class="fa fa-comment fa-fw"></i>&nbsp;Hello</a></li>
				<li<?php if (in_array($app['request']->attributes->get('_route'), ['contact', 'contact_process'])) : ?> class="active"<?php endif ?>><a href="<?php echo $view['router']->generate('contact') ?>"><i class="fa fa-envelope fa-fw"></i>&nbsp;Contact</a></li>
			</ul>
		</div>
	</div>
</div>
