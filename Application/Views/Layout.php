<!doctype html>
<html>
	<head>
		<title>
			<?php $view['slots']->output('title', 'Titre par défaut') ?>
		</title>
	</head>
	<body>
		<?php echo $view->render('Common/messages') ?>

		<?php $view['slots']->output('_content') ?>

		<?php if ($app['debug']) : ?>
			<?php echo $view->render('Common/DebugInfos') ?>
		<?php endif ?>

	</body>
</html>
