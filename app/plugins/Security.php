<?php
use Phalcon\Events\Event,
	Phalcon\Mvc\User\Plugin,
	Phalcon\Acl;

class Security extends Plugin
{



	public function notSession(){
		$Redireccion = new Redireccion();
		if( $this->session->has("id") ){
			//return $Redireccion->forward('home/');
			//echo $this->session->get("PrimerMenu");
			//exit();
			return $this->response->redirect($this->session->get("PrimerMenu"));
		} else {
			//return $Redireccion->forward('login/');
			return $this->response->redirect('login/');
		}
	}

	public function yesSession(){
		$Redireccion = new Redireccion();
		if( $this->session->has("id") ){
			//return $Redireccion->forward('home/');
			$this->response->redirect($this->session->get("PrimerMenu"));
			//$this->view->disable();
		}
	}


	public function securitySession(){
		if( !$this->session->has("id") ){
			$this->response->redirect('login/');
			$this->view->disable();
			return false;
		}
		return true;
	}









}