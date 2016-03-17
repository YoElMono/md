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
		
		$this->ajaxBody("Inicio");
		$this->setHeaderMenu("Inicio" , "Inicio" , "home" , "");
		$this->setActionSubMenu(0, "home/slider.phtml");
		$this->view->session = $_SESSION['PermisosUser']['sub_home_home'];
		return true;
	}


	public function qrAction($trama){
		//print_r($trama);
		//exit();
		$this->view->disable();

		$Result = array();
		if( isset($trama) && !empty($trama) ){
			$Explode = explode("," , $trama);
			//print_r($Explode);
			//exit();
			$email = trim($Explode[2]);
			$cadena=trim($Explode[1]);

			$Usuarios = Usuarios::findFirst("sha1(email)='".$email."'");
			if(!$Usuarios){
				$Data="error";	
			}else{
				//echo "<pre>";
				//print_r($Usuarios);
				//exit();

				$Data="<div><h2>Bienvenido</h2> </div><div style='marging-top:20px'><h3>".$Usuarios->nombre." ".$Usuarios->apellido."</h3></div><div style='color:#900; marging-top:50px'><h2>Checking Exitoso !!</h2> </div>";	

			}

		}else{

			$Data="error";	


		}
		echo   $Data;


			


		
		//$this->response->setContentType('application/json', 'UTF-8');
       // $response = new \Phalcon\Http\Response();
        //$response->setContent(json_encode($Data));
        //return $response;




	}	




}