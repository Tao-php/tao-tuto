<?php $view->extend('Layout') ?>

<?php $view['slots']->set('title', 'Contact') ?>

<form action="" method="post">
	<p><label for="mail">Adresse email</label><br>
	<input name="email" id="email" type="text" size="50" maxlength="255" value="<?php echo $view->e($email) ?>"></p>

	<p><label for="subject">Sujet</label><br>
	<input name="subject" id="subject" type="text" size="50" maxlength="255" value="<?php echo $view->e($subject) ?>"></p>

	<p><label for="message">Message</label><br>
	<textarea name="message" id="message" cols="37" rows="7"><?php echo $view->e($message) ?></textarea></p>

	<p><input type="submit" class="submit" value="envoyer"></p>
</form>
