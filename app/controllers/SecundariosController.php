<?php


use Phalcon\Mvc\Controller,
	Phalcon\Db\Result\Pdo,
	Phalcon\Tag as Tag,
	Phalcon\Db\Column;




class SecundariosController extends ControllerBase {
	
	private $Security ;
	var $Data = array();

	public function initialize() {
		Tag::setTitle('Secundarios');
		parent::initialize();
		$this->setJS("secundarios");
		$this->Security = new Security();
		$this->Modulo = "sub_configuraciones_secundarios";
		$this->Controller = "secundarios";
		$this->Path = __DIR__ . "/../../public/tmp/" ;
		$this->UploadAction = false;
	}


	public function getData(){
		$this->Data = array(
			"columns" => array(
				array("id" , "No" , "5%" , Column::BIND_PARAM_INT , 10),
				array("padre" , "Padre" , "15%" , Column::BIND_PARAM_INT , 30),
				array("nombre" , "Nombre" , "15%" , Column::BIND_PARAM_STR , 30),
				array("user" , "Usuario" , "15%" , Column::BIND_PARAM_STR , 30),
				array("pass" , "Password" , "15%" , Column::BIND_PARAM_STR , 30),
				array("tipo" , "Tipo" , "15%" , Column::BIND_PARAM_STR , 30),
				array("status" , "Estatus" , "12%" , Column::BIND_PARAM_INT , 10),
				array("" , "Acciones" , "8%" , $this->newButton($this->Controller . "/new/") ),
			),
			"data" => array(),
			"configs" => array(
				"limit" => 10,
				"slug" => $this->Controller."/?",
				"controller" => $this->Controller,
				"columns" => "*",
				"where" => "",
				"name" => $this->Controller,
				"table" => "ViewSecundarios",
				"new" => (int) $_SESSION["PermisosUser"][$this->Modulo . "_new"],
				"edit" => (int) $_SESSION["PermisosUser"][$this->Modulo . "_edit"],
				"delete" => (int) $_SESSION["PermisosUser"][$this->Modulo . "_delete"],
			),
		);		
	}



	public function excelAction(){
		if( !$this->Security->securitySession() ){
			return false;
		}
		if( $_SESSION["PermisosUser"][$this->Modulo] == 0 ){
			$this->response->redirect($this->session->get("PrimerMenu"));
			return false;
		}		
		$this->getData();
		$this->Data["configs"]["excel_title"] = "Listado de Usuarios Secundarios";
		$Grid = new Grid();
		$Grid->Data = $this->Data;
		$Grid->Excel = 1;
		$Grid->Start();
		$Grid->Dump();
		return false;
	}


	public function indexAction(){
		if( !$this->Security->securitySession() ){
			return false;
		}
		if( $_SESSION["PermisosUser"][$this->Modulo] == 0 ){
			$this->response->redirect($this->session->get("PrimerMenu"));
			return false;
		}
		//echo "<pre>"; print_r($_SESSION); echo "</pre>"; exit();
		$this->ajaxBody("Usuarios Secundarios");
		$this->setHeaderMenu("Configuraciones" , "Usuarios Secundarios" , $this->Controller , "");
		$this->getData();
		$Grid = new Grid();
		$Grid->Data = $this->Data;
		$Grid->Start();
		$this->view->htmlGrid = $Grid->getGrid();
		$this->view->msjResponse = "";
		if( $this->session->has("mensajeReturn") ){
			$this->view->msjResponse = $this->session->get("mensajeReturn");
			$this->session->remove("mensajeReturn");
		}
		return false;
	}




	public function getUsuariosForm(){
		$Data = array();
		$Table = new Usuarios();
		$Result = $Table->find(array(
		    "conditions" => "status=1 AND id!=1",
		    "order" => "nombre ASC"
		));
		foreach($Result as $value){
			$Data[] = array("id" => $value->id , "name" => $value->nombre);
		}
		return $Data;
	}






	public function newAction(){
		if( !$this->Security->securitySession() ){
			return false;
		}
		if( $_SESSION["PermisosUser"][$this->Modulo] == 0 ){
			$this->response->redirect($this->session->get("PrimerMenu"));
			return false;
		}
		$this->view->msjResponse = "";
		$this->view->jsResponse = "";
		if($this->request->isPost()){
			$this->clearPost();
			$_POST["ultimo_acceso"] = date("Y-m-d H:i:s");
			$this->clearPostInt(array("status"));
			$Tabla = new UsuariosSecundarios();
			$Result = $Tabla->find(array(
				"columns" => "id",
			    "conditions" => "user=:user:",
			    "bind" => array("user" => $this->request->getPost("user" , "string")),
			    "bindTypes" => array("user" => Column::BIND_PARAM_STR),
			    "limit" => 1
			));
			if( count($Result) <= 0 ){
				$Tabla->assign($this->request->getPost());
				if($Tabla->save()){
					$this->session->set("mensajeReturn" , $this->msjReturn("&Eacute;xito" , "Se guardo el registro correctamente." , "success"));
					$this->response->redirect($this->Controller."/");
					$this->view->disable();
					return false;
				}
				$this->view->msjResponse = $this->msjReturn("Error" , "Ocurrio un error , intente de nuevo." , "error");
				$this->view->jsResponse = $this->setValueData("formulario_registro" , $_POST);
			} else {
				$this->view->msjResponse = $this->msjReturn("Error" , "Existe un registro con los mismos datos." , "error");
				$this->view->jsResponse = $this->setValueData("formulario_registro" , $_POST);
			}
		}
		$this->view->Usuarios = $this->getUsuariosForm();
		$this->ajaxBody("Usuarios Secundarios");
		$this->setHeaderMenu("Configuraciones" , "Usuarios Secundarios" , $this->Controller , "Nuevo");
		$this->view->formAction = $this->Controller."/new/";
		return false;
	}






