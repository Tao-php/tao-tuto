<?php $view->extend('Layout') ?>

<?php $view['slots']->set('title', 'Hello') ?>

<p>Hello <?php echo $view->e($name_from_controller) ?>!</p>
