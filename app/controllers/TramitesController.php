<?php


use Phalcon\Mvc\Controller,
	Phalcon\Db\Result\Pdo,
	Phalcon\Tag as Tag,
	Phalcon\Db\Column;




class TramitesController extends ControllerBase {
	
	private $Security ;
	var $Data = array();
	var $MenuSlider = array();

	public function initialize() {
		$this->Title = "Tramites";
		Tag::setTitle($this->Title);
		parent::initialize();

		$this->setJS("tramites");
		$this->Security = new Security();
		$this->Modulo = "sub_tramites_tramites"; 
		$this->Controller = "tramites";
	}

	public function dropAccents($incoming_string){        
        $tofind = "ÀÁÂÄÅàáâäÒÓÔÖòóôöÈÉÊËèéêëÇçÌÍÎÏìíîïÙÚÛÜùúûüÿÑñ";
        $replac = "AAAAAaaaaOOOOooooEEEEeeeeCcIIIIiiiiUUUUuuuuyNn";
        return utf8_encode(strtr(utf8_decode($incoming_string),utf8_decode($tofind),$replac));
    }

	public function GeneraRuta($titulo){
		$titulo=$this->dropAccents($titulo);
		$titulo=trim($titulo);
		$titulo=str_replace(' ','_',$titulo);
		$titulo=str_replace(',','',$titulo);
		$titulo=str_replace('.','',$titulo);
		$titulo=str_replace('ñ','n',$titulo);
		$titulo=str_replace('Á','A',$titulo);
		$titulo=str_replace('á','a',$titulo);
		$titulo=str_replace(';','',$titulo);
		$titulo=str_replace(':','',$titulo);
		$titulo=str_replace('-','_',$titulo);
		$titulo=str_replace('"','',$titulo);
		$titulo=str_replace("'",'',$titulo);
		$titulo=str_replace("?",'',$titulo);
		$titulo=str_replace("¿",'',$titulo);
		$titulo=str_replace("#",'',$titulo);
		$titulo=str_replace("!",'',$titulo);
		$titulo=str_replace("¡",'',$titulo);
		$titulo=str_replace("%",'',$titulo);
		$titulo=str_replace("/",'',$titulo);
		//$titulo=mysql_real_escape_string($titulo);
		return strtolower($titulo); 
	}


	public function SinHtml($texto){
		$texto = @eregi_replace("<head[^>]*>.*</head>"," ",$texto);
		$texto = @eregi_replace("<script[^>]*>.*</script>"," ",$texto);
		$texto = @eregi_replace("<style[^>]*>.*</style>"," ",$texto);
		$texto = @eregi_replace("<[^>]*>"," ",$texto);
		$texto = @eregi_replace("&nbsp;","",$texto);
		return $texto ;
	}



	public function getRealIP() {
	    if (!empty($_SERVER['HTTP_CLIENT_IP']))
	        return $_SERVER['HTTP_CLIENT_IP'];
	       
	    if (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))
	        return $_SERVER['HTTP_X_FORWARDED_FOR'];
	   
	    return $_SERVER['REMOTE_ADDR'];
	}



	public function getAction(){
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
		$Data = array("aaData" => array());
		$Result = ViewTramites::find(array(
			"columns" => "id ,empresa,liquidador,date(fecha_creacion) as fecha,status",
		    "conditions" => "status!=2",
		   //"limit" => 4
		));	
		//1 activo
		//0 inactivo
		//2 borrado			
		if( count($Result) > 0 ){
			foreach($Result as $key => $value){
				$Buttons = '';
				if( (int) $_SESSION["PermisosUser"][$this->Modulo . "_edit"] == 1 ){
					$Buttons .= '<a href="tramites/edit/'.trim($value->id).'/" class="btn btn-sm btn-icon btn-primary"><i class="fa fa-pencil"></i></a> ';
				}

				if( (int) $_SESSION["PermisosUser"][$this->Modulo . "_delete"] == 1 ){
					$Buttons .= '<a href="del/'.(int) trim($value->id).'/" class="delete_usuarios btn btn-sm btn-icon btn-danger"><i class="fa fa-trash-o"></i></a>';
				}

				//$img="<img src='../tmp/tramites/".$value->img."' width=50 height=50 >";

				$Data["aaData"][] = array(
					"id" => trim($value->id),					
					"empresa" => utf8_decode(trim($value->empresa)),
					"liquidador" => utf8_decode(trim($value->liquidador)),
					"fecha" => trim($value->fecha),					
					"status" => trim($this->selectStatus($value->status)),
					"buttons" => trim($Buttons),
				);
			}
		}
		//echo "<pre>"; print_r($Data); echo "</pre>"; exit();
		$this->view->disable();
		$this->response->setContentType('application/json', 'UTF-8');
        $response = new \Phalcon\Http\Response();
        $response->setContent(json_encode($Data));
        return $response;
	}


	

	public function indexAction(){
		if( !$this->Security->securitySession() ){
			return false;
		}
		if( $_SESSION["PermisosUser"][$this->Modulo] == 0 ){
			$this->response->redirect($this->session->get("PrimerMenu"));
			return false;
		}

		$this->ajaxBody($this->Title); 
		$this->setHeaderMenu("Tramites" , "Listado de Tramites" , $this->Controller , "");		
		$this->view->msjResponse = "";
		if( $this->session->has("mensajeReturn") ){
			$this->view->msjResponse = $this->session->get("mensajeReturn");
			$this->session->remove("mensajeReturn");
		}
	}



	public function newAction(){

		//$Folder =  __DIR__  . "/../../../tmp/documentos/";
		$Folder ="tmp/documentos/";
		@mkdir($Folder , 777);

		if( !$this->Security->securitySession() ){
			return false;
		}
		if( $_SESSION["PermisosUser"][$this->Modulo] == 0 ){
			$this->response->redirect($this->session->get("PrimerMenu"));
			return false;
		}
		$this->view->msjResponse = "";
		$this->view->jsResponse = "";
		$this->view->contenido = "";
		if($this->request->isPost()){
			//$this->clearPost();
			//$this->clearPostInt(array("status" , "id_departamento" , "id_puesto"));
			//$_POST["slug"] = uniqid();
			$_POST["fecha"] = date("Y-m-d H:i:s");
			$_POST["id_usuario"] = $_SESSION["id"];			
			$_POST["nombre"] = utf8_encode($_POST["nombre"]);
			//$_POST["contenido"] = utf8_encode($_POST["contenido"]);			
			$_POST["ip"] = $this->getRealIP();

			
			//print_r($_FILES);
			

			if($_FILES['img']["name"] != ""){
				$name = explode('.', $_FILES['img']['name']);
				$ext = $name[count($name)-1];
				$name = 'img_'.uniqid().'.'.$ext;
				$_POST['img'] = $name;
			}	/**/			
			
			
			$Tabla = new Documentos();
			$Result = $Tabla->find(array(
				"columns" => "id",
			    "conditions" => "nombre=:nombre: and status=:status:",
			    "bind" => array("nombre" => $this->request->getPost("nombre" , "string") , "status" => $this->request->getPost("status" , "string") ),
			    "bindTypes" => array("nombre" => Column::BIND_PARAM_STR ,  "status" => Column::BIND_PARAM_STR ),
			    "limit" => 1
			));
			if( count($Result) <= 0 ){
				$Tabla->assign($this->request->getPost());
				if($Tabla->save()){
					if($_FILES['img']["name"] != ""){
						@copy($_FILES['img']['tmp_name'],$Folder.$name);
						$ruta=$Folder.$name;
						$directorio='tipos';
						$this->Miniaturas($ruta,50,$name,$directorio);		
						

					}/**/					
					//$this->setSlug($Tabla->id , $_POST["slug"]);
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
				//$this->view->jsResponse .= '<script type="text/javascript">Puestos('.$_POST["id_puesto"].');</script>';
			}
		}
		$this->view->img = '';
		$this->ajaxBody($this->Title);
		$this->setHeaderMenu("Documentos" , "Nuevo Documento" , $this->Controller , "Nuevo");		
		$this->view->textarea = "";
		$this->view->formAction = $this->Controller . "/new/";
		//$this->view->Departamentos = $this->getDepartamentos();
	}


public function editAction($id=""){

		$Folder =  __DIR__  . "/../../../tmp/documentos/";
		$Folder ="tmp/documentos/";
		@mkdir($Folder , 777);

		if( !$this->Security->securitySession() ){
			return false;
		}
		if( $_SESSION["PermisosUser"][$this->Modulo] == 0 ){
			$this->response->redirect($this->session->get("PrimerMenu"));
			return false;
		}
		$_POST["id"] = $id;
		//unset($_FILES);
		$this->view->msjResponse = "";
		$this->view->jsResponse = "";
		$this->view->contenido = "";
		if($this->request->isPost()){

			
			$Tabla = Documentos::findFirst(array(
				"columns" => "*",
			    "conditions" => "id=:id:",
			    "bind" => array("id" => $this->request->getPost("id" , "int")),
			    "bindTypes" => array("id" => Column::BIND_PARAM_INT),
			    "limit" => 1
			));
			
			$_POST["fecha_edit"] = date("Y-m-d H:i:s");
			$_POST["nombre"] = utf8_encode($_POST["nombre"]);
            //$_POST["contenido"] = utf8_encode($_POST["contenido"]);			
			$_POST["ip"] = $this->getRealIP();
            
			if($_FILES['img']["name"] != ""){
				$name = explode('.', $_FILES['img']['name']);
				$ext = $name[count($name)-1];
				$name = 'img_'.uniqid().'.'.$ext;
				$_POST['img'] = $name;
			}/**/
						
			
			//echo '<pre>';print_r($_POST);echo '</pre>';exit();
			//echo '<pre>';print_r($_POST);echo '</pre>';exit();
			$Tabla->assign($this->request->getPost());
			if($Tabla->update()){
				
                /**/if($_FILES['img']["name"] != ""){
					@copy($_FILES['img']['tmp_name'],$Folder.$_POST['img']);
					$ruta=$Folder.$name;
					$directorio='tipos';
					$this->Miniaturas($ruta,50,$name,$directorio);	
				}				
				$this->session->set("mensajeReturn" , $this->msjReturn("&Eacute;xito" , "Se edito el registro correctamente." , "success"));
				$this->response->redirect($this->Controller."/");
				$this->view->disable();
				return false;
			}
			$this->view->msjResponse = $this->msjReturn("Error" , "Ocurrio un error , intente de nuevo." , "error");
			$this->view->jsResponse = $this->setValueData("formulario_registro" , $_POST);
		}
		$this->ajaxBody($this->Title);
		$this->setHeaderMenu("Documentos" , "Listado de Documentos" , $this->Controller , "Editar");		
		$this->view->formAction = $this->Controller."/edit/" .$id;
		//$this->view->Departamentos = $this->getDepartamentos();
		$DataForm = array("data" => array());
		$Tabla = new Documentos();
		$Result = $Tabla->find(array(
			"columns" => "*",
		    "conditions" => "id=:id:",
		    "bind" => array("id" => $id),//$this->request->getPost("id" , "string")),
		    "bindTypes" => array("id" => Column::BIND_PARAM_INT),
		    "limit" => 1
		));
		if( count($Result) <= 0 ){
			$this->response->redirect($this->Controller."/");
			$this->view->disable();
			return false;			
		}
		foreach($Result as $value){
			$DataForm["data"] = array(
				"id" => $value->id,
				"status" => $value->status,
				"nombre" => utf8_decode($value->nombre), 
				"img" => $value->img,               
				
			);
			$this->view->nombre = utf8_decode($value->nombre);
			$this->view->img = $value->img;	
            $this->view->status = $value->status;		
		//echo '<pre>';print_r($DataForm["data"]);echo '</pre>';exit();
		}
		$this->view->jsResponse = $this->setValueData("formulario_registro" , $DataForm["data"]);
		//$this->view->jsResponse .= '<script type="text/javascript">Puestos('.$DataForm["data"]["id_puesto"].');</script>';
	}




	public function deleteAction($id=""){
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
		$_POST["status"] = 2;
		$_POST["id"] = $id;
		$_POST["fecha_edit"] = date("Y-m-d H:i:s");
		$_POST["id_usuario"] = $_SESSION["id"];
		$Tabla = Documentos::findFirst(array(
			"columns" => "*",
		    "conditions" => "id=:id:",
		    "bind" => array("id" => $this->request->getPost("id" , "string")),
		    "bindTypes" => array("id" => Column::BIND_PARAM_INT),
		    "limit" => 1
		));				
		$Tabla->assign($this->request->getPost());
		if($Tabla->update()){
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