	public function editAction($idPost=0){
		if( !$this->Security->securitySession() ){
			return false;
		}
		if( $_SESSION["PermisosUser"][$this->Modulo] == 0 ){
			$this->response->redirect($this->session->get("PrimerMenu"));
			return false;
		}
		$idPost = (int) $idPost;
		$this->view->msjResponse = "";
		$this->view->jsResponse = "";
		if($this->request->isPost()){
			$this->clearPost();
			$this->clearPostInt(array("status"));
			$Tabla = UsuariosSecundarios::find(array(
				"columns" => "id",
			    "conditions" => "id!=:id: AND (user=:user:)",
			    "bind" => array("id" => $idPost , "user" => $this->request->getPost("user" , "string")),
			    "bindTypes" => array("id" => Column::BIND_PARAM_INT , "user" => Column::BIND_PARAM_STR),
			    "limit" => 1
			));
			if( count($Tabla) <= 0 ){
				$Tabla = UsuariosSecundarios::findFirst(array(
					"columns" => "*",
				    "conditions" => "id=:id:",
				    "bind" => array("id" => $idPost),
				    "bindTypes" => array("id" => Column::BIND_PARAM_INT),
				    "limit" => 1
				));
				$Tabla->assign($this->request->getPost());
				if($Tabla->update()){
					$this->session->set("mensajeReturn" , $this->msjReturn("&Eacute;xito" , "Se edito el registro correctamente." , "success"));
					$this->response->redirect($this->Controller."/");
					$this->view->disable();
					return false;
				}
				$this->view->msjResponse = $this->msjReturn("Error" , "Ocurrio un error , intente de nuevo." , "error");
				$this->view->jsResponse = $this->setValueData("formulario_registro" , $_POST);
			} else {
				$this->view->msjResponse = $this->msjReturn("Error" , "Existe un registro con los mismos datos." , "error");
				$this->view->jsResponse = $this->setValueData("formulario_registro" , $_POST);
			}
		}
		$this->ajaxBody("Usuarios Secundarios");
		$this->setHeaderMenu("Configuraciones" , "Usuarios Secundarios" , $this->Controller , "Editar");		
		$this->view->formAction = $this->Controller."/edit/" .$idPost;
		$DataForm = array("data" => array());
		$Tabla = new UsuariosSecundarios();
		$Result = $Tabla->find(array(
			"columns" => "*",
		    "conditions" => "id=:id:",
		    "bind" => array("id" => $idPost),
		    "bindTypes" => array("id" => Column::BIND_PARAM_INT),
		    "limit" => 1
		));
		if( count($Result) <= 0 ){
			$this->response->redirect("estados/");
			$this->view->disable();
			return false;			
		}
		foreach($Result as $value){
			$DataForm["data"] = array(
				"id" => $value->id,
				"id_usuario" => $value->id_usuario,
				"nombre" => $value->nombre,
				"apellido" => $value->apellido,
				"user" => $value->user,
				"pass" => $value->pass,
				"status" => $value->status,
			);
		}
		$this->view->Usuarios = $this->getUsuariosForm();
		$this->view->jsResponse = $this->setValueData("formulario_registro" , $DataForm["data"]);
		return false;
	}



	public function deleteAction($idPost=0){
		if( !$this->Security->securitySession() ){
			$this->view->disable();
			$this->response->setContentType('application/json', 'UTF-8');
	        $response = new \Phalcon\Http\Response();
	        $response->setContent(json_encode(array()));
	        return $response;
		}
		if( $_SESSION["PermisosUser"][$this->Modulo] == 0 ){
			$this->view->disable();
			$this->response->setContentType('application/json', 'UTF-8');
	        $response = new \Phalcon\Http\Response();
	        $response->setContent(json_encode(array()));
	        return $response;
		}
		$idPost = (int) $idPost;
		if( UsuariosSecundarios::find("id=".$idPost)->delete() ){
			$Msj = array("status" => 1 , "msj" => "Se elimino el registro correctamente.");
		} else {
			$Msj = array("status" => 0 , "msj" => "Ocurrio un error al eliminar el registro.");
		}
		$this->view->disable();
		$this->response->setContentType('application/json', 'UTF-8');
        $response = new \Phalcon\Http\Response();
        $response->setContent(json_encode($Msj));
        return $response;
	}






}