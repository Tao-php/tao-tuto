<!doctype html>
<html>
	<head>
		<title>
			<?php $view['slots']->output('title', 'Titre par défaut') ?>
		</title>
		<link rel="stylesheet" type="text/css" href="<?php echo $view['assets']->getUrl('app.css', 'css') ?>">
	</head>
	<body>
		<?php echo $view->render('Common/messages') ?>

		<?php $view['slots']->output('_content') ?>

		<?php if ($app['debug']) : ?>
			<?php echo $view->render('Common/DebugInfos') ?>
		<?php endif ?>

		<script src="<?php echo $view['assets']->getUrl('app.js', 'js') ?>"></script>
	</body>
</html>
