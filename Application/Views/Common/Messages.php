<?php # affichage des éventuels messages d'erreurs
	if ($app['messages']->hasError()) : ?>
		<ul class="messages error">
			<?php foreach ($app['messages']->getError() as $message) : ?>
			<li><?php echo $message ?></li>
			<?php endforeach; ?>
		</ul>
	<?php endif; ?>

	<?php # affichage des éventuels messages d'avertissements
	if ($app['messages']->hasWarning()) : ?>
		<ul class="messages warning">
			<?php foreach ($app['messages']->getWarning() as $message) : ?>
			<li><?php echo $message ?></li>
			<?php endforeach; ?>
		</ul>
	<?php endif; ?>

	<?php # affichage des éventuels messages de confirmation
	if ($app['messages']->hasSuccess()) : ?>
		<ul class="messages success">
			<?php foreach ($app['messages']->getSuccess() as $message) : ?>
			<li><?php echo $message ?></li>
			<?php endforeach; ?>
		</ul>
	<?php endif; ?>

	<?php # affichage des éventuels messages d'information
	if ($app['messages']->hasInfo()) : ?>
		<ul class="messages info">
			<?php foreach ($app['messages']->getInfo() as $message) : ?>
			<li><?php echo $message ?></li>
			<?php endforeach; ?>
		</ul>
	<?php endif; ?>
