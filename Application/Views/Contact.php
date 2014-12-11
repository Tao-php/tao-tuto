<?php $view->extend('Layout') ?>

<?php $view['slots']->set('title', 'Contact') ?>

<h1 class="page-header">Contact</h1>

<form action="<?php echo $view['router']->generate('contact_process') ?>" class="form-horizontal" method="post" role="form">

    <div class="form-group">
        <label for="mail" class="col-sm-2 control-label">Adresse email</label>
        <div class="col-sm-10">
            <input name="email" id="email" type="email" size="50" maxlength="255" value="<?php
            echo $view->e($email) ?>" placeholder="Saisissez votre adresse email" class="form-control" required>
        </div>
    </div>

    <div class="form-group">
        <label for="subject" class="col-sm-2 control-label">Sujet</label>
        <div class="col-sm-10">
            <input name="subject" id="subject" type="text" size="50" maxlength="255" value="<?php
            echo $view->e($subject) ?>" placeholder="Saisissez un sujet" class="form-control">
        </div>
    </div>

    <div class="form-group">
        <label for="message" class="col-sm-2 control-label">Message</label>
        <div class="col-sm-10">
            <textarea name="message" id="message" cols="37" rows="7" placeholder="Saisissez un message" class="form-control" required><?php
            echo $view->e($message) ?></textarea>
        </div>
    </div>

    <div class="form-group">
        <div class="col-sm-offset-2 col-sm-10">
            <button type="submit" class="btn btn-primary">envoyer</button>
        </div>
    </div>
</form>
