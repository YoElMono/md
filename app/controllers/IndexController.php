<?php



use Phalcon\Mvc\Controller,
	Phalcon\Db\Result\Pdo,
	Phalcon\Tag as Tag,
	Phalcon\Db\Column;


class IndexController extends ControllerBase {
	public function initialize() {
		Tag::setTitle('');
		parent::initialize();
		$this->view->setLayout("clear");
	}

	public function indexAction() {
		if( $_SESSION["PrimerMenu"] == "" ){
			$this->session->remove("id");
			$this->session->remove("id_fb");
			$this->session->remove("nombre");
			$this->session->remove("apellido");
			$this->session->remove("email");
			$this->session->remove("user");
			$this->session->remove("tipo");
			$this->session->remove("menu");
			$this->session->remove("PrimerMenu");
			$this->session->destroy();
			$this->response->redirect('login/');
			$this->view->disable();
			return false;
		}
		$security = new Security();
		$security->notSession();
		$this->view->disable();
		return false;
	}






}

