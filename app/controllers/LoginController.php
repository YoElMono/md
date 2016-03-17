<?php

use Phalcon\Mvc\Controller,
	Phalcon\Tag as Tag,
	Phalcon\Mvc\Model\Criteria,
    Phalcon\Http\Request,
	Phalcon\Mvc\Model;



class LoginController extends ControllerBase {

	public $error;
	private $Security ;
	private $Redireccion ;


	public function initialize() {
		Phalcon\Tag::setTitle('Login');
		parent::initialize();
		$this->setJS("login");
		$this->Security = new Security();
		$this->Redireccion = new Redireccion();
	}


	public function indexAction() {
		$this->Security->yesSession();		
		if ($this->request->isPost()){
			$uname = $this->request->getPost('usuario', array('string', 'striptags'));
			$password = $this->request->getPost('pass', array('string', 'striptags'));
			$this->validateloginAction($uname, $password);
			return true;
		} else {
			$this->view->setLayout('login');
		}
	}


	public function fbAction() {
		$this->Security->yesSession();		
		$Msj = array("status" => 0 , "msj" => "");
		if( !isset($_SESSION['facebook']) ){
			$Facebook = new FB();
			$domain = $this->getDomain();
			//echo $domain; exit();
			$Facebook->Configs["url"] = $domain . "login/fb/";
			$Facebook->connect();
			$Facebook->Url;
			header('Location: ' . $Facebook->Url);
		} else {
			$this->view->setLayout('login');
			$Facebook = new FB();
			$Info = $Facebook->getInfoUser();
			$this->validateloginAction($Info["id"] , "lomeli_" . $Info["id"] , 1);
		}
		return true;
	}





	public function logoutAction(){
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


	public function validateloginAction($uname="", $password="" , $type=0){
		if ($uname == null) {
			echo '<div class="res_login">El usuario es necesario.</div>';
		} else if ($password == null) {
			echo '<div class="res_login">El password es necesario.</div>';
		} else {
			if( $type == 0 ){
						
				$pass =sha1($password); 			

				$user = Usuarios::findfirst("user='$uname' AND password='$pass' AND status=1 and tipo!='APP'");	
				//echo "<pre>";
				//print_r($user);
				//exit();
			} else {
				$user = Usuarios::findfirst("id_fb='$uname' AND status=1");	
			}
			if (!$user) {			
			
					echo '<div class="res_login">Datos incorrectos.</div>';
				
			} else {
				
				$ultimo_acceso = date("Y-m-d H:i:s");
				if( isset($_SESSION['facebook']) ){
					$Token = isset($_SESSION['facebook']);
					$user->__set('ultimo_acceso' , $ultimo_acceso , 'token' , $Token);
				} else {
					$user->__set('ultimo_acceso' , $ultimo_acceso);
				}
				$updatelogin = $user->save();
				if (!$updatelogin) {
					echo '<div class="res_login">Ocurrio un error intente de nuevo.</div>';
				} else {							

					$this->session->set("id",$user->__get('id'));
					$this->session->set("id_fb",$user->__get('id_fb'));
					$this->session->set("nombre",$user->__get('nombre'));
					$this->session->set("apellido",$user->__get('apellido'));
					$this->session->set("email",$user->__get('email'));
					$this->session->set("user",$user->__get('user'));
					$this->session->set("tipo",$user->__get('tipo'));
					$this->session->set("id_negocio",$user->__get('id_negocio'));
					$this->setIP();
					$this->setMenu();
					$this->response->redirect($this->view->PrimerMenu);
					$this->view->disable();


				}
			}
		}
	}


	public function setIP(){
		return true;
		$LogsIps = new LogsIps();
		$LogsIps->assign(array(
			"id_usuario" => (int) $_SESSION["id"] , 
			"ip" => trim($this->get_client_ip()) ,
			"fecha" => date("Y-m-d H:i:s")
		));
		$LogsIps->save();
	}



	function get_client_ip() {
	    $ipaddress = '';
	    if ($_SERVER['HTTP_CLIENT_IP'])
	        $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
	    else if($_SERVER['HTTP_X_FORWARDED_FOR'])
	        $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
	    else if($_SERVER['HTTP_X_FORWARDED'])
	        $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
	    else if($_SERVER['HTTP_FORWARDED_FOR'])
	        $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
	    else if($_SERVER['HTTP_FORWARDED'])
	        $ipaddress = $_SERVER['HTTP_FORWARDED'];
	    else if($_SERVER['REMOTE_ADDR'])
	        $ipaddress = $_SERVER['REMOTE_ADDR'];
	    else
	        $ipaddress = 'UNKNOWN';
	    return $ipaddress;
	}




}
