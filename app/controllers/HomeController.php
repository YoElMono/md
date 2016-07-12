<?php


use Phalcon\Mvc\Controller,
	Phalcon\Db\Result\Pdo,
	Phalcon\Tag as Tag;


class HomeController extends ControllerBase {

	private $Security ;

	public function initialize() {
		Tag::setTitle('Home');
		parent::initialize();
		$this->setJS("home");
		//$this->view->setLayout('home');
		$this->Security = new Security();
	}



	public function indexAction(){
		if( !$this->Security->securitySession() ){
			return false;
		}
		
		//Empresas
		$Empresas = array();
		$Table = new Negocios();
		$Result = $Table->find(array(
				"columns" => "id,razon_social",
			    "conditions" => "status=1 and id_cliente=".(int) $_SESSION['id_cliente'],			    	    
		));
		$NumEmpresas=count($Result);
		$this->view->NumEmpresas = $NumEmpresas;

		//Tramites
		$Tramites = array();
		$Archivos = array();
		$Table = new ViewTramites();
		$Tabla = new Documentos();
		$ResultTramites = $Table->find(array(
				"columns" => "*",
			    "conditions" => "id_cliente=".(int) $_SESSION['id_cliente'],			    	    
		));
		$NumTramites=count($ResultTramites);
		$this->view->NumTramites = $NumTramites;

		foreach($ResultTramites as $value){
			$Tramites[] = array("id" => $value->id ,
				"id_empresa" => $value->id_empresa,
				"empresa" => $value->empresa,
				"status" => $value->status,
				
				
				);
			

		}
		$this->view->Tramites = $Tramites;

		//echo "<pre>";print_r($Tramites);exit();






		$this->ajaxBody("Inicio");
		$this->setHeaderMenu("Inicio" , "Inicio" , "home" , "");
		$this->setActionSubMenu(0, "home/slider.phtml");
		$this->view->session = $_SESSION['PermisosUser']['sub_home_home'];
		return true;


		
		








	}


	




}