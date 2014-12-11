<?php $view->extend('Layout') ?>

<?php $view['slots']->set('title', 'Installation / Mise à jour') ?>


<h1 class="page-header">Installation / Mise à jour</h1>
<form action="<?php echo $view['router']->generate('install_process') ?>" class="form" method="post" role="form">


    <button type="submit" class="btn btn-primary">installer / mettre à jour</button>

</form>
