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
		
		
		if ($_SESSION['tipo']=='Cliente') {	
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
				$Table = new ViewTramites();				
				$ResultTramites = $Table->find(array(
						"columns" => "*",
					    "conditions" => "id_cliente=".(int) $_SESSION['id_cliente'],			    	    
				));
				$NumTramites=count($ResultTramites);
				$this->view->NumTramites = $NumTramites;

				foreach($ResultTramites as $value){


					//Archivos
					$Archivos = array();				
					$Tabla = new Documentos();			
					$Resultarchivo = $Tabla->find(array(
							"columns" => "nombre,archivo",
						    "conditions" => "status=1 and id_negocio=".(int) $value->id_empresa ,			    	    
					));
					foreach($Resultarchivo as $valor){					
						 $Archivos[] = array(
										"nombre" => $valor->nombre,
										"archivo" => $valor->archivo,										
										);	
					}	


					//Pagos
					$Pagos = array();				
					$Tablapagos = new Movimientos();			
					$ResultPagos = $Tablapagos->find(array(
							"columns" => "id,fecha,monto",
						    "conditions" => "status=1 and tipo='Pago' and id_tramite=".(int) $value->id,			    	    
					));
					foreach($ResultPagos as $valorpago){					
						 $Pagos[] = array(
										"fecha" => $valorpago->fecha,
										"monto" => $valorpago->monto,										
										);	
					}



					$Tramites[] = array("id" => $value->id ,
						"id_empresa" => $value->id_empresa,
						"empresa" => $value->empresa,
						"costo" => $value->costo,
						"status" => $value->status,	
						"archivos"=>$Archivos,
						"pagos"=>$Pagos,
						);			
				}
				$this->view->Tramites = $Tramites;
				//echo "<pre>";print_r($Tramites);exit();
		}


		if ($_SESSION['tipo']=='Administrador') {	
				//Empresas
				$Empresas = array();
				$Table = new Negocios();
				$Result = $Table->find(array(
						"columns" => "id,razon_social",
					    "conditions" => "status=1",			    	    
				));
				$NumEmpresas=count($Result);
				$this->view->NumEmpresas = $NumEmpresas;
				//clientes
				$Clientes = array();
				$Table = new Clientes();
				$Result = $Table->find(array(
						"columns" => "id",
					    "conditions" => "status=1",			    	    
				));
				$NumClientes=count($Result);
				$this->view->NumClientes = $NumClientes;

				

				//Tramites
				$Tramites = array();
				$Table = new Tramites();
				$ResultTramites = $Table->find(array(
						"columns" => "id",
					    "conditions" => "",			    	    
				));
				$NumTramites=count($ResultTramites);
				$this->view->NumTramites = $NumTramites;
				
				//echo "<pre>";print_r($Tramites);exit();
		}


		






		$this->ajaxBody("Inicio");
		$this->setHeaderMenu("Inicio" , "Inicio" , "home" , "");
		$this->setActionSubMenu(0, "home/slider.phtml");
		$this->view->session = $_SESSION['PermisosUser']['sub_home_home'];
		return true;


		
		








	}


	




}