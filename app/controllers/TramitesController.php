<?php


use Phalcon\Mvc\Controller,
	Phalcon\Db\Result\Pdo,
	Phalcon\Tag as Tag,
	Phalcon\Db\Column,
	PhpOffice\PhpWord\TemplateProcessor;




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


	public function crear_word($id_documento,$empresa,$vars = ""){
		$Folder =  __DIR__  . "/../../public/tmp/tramites/";
		//$Folder ="tmp/word/";
		@mkdir($Folder , 777);
		$Documento = Documentos::findFirst($id_documento);
		$empresa = $this->GeneraRuta($empresa);
		if($Documento){
			$file = date("d_m_Y-").$empresa.".docx";
			if(file_exists($Folder.$file)) unlink($Folder.$file);
			$word_path = dirname(__FILE__).'/../../vendor/phpoffice/phpword/src/PhpWord/Autoloader.php';
			require_once $word_path;
			PhpOffice\PhpWord\Autoloader::register();

			$templateWord = new TemplateProcessor(__DIR__.'/../../public/tmp/documentos/'.$Documento->img);
			if($vars != "")
				foreach ($vars as $key => $value)
					$templateWord->setValue($key,$value);
			$templateWord->saveAs($Folder.$file);
			return file_exists($Folder.$file) ? $file:false ;
		}else{
			return false;
		}
	}



	public function newAction($id_negocio = ""){


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
			$_POST["fecha_creacion"] = date("Y-m-d H:i:s");
			$_POST["id_usuario"] = $_SESSION["id"];			
			$_POST["liquidador"] = utf8_encode($_POST["liquidador"]);
			//$_POST["contenido"] = utf8_encode($_POST["contenido"]);			
			$_POST["ip"] = $this->getRealIP();
			$Tabla = new Tramites();
			$Result = $Tabla->find(array(
				"columns" => "id",
			    "conditions" => "id_empresa=:id_empresa: and status=:status: and liquidador=:liquidador: and date(fecha_creacion) = date('Y-m-d') ",
			    "bind" => array( "id_empresa" => $this->request->getPost("id_empresa" , "int") , "status" => $this->request->getPost("status" , "string") , "liquidador" => $this->request->getPost("liquidador" , "string") ),
			    "bindTypes" => array("id_empresa" => Column::BIND_PARAM_INT ,  "status" => Column::BIND_PARAM_STR,  "liquidador" => Column::BIND_PARAM_STR ),
			    "limit" => 1
			));
			if( count($Result) <= 0 ){

				$datos = Negocios::findFirst($_POST["id_empresa"]);

				if($datos){
					$socios = SociosEmpresa::find("id_empresa = $datos->id and status = 1 ");
					$array["NOMBRE_SOCIEDAD"] = utf8_decode($datos->nombre);
					$array["REGISTRO"] = $datos->registro;
					$array["FECHA_DISOLUCION"] = $datos->fecha_disolucion;
					$array["ACCIONES_TOTAL"] = $datos->acciones_totales;
					$array["SUMA_CAPITAL_TOT"] = $datos->capital_total;
					$array["LIQUIDADOR"] = utf8_decode($_POST["liquidador"]);
					$array["FECHA_LIQ"] = $datos->fecha_liquidacion;
					$array["FECHA_BALANCE"] = $datos->fecha_balance;
					
					if(count($socios)>0){
						$i = 1;
						foreach ($socios as $key => $value) {
							$array["SOCIO_".$i] = utf8_decode($value->nombre_socio);
							$array["RFC".$i] = $value->rfc_socio;
							$array["CURP".$i] = $value->curps_socio;
							$array["ACCIONES".$i] = $value->acciones_socios;
							$array["TOTAL".$i] = $value->suma;
							$i++;
						}
					}

					if($file = $this->crear_word($_POST["id_documento"],utf8_decode($datos->nombre),$array)){

						$_POST["archivo"] = $file;
						$Tabla->assign($this->request->getPost());

						if($Tabla->save()){
							/*if($_FILES['img']["name"] != ""){
								@copy($_FILES['img']['tmp_name'],$Folder.$name);
								$ruta=$Folder.$name;
								$directorio='tipos';
								$this->Miniaturas($ruta,50,$name,$directorio);		
								

							}*/					
							//$this->setSlug($Tabla->id , $_POST["slug"]);
							$this->session->set("mensajeReturn" , $this->msjReturn("&Eacute;xito" , "Se guardo el registro correctamente." , "success"));					
							$this->response->redirect($this->Controller."/");
							$this->view->disable();
							return false;
						}
					}

					$this->view->msjResponse = $this->msjReturn("Error" , "Ocurrio un error , intente de nuevo." , "error");
					$this->view->jsResponse = $this->setValueData("formulario_registro" , $_POST);
				}
				$this->view->msjResponse = $this->msjReturn("Error" , "Ocurrio un error , intente de nuevo." , "error");
				$this->view->jsResponse = $this->setValueData("formulario_registro" , $_POST);
			} else {
				$this->view->msjResponse = $this->msjReturn("Error" , "Existe un registro con los mismos datos." , "error");
				$this->view->jsResponse = $this->setValueData("formulario_registro" , $_POST);
				//$this->view->jsResponse .= '<script type="text/javascript">Puestos('.$_POST["id_puesto"].');</script>';
			}
		}

		$Negocios = Negocios::find("status = 1");
		if(count($Negocios)>0){
			foreach ($Negocios as $key => $value) {
				$Negocio[] = array("id" => $value->id, "nombre" => utf8_decode($value->nombre)); 
			}
		}else{
			$Negocio = "";
		}

		$Docs = Documentos::find("status = 1");
		if(count($Docs)>0){
			foreach ($Docs as $key => $value) {
				$Documentos[] = array("id" => $value->id, "nombre" => utf8_decode($value->nombre));
			}
		}else{
			$Documentos = "";
		}

		$this->view->Documentos = $Documentos;
		$this->view->Negocios = $Negocio;
		$this->view->archivo = '';
		$this->view->id_negocio = $id_negocio;
		$this->ajaxBody($this->Title);
		$this->setHeaderMenu("Trámites" , "Nuevo Trámite" , $this->Controller , "Nuevo");		
		$this->view->id_archivo = "";
		$this->view->formAction = $this->Controller . "/new/";

		//$this->view->Departamentos = $this->getDepartamentos();
	}


