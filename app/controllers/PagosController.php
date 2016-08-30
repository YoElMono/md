<?php
use Phalcon\Mvc\Controller,
	Phalcon\Db\Result\Pdo,
	Phalcon\Tag as Tag,
	Phalcon\Db\Column;




class PagosController extends ControllerBase {
	
	private $Security ;
	var $Data = array();
	var $MenuSlider = array();

	public function initialize() {
		$this->Title = "Pagos | Gastos";
		Tag::setTitle($this->Title);
		parent::initialize();

		$this->setJS("pagos");
		$this->Security = new Security();
		$this->Modulo = "sub_pagos_pagos"; 
		$this->Controller = "pagos";
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
			"columns" => "id ,cliente ,empresa,costo, status",
		    "conditions" => "status!=4",
		   //"limit" => 4
		));	
		//1 activo
		//0 inactivo
		//2 borrado			
		if( count($Result) > 0 ){
			foreach($Result as $key => $value){
				$Buttons = '';
				if( (int) $_SESSION["PermisosUser"][$this->Modulo . "_new"] == 1 ){
					$Buttons .= '<a href="pagos/new/'.trim($value->id).'/" class="btn btn-sm btn-icon btn-success"><i class="fa fa-plus"></i></a> ';
				}

				if( (int) $_SESSION["PermisosUser"][$this->Modulo . "_delete"] == 1 ){
					
					
				}


				
				//pagos
				$Pagos =0;				
				$Tabla = new Movimientos();			
				$Resultsuma = $Tabla->find(array(
						"columns" => "monto",
					    "conditions" => "status=1 and tipo='Pago' and id_tramite=".(int) $value->id ,			    	    
				));
				foreach($Resultsuma as $valor){					
					 $Pagos+=$valor->monto;
				}		
				
			
				
				
				//$img="<img src='tmp/clientes/".$value->img."' width=50 height=50 >";

				$Data["aaData"][] = array(
					"id" => trim($value->id),					
					"cliente" => utf8_decode(trim($value->cliente)),
					"empresa" => trim($value->empresa),
					"costo" => utf8_decode(trim($value->costo)),
					"pagos" => $Pagos,
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
		$this->setHeaderMenu("Pagos | Gatos" , "Listado de tramites" , $this->Controller , "");		
		$this->view->msjResponse = "";
		if( $this->session->has("mensajeReturn") ){
			$this->view->msjResponse = $this->session->get("mensajeReturn");
			$this->session->remove("mensajeReturn");
		}













	}



	public function newAction($id){

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
			if(isset($_POST['concepto'])) $_POST['concepto'] = utf8_encode($_POST['concepto']);
			else $_POST['concepto'] = '';
			$_POST['fecha_creacion'] = date("Y-m-d H:i:s");
			$_POST['id_user'] = $_SESSION['id'];
			$_POST['status'] = 1;
			$movimiento = new Movimientos();
			$movimiento->assign($_POST);
			if($movimiento->save()){
				$response = ["status"=>1,"id"=>$movimiento->id,"monto"=>$_POST['monto'],"concepto"=>utf8_decode($_POST['concepto']),"fecha"=>$_POST['fecha']];
			}else{
				$response = ["status"=>0,"msj"=>"Error al guardar el registro"];
			}
			echo json_encode($response);exit();
		}
		$this->view->numero_tramite = $id;
		$generales = ViewTramites::findFirst("id = $id");
		$this->view->cliente = utf8_decode($generales->cliente);
		$this->view->empresa = utf8_decode($generales->empresa);
		$this->view->costo = $generales->costo;
		$pagos = $gastos = [];
		$total = $total_gastos = 0;
		$Pagos = Movimientos::find("status=1 and tipo='Pago' and id_tramite=".(int) $id);
		if(count($Pagos)>0){
			foreach ($Pagos as $key => $value) {
				$pagos[] = ["id"=>$value->id,"fecha"=>$value->fecha,"monto"=>$value->monto];
				$total += $value->monto;
			}
		}
		$restante = $generales->costo-$total;
		$Gastos = Movimientos::find("status=1 and tipo='Gasto' and id_tramite=".(int) $id);
		if(count($Gastos)>0){
			foreach ($Gastos as $key => $value) {
				$gastos[] = ["id"=>$value->id,"fecha"=>$value->fecha,"monto"=>$value->monto,"concepto"=>utf8_decode($concepto)];
				$total_gastos += $value->monto;
			}
		}
		$utilidad = $generales->costo-$total_gastos;
		$this->view->pagos = $pagos;
		$this->view->gastos = $gastos;
		$this->view->total = $total;
		$this->view->total_gastos = $total_gastos;
		$this->view->restante = $restante;
		$this->view->utilidad = $utilidad;
		$this->ajaxBody($this->Title);
		$this->setHeaderMenu("Pagos | Gatos" , "Nuevo Pago" , $this->Controller , "Registro");					
		$this->view->textarea = "";
		$this->view->formAction = $this->Controller . "/new/";
		//$this->view->Departamentos = $this->getDepartamentos();
	}


public function editAction($id=""){

		/*$Folder = "tmp/clientes/";
		@mkdir($Folder , 777);*/

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


		/*/clasificacion
		$Tipos = array();
		$Table = new Tipo();
		$Result_tipos = $Table->find(array(
				"columns" => "id,nombre",
			    "conditions" => "status=1 order by nombre ASC",			    	    
		));
		foreach($Result_tipos as $value){
			$Tipos[] = array("id" => $value->id ,"nombre" => $value->nombre);
		}		
		$this->view->Tipos = $Tipos;*/


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

			
			$Tabla = Clientes::findFirst(array(
				"columns" => "*",
			    "conditions" => "id=:id:",
			    "bind" => array("id" => $this->request->getPost("id" , "int")),
			    "bindTypes" => array("id" => Column::BIND_PARAM_INT),
			    "limit" => 1
			));
			
			$_POST["fecha_modificacion"] = date("Y-m-d H:i:s");			
			$_POST["id_usuario"] = $_SESSION["id"];
			$_POST["ip"] = $this->getRealIP();			
			$_POST["nombre"] = utf8_encode($_POST["nombre"]);
			$_POST["rfc"] = utf8_encode($_POST["rfc"]);
			$_POST["direccion"] = utf8_encode($_POST["direccion"]);
		


			/*if($_FILES['img']["name"] != ""){
				$name = explode('.', $_FILES['img']['name']);
				$ext = $name[count($name)-1];
				$name = 'img_'.uniqid().'.'.$ext;
				$_POST['img'] = $name;
			}*/
						
			
			//echo '<pre>';print_r($_POST);echo '</pre>';exit();
			//echo '<pre>';print_r($_POST);echo '</pre>';exit();
			$Tabla->assign($this->request->getPost());
			if($Tabla->update()){

				/*if($_FILES['img']["name"] != ""){
					@copy($_FILES['img']['tmp_name'],$Folder.$_POST['img']);
					$ruta=$Folder.$name;
					$directorio='clientes';
					$this->Miniaturas($ruta,50,$name,$directorio);	


				}	*/

				$this->session->set("mensajeReturn" , $this->msjReturn("&Eacute;xito" , "Se edito el registro correctamente." , "success"));
				$this->response->redirect($this->Controller."/");
				$this->view->disable();
				return false;
			}
			$this->view->msjResponse = $this->msjReturn("Error" , "Ocurrio un error , intente de nuevo." , "error");
			$this->view->jsResponse = $this->setValueData("formulario_registro" , $_POST);
		}
		

		$this->ajaxBody($this->Title);
		$this->setHeaderMenu("Clientes" , "Editar Clientes" , $this->Controller , "Registro");		
		$this->view->formAction = $this->Controller."/edit/" .$id;
		//$this->view->Departamentos = $this->getDepartamentos();
		$DataForm = array("data" => array());
		$Tabla = new Clientes();
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
				"id_estado" => utf8_decode($value->id_estado),				
				"nombre" => utf8_decode($value->nombre),
				"rfc" => utf8_decode($value->rfc),
				"direccion" => utf8_decode($value->direccion),
				"cp" => utf8_decode($value->cp),
				"telefono" => utf8_decode($value->telefono),			
				"correo" => utf8_decode($value->correo),				
			);
			//$this->view->nombre = utf8_decode($value->nombre);
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
		$_POST["id_user"] = $_SESSION["id"];
		$Tabla = Movimientos::findFirst(array(
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