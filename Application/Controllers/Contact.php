<?php
namespace Application\Controllers;

use Respect\Validation\Validator as v;

class Contact extends BaseController
{
	protected $email;
	protected $subject;
	protected $message;

	public function form()
	{
		return $this->render('Contact', [
			'email' => $this->email,
			'subject' => $this->subject,
			'message' => $this->message
		]);
	}

	public function process()
	{
		$this->email = $this->app['request']->request->get('email');
		$this->subject = $this->app['request']->request->get('subject', 'email depuis la page contact');
		$this->message = $this->app['request']->request->get('message');

		if (empty($this->email)) {
			$this->app['instantMessages']->error('Vous devez saisir une adresse email.');
		}
		elseif (!v::email()->validate($this->email)) {
			$this->app['instantMessages']->error('Vous devez saisir une adresse email valide.');
		}

		if (empty($this->message)) {
			$this->app['instantMessages']->error('Vous devez saisir un message.');
		}

		# si on as une erreur, on ré-affiche le formulaire
		if ($this->app['instantMessages']->hasError()) {
			return $this->form();
		}

		// envoi de l'email ...

		$this->app['flashMessages']->success('Votre email a été envoyé.');

		return $this->redirectToRoute('contact');
	}
}
