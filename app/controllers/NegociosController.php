<?php
use Phalcon\Mvc\Controller,
	Phalcon\Db\Result\Pdo,
	Phalcon\Tag as Tag,
	Phalcon\Db\Column;




class NegociosController extends ControllerBase {
	
	private $Security ;
	var $Data = array();
	var $MenuSlider = array();

	public function initialize() {
		$this->Title = "Empresas";
		Tag::setTitle($this->Title);
		parent::initialize();

		$this->setJS("negocios");
		$this->Security = new Security();
		$this->Modulo = "sub_negocios_negocios"; 
		$this->Controller = "negocios";
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
		$Result = ViewNegocios::find(array(
			"columns" => "id ,cliente,razon_social ,telefono,tipo_sociedad, status",
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
					$Buttons .= '<a href="negocios/edit/'.trim($value->id).'/" class="btn btn-sm btn-icon btn-primary"><i class="fa fa-pencil"></i></a> ';
				}

				if( (int) $_SESSION["PermisosUser"][$this->Modulo . "_delete"] == 1 ){
					$Buttons .= '<a href="del/'.(int) trim($value->id).'/" class="delete_usuarios btn btn-sm btn-icon btn-danger"><i class="fa fa-trash-o"></i></a> ';
				}
				
				$Buttons .= '<a href="tramites/new/'.(int) trim($value->id).'/" class="btn btn-sm btn-icon btn-success"><i class="fa fa-gavel"></i></a>';

				//$img="<img src='tmp/negocios/".$value->img."' width=50 height=50 >";

				$Data["aaData"][] = array(
					"id" => trim($value->id),
					"cliente" => utf8_decode(trim($value->cliente)),					
					"razon_social" => utf8_decode(trim($value->razon_social)),
					"telefono" => trim($value->telefono),
					"tipo_sociedad" => utf8_decode(trim($value->tipo_sociedad)),
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
		$this->setHeaderMenu("Empresas" , "Listado de Empresas" , $this->Controller , "");		
		$this->view->msjResponse = "";
		if( $this->session->has("mensajeReturn") ){
			$this->view->msjResponse = $this->session->get("mensajeReturn");
			$this->session->remove("mensajeReturn");
		}

	}
	public function casoAction(){
		
		
			$this->view->msjResponse = "";
			$this->view->jsResponse = "";
			$this->view->contenido = "";
			
			
			
			$this->ajaxBody($this->Title);
			$this->setHeaderMenu("Empresas" , "Nuevo Tramite" , $this->Controller , "Registro");		
			$this->view->textarea = "";
			$this->view->formAction = $this->Controller . "/caso/";
			//$this->view->Departamentos = $this->getDepartamentos();
		
	}


	public function newAction($id_cliente=0){

			 
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


			
		$this->view->IdCliente = $id_cliente;
		

		/**///cliente
		$Cliente = array();
		$Table = new Clientes();
		$Result_clientes = $Table->find(array(
				"columns" => "id,nombre",
			    "conditions" => "status=1 order by nombre ASC",			    	    
		));
		foreach($Result_clientes as $value){
			$Cliente[] = array("id" => $value->id ,"nombre" => $value->nombre);
		}		
		$this->view->Cliente = $Cliente;
		


		if($this->request->isPost()){
			//$this->clearPost();
				
			$_POST["fecha_creacion"] = date("Y-m-d H:i:s");
			$_POST["id_usuario"] = $_SESSION["id"];
			$_POST["ip"] = $this->getRealIP();
			
			$_POST["ciudad"] = utf8_encode($_POST["ciudad"]);
			$_POST["razon_social"] = utf8_encode($_POST["razon_social"]);
			$_POST["rfc"] = utf8_encode($_POST["rfc"]);
			$_POST["fme"] = utf8_encode($_POST["fme"]);
			$_POST["fiel"] = utf8_encode($_POST["fiel"]);
			$_POST["ciec"] = utf8_encode($_POST["ciec"]);
			$_POST["fiel"] = utf8_encode($_POST["fiel"]);
			$_POST["valor_accion"] = round(($_POST["capital_total"]/$_POST["acciones_totales"]),2,PHP_ROUND_HALF_DOWN);
			
			//echo "<pre>";print_r($_POST);exit();			
			
			
			$Table = new Negocios();
			$Result = $Table->find(array(
				"columns" => "id",
			    "conditions" => "razon_social='".$_POST['razon_social']."'", 
			    "limit" => 1
			));
			if( count($Result) <= 0 ){
				$Table->assign($this->request->getPost());
				if($Table->save()){

					
					///////////////////////
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
		$this->setHeaderMenu("Empresas" , "Nueva Empresas" , $this->Controller , "Registro");		
		$this->view->textarea = "";
		$this->view->formAction = $this->Controller . "/new/";
		//$this->view->Departamentos = $this->getDepartamentos();
	}


	public function editAction($id=""){

		
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


		/**///cliente
		$Cliente = array();
		$Table = new Clientes();
		$Result_clientes = $Table->find(array(
				"columns" => "id,nombre",
			    "conditions" => "status=1 order by nombre ASC",			    	    
		));
		foreach($Result_clientes as $value){
			$Cliente[] = array("id" => $value->id ,"nombre" => $value->nombre);
		}		
		$this->view->Cliente = $Cliente;


		//Estados
		$Estados = array();
		$Table = new Estados();
		$Result_estados = $Table->find(array(
				"columns" => "id,nombre",
			    "conditions" => "status=1 order by nombre ASC",			    	    
		));
		foreach($Result_estados as $value){
			$Estados[] = array("id" => $value->id ,"nombre" => $value->nombre);
		}		
		$this->view->Estados = $Estados;

		///////////////////////////////////

		//actualizamos
		if($this->request->isPost()){

			
			$Tabla = Negocios::findFirst(array(
				"columns" => "*",
			    "conditions" => "id=:id:",
			    "bind" => array("id" => $this->request->getPost("id" , "int")),
			    "bindTypes" => array("id" => Column::BIND_PARAM_INT),
			    "limit" => 1
			));
			
			$_POST["fecha_modificacion"] = date("Y-m-d H:i:s");			
			$_POST["id_usuario"] = $_SESSION["id"];
			$_POST["ip"] = $this->getRealIP();			
			$_POST["ciudad"] = utf8_encode($_POST["ciudad"]);
			$_POST["razon_social"] = utf8_encode($_POST["razon_social"]);
			$_POST["rfc"] = utf8_encode($_POST["rfc"]);
			$_POST["fme"] = utf8_encode($_POST["fme"]);
			$_POST["fiel"] = utf8_encode($_POST["fiel"]);
			$_POST["ciec"] = utf8_encode($_POST["ciec"]);
			$_POST["fiel"] = utf8_encode($_POST["fiel"]);
			$_POST["valor_accion"] = round(($_POST["capital_total"]/$_POST["acciones_totales"]),2,PHP_ROUND_HALF_DOWN);


			$Tabla->assign($this->request->getPost());

			if($Tabla->update()){	

				$this->session->set("mensajeReturn" , $this->msjReturn("&Eacute;xito" , "Se edito el registro correctamente." , "success"));
				$this->response->redirect($this->Controller."/");
				$this->view->disable();
				return false;
			}
			$this->view->msjResponse = $this->msjReturn("Error" , "Ocurrio un error , intente de nuevo." , "error");
			$this->view->jsResponse = $this->setValueData("formulario_registro" , $_POST);
		}
		

		$this->ajaxBody($this->Title);
		$this->setHeaderMenu("Empresas" , "Editar Empresa" , $this->Controller , "Registro");		
		$this->view->formAction = $this->Controller."/edit/" .$id;
		//$this->view->Departamentos = $this->getDepartamentos();
		$DataForm = array("data" => array());
		$Tabla = new Negocios();
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
				"ciudad" => utf8_decode($value->ciudad),
				"id_cliente" => utf8_decode($value->id_cliente),				
				"razon_social" => utf8_decode($value->razon_social),
				"rfc" => utf8_decode($value->rfc),
				"fme" => utf8_decode($value->fme),
				"telefono" => utf8_decode($value->telefono),
				"fecha_disolucion" => utf8_decode($value->fecha_disolucion),
				"fecha_liquidacion" => utf8_decode($value->fecha_liquidacion),
				"fecha_balance" => utf8_decode($value->fecha_balance),
				"capital_total" => utf8_decode($value->capital_total),
				"acciones_totales" => utf8_decode($value->acciones_totales),
				"acciones_totales2" => utf8_decode($value->acciones_totales),	
				"tipo_sociedad" => utf8_decode($value->tipo_sociedad),	
				"fiel" => utf8_decode($value->fiel),	
				"ciec" => utf8_decode($value->ciec),	
				"opinion" => utf8_decode($value->opinion),	
				"archivo_acta" => utf8_decode($value->archivo_acta),
				"archivo_fiel" => utf8_decode($value->archivo_fiel),	
				"archivo_ciec" => utf8_decode($value->archivo_ciec),

			);
			$this->view->idregistro =$value->id;
			$this->view->archivo_acta =$value->archivo_acta;
			$this->view->archivo_fiel =$value->archivo_fiel;
			$this->view->archivo_ciec =$value->archivo_ciec;
			$this->view->valor_accion = $value->valor_accion;
			//$this->view->img = $value->img;		
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
		$_POST["fecha_modificacion"] = date("Y-m-d H:i:s");
		$_POST["id_usuario"] = $_SESSION["id"];
		$Tabla = Negocios::findFirst(array(
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


	public function socionewAction($id=""){

		if($this->request->isPost()){
			//$this->clearPost();
		
			$_POST["fecha_creacion"] = date("Y-m-d H:i:s");
			$_POST["id_usuario"] = $_SESSION["id"];
			$_POST["ip"] = $this->getRealIP();
			
			$_POST["id_empresa"] = $id;
			$_POST["nombre_socio"] = utf8_encode($_POST["nombre_socio"]);
			$_POST["rfc_socio"] = utf8_encode($_POST["rfc_socio"]);
			$_POST["curps_socio"] = utf8_encode($_POST["curps_socio"]);
			$_POST["status"]=1;
			$_POST["suma"] = round($_POST["suma"],2,PHP_ROUND_HALF_DOWN);
			
			

					
			
			$Table = new SociosEmpresa();
			$Result = $Table->find(array(
				"columns" => "id",
			    "conditions" => "nombre_socio='".$_POST['nombre_socio']."'", 
			    "limit" => 1
			));
			if(isset($_POST['id'])) $socio = $Table->findFirst($_POST['id']);
			else $socio = false;

			if($socio){
				$_POST['fecha_modificacion'] = date("Y-m-d H:i:s");
				unset($_POST["fecha_creacion"]);
				$socio->assign($_POST);
				if($socio->update()){
					$response['bien'] = true;
					$response['msg'] = "Registro actualizado :)";
				}else{
					$response['bien'] = false;
					$response['msg'] = "Error al guardar";
				}
			}elseif( count($Result) <= 0 ){
				$Table->assign($this->request->getPost());
				if($Table->save()){
					$response['bien'] = true;
					$response['msg'] = "Registro guardado :)";
					
					///////////////////////
					/*$this->session->set("mensajeReturn" , $this->msjReturn("&Eacute;xito" , "Se guardo el registro correctamente." , "success"));					
					$this->response->redirect($this->Controller."/");
					$this->view->disable();
					return false;*/
				}else{
					$response['bien'] = false;
					$response['msg'] = "Error al guardar";
				}
				/*$this->view->msjResponse = $this->msjReturn("Error" , "Ocurrio un error , intente de nuevo." , "error");
				$this->view->jsResponse = $this->setValueData("formulario_registro" , $_POST);*/
			} else {
				/*$this->view->msjResponse = $this->msjReturn("Error" , "Existe un registro con los mismos datos." , "error");
				$this->view->jsResponse = $this->setValueData("formulario_registro" , $_POST);*/
				//$this->view->jsResponse .= '<script type="text/javascript">Puestos('.$_POST["id_puesto"].');</script>';
				$response['bien'] = false;
				$response['msg'] = "Ya hay un registro igual";
			}

			echo json_encode($response);exit();
		}


	}

	public function getsociosAction($id_negocio = "")	{
		if($id_negocio != "" && is_numeric($id_negocio)){
			$socios = SociosEmpresa::find("id_empresa = $id_negocio and status = 1");
			if(count($socios)>0){
				foreach ($socios as $key => $value) {
					$Socios[] = array("id"=>$value->id,"id_negocio"=>$value->id_empresa, "nombre"=>utf8_decode($value->nombre_socio),"rfc"=>$value->rfc_socio,"curp"=>$value->curps_socio,"acciones"=>$value->acciones_socios,"valor"=>$value->valor,"total"=>$value->suma);
				}
				$response['bien'] = true;
				$response['msg'] = "todo bien";
				$response['socios'] = $Socios;

			}else{
				$response['bien'] = false;
				$response['msg'] = "no hay socios";
			}
			echo json_encode($response);exit();
		}else{
			echo "ERROR";exit();
		}
	}

	public function getdocumentosAction($id_negocio = "")	{
		if($id_negocio != "" && is_numeric($id_negocio)){
			$documentos = Documentos::find("id_negocio = $id_negocio and status = 1");
			if(count($documentos)>0){
				foreach ($documentos as $key => $value) {
					$Documentos[] = array("id"=>$value->id, "nombre"=>utf8_decode($value->nombre),"link"=>'<a href="doc_empresas/'.$value->archivo.'" class="btn btn-warning btn-icon"><i class="fa fa-folder-open"></i></a>');
				}
				$response['bien'] = true;
				$response['msg'] = "todo bien";
				$response['documentos'] = $Documentos;

			}else{
				$response['bien'] = false;
				$response['msg'] = "no hay socios";
			}
			echo json_encode($response);exit();
		}else{
			echo "ERROR";exit();
		}
	}

	public function deletesocioAction($id=""){
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
		$_POST["fecha_modificacion"] = date("Y-m-d H:i:s");
		$_POST["id_usuario"] = $_SESSION["id"];
		$Tabla = SociosEmpresa::findFirst(array(
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


	public function documentosAction($id){

		$Folder =  __DIR__  . "/../../public/doc_empresas/";
		@mkdir($Folder , 777);
		//echo json_encode($_FILES);exit();
		//echo '<pre>';print_r($_FILES);print_r($_POST);
		//exit();
		if($_FILES['img']['name'] != "" and $_FILES['img']['error'] == 0){
				$name = explode('.', $_FILES['img']['name']);
				$ext = $name[count($name)-1];
				$name = 'archivo_'.uniqid().'.'.$ext;
				//$_POST['img'] = $name;
				
				$_POST['archivo'] = $name;
				$_POST['fecha'] = date('Y-m-d H:i:s');
				$_POST['ip'] = $this->getRealIP();
				$_POST['id_negocio'] = $id;
				$_POST['id_usuario'] = $_SESSION['id'];
				$_POST['status'] = 1;
				$_POST['nombre'] = utf8_decode($_POST['nombre_documento']);
				//echo json_encode($usuario);
				//exit();
				$Documentos = new Documentos();
				$Documentos->assign($this->request->getPost());




				if($Documentos->save()){
					if($_FILES['img']['name'] != '') @copy($_FILES['img']['tmp_name'],$Folder.$name);
					echo json_encode(array("bien"=>true,"msg"=>"Actualización Completada","doc"=>["nombre"=>utf8_decode($Documentos->nombre),"link"=>'<a href="doc_empresas/'.$name.'" class="btn btn-warning btn-icon"><i class="fa fa-folder-open"></i></a>']));
				}else{
					echo json_encode(array("bien"=>false,"msg"=>"Error Al actualizar"));
				}
			}
			
		else{
			echo json_encode(array("bien"=>false,"msg"=>"Error Al actualizar"));
		}

	}





			
			
			







}