public function editAction($id=""){

		$Folder =  __DIR__  . "/../../public/tmp/tramites/";
		//$Folder ="tmp/documentos/";
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

			
			$Tabla = Tramites::findFirst(array(
				"columns" => "*",
			    "conditions" => "id=:id:",
			    "bind" => array("id" => $this->request->getPost("id" , "int")),
			    "bindTypes" => array("id" => Column::BIND_PARAM_INT),
			    "limit" => 1
			));
			
			$_POST["fecha_edit"] = date("Y-m-d H:i:s");
			$_POST["liquidador"] = utf8_encode($_POST["liquidador"]);
            //$_POST["contenido"] = utf8_encode($_POST["contenido"]);			
			$_POST["ip"] = $this->getRealIP();
            
			if($_FILES['archivo']["name"] != ""){
				$name = explode('.', $_FILES['archivo']['name']);
				$ext = $name[count($name)-1];
				$name = explode(".",$Tabla->archivo);
				$name = str_replace($name[count($name)-1] ,"", $Tabla->archivo);
				//$name = 'img_'.uniqid().'.'.$ext;
				$name = $name.$ext;
				$_POST['archivo'] = $name;
			}/**/
						
			
			//echo '<pre>';print_r($_POST);echo '</pre>';exit();
			//echo '<pre>';print_r($_POST);echo '</pre>';exit();
			$Tabla->assign($this->request->getPost());
			if($Tabla->update()){
				
                if($_FILES['archivo']["name"] != ""){
					@copy($_FILES['archivo']['tmp_name'],$Folder.$_POST['archivo']);
					//$ruta=$Folder.$name;
					//$directorio='tipos';
					//$this->Miniaturas($ruta,50,$name,$directorio);	
				}	/**/			
				$this->session->set("mensajeReturn" , $this->msjReturn("&Eacute;xito" , "Se edito el registro correctamente." , "success"));
				$this->response->redirect($this->Controller."/");
				$this->view->disable();
				return false;
			}
			$this->view->msjResponse = $this->msjReturn("Error" , "Ocurrio un error , intente de nuevo." , "error");
			$this->view->jsResponse = $this->setValueData("formulario_registro" , $_POST);
		}
		$this->ajaxBody($this->Title);
		$this->setHeaderMenu("Trámites" , "Listado de Trámites" , $this->Controller , "Editar");		
		$this->view->formAction = $this->Controller."/edit/" .$id;
		//$this->view->Departamentos = $this->getDepartamentos();
		$DataForm = array("data" => array());
		$Tabla = new Tramites();
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
				"liquidador" => utf8_decode($value->liquidador), 
				"archivo" => $value->archivo, 
				"id_empresa" => $value->id_empresa, 
				"id_documento" => $value->id_documento
			);		
			$this->view->archivo = $value->archivo;
		//echo '<pre>';print_r($DataForm["data"]);echo '</pre>';exit();
		}



		$Negocios = Negocios::find("status = 1");
		if(count($Negocios)>0){
			foreach ($Negocios as $key => $value) {
				$Negocio[] = array("id" => $value->id, "nombre" => utf8_decode($value->nombre)); 
			}
		}else{
			$Negocio = "";
		}

		$Docs = Documentos::find("status = 1");
		if(count($Docs)>0){
			foreach ($Docs as $key => $value) {
				$Documentos[] = array("id" => $value->id, "nombre" => utf8_decode($value->nombre));
			}
		}else{
			$Documentos = "";
		}

		$this->view->Documentos = $Documentos;
		$this->view->Negocios = $Negocio;

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
		$Tabla = Tramites::findFirst(array(
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