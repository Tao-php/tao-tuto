<!doctype html>
<html>
	<head>
		<title>
			<?php $view['slots']->output('title', 'Titre par défaut') ?>
		</title>

		<!-- Bootstrap -->
		<link rel="stylesheet" type="text/css" href="<?php echo $view['assets']->getUrl('bootstrap/dist/css/bootstrap.min.css', 'components') ?>">
		<link rel="stylesheet" type="text/css" href="<?php echo $view['assets']->getUrl('bootstrap/dist/css/bootstrap-theme.min.css', 'components') ?>">

		<link rel="stylesheet" type="text/css" href="<?php echo $view['assets']->getUrl('fontawesome/css/font-awesome.min.css', 'components') ?>">

		<link rel="stylesheet" type="text/css" href="<?php echo $view['assets']->getUrl('app.css', 'css') ?>">

		<!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
		<!--[if lt IE 9]>
			<script src="<?php echo $view['assets']->getUrl('html5shiv/dist/html5shiv.min.js', 'components') ?>"></script>
			<script src="<?php echo $view['assets']->getUrl('respond/dest/respond.min.js', 'components') ?>"></script>
		<![endif]-->
	</head>
	<body>
		<?php echo $view->render('Common/Navbar') ?>

		<div class="container">
			<?php echo $view->render('Common/messages') ?>

			<?php $view['slots']->output('_content') ?>

			<?php if ($app['debug']) : ?>
				<?php echo $view->render('Common/DebugInfos') ?>
			<?php endif ?>
		</div>

		<script src="<?php echo $view['assets']->getUrl('jquery/dist/jquery.min.js', 'components') ?>"></script>
		<script src="<?php echo $view['assets']->getUrl('bootstrap/dist/js/bootstrap.min.js', 'components') ?>"></script>

		<script src="<?php echo $view['assets']->getUrl('app.js', 'js') ?>"></script>
	</body>
</html>
