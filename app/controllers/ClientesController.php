<?php
use Phalcon\Mvc\Controller,
	Phalcon\Db\Result\Pdo,
	Phalcon\Tag as Tag,
	Phalcon\Db\Column;




class ClientesController extends ControllerBase {
	
	private $Security ;
	var $Data = array();
	var $MenuSlider = array();

	public function initialize() {
		$this->Title = "Clientes";
		Tag::setTitle($this->Title);
		parent::initialize();

		$this->setJS("clientes");
		$this->Security = new Security();
		$this->Modulo = "sub_clientes_clientes"; 
		$this->Controller = "clientes";
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
		$Result = Clientes::find(array(
			"columns" => "id ,nombre ,telefono,correo, status",
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
					$Buttons .= '<a href="clientes/edit/'.trim($value->id).'/" class="btn btn-sm btn-icon btn-primary"><i class="fa fa-pencil"></i></a> ';
				}

				if( (int) $_SESSION["PermisosUser"][$this->Modulo . "_delete"] == 1 ){
					$Buttons .= '<a href="del/'.(int) trim($value->id).'/" class="delete_usuarios btn btn-sm btn-icon btn-danger"><i class="fa fa-trash-o"></i></a> ';
				}


				if( (int) $_SESSION["PermisosUser"]['sub_negocios_negocios_new'] == 1 ){
					$Buttons .= '<a href="negocios/new/'.(int) trim($value->id).'/" class="btn btn-sm btn-icon btn-success"><i class="fa fa-university"></i></a>';
				}


				//$img="<img src='tmp/clientes/".$value->img."' width=50 height=50 >";

				$Data["aaData"][] = array(
					"id" => trim($value->id),					
					"nombre" => utf8_decode(trim($value->nombre)),
					"telefono" => trim($value->telefono),
					"correo" => utf8_decode(trim($value->correo)),
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
		$this->setHeaderMenu("Clientes" , "Listado de clientes" , $this->Controller , "");		
		$this->view->msjResponse = "";
		if( $this->session->has("mensajeReturn") ){
			$this->view->msjResponse = $this->session->get("mensajeReturn");
			$this->session->remove("mensajeReturn");
		}













	}



	public function newAction(){

		 //$Folder =  __DIR__  . "tmp/negocios/";
		 $Folder = "tmp/clientes/";
		
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
		$this->view->Tipos = $Tipos;
		*/


		/*//Estados
		$Estados = array();
		$Table = new Estados();
		$Result_estados = $Table->find(array(
				"columns" => "id,nombre",
			    "conditions" => "status=1 order by nombre ASC",			    	    
		));
		foreach($Result_estados as $value){
			$Estados[] = array("id" => $value->id ,"nombre" => $value->nombre);
		}		
		$this->view->Estados = $Estados;*/



		if($this->request->isPost()){
			//$this->clearPost();
			//$this->clearPostInt(array("status" , "id_departamento" , "id_puesto"));			
			$_POST["fecha_creacion"] = date("Y-m-d H:i:s");
			$_POST["id_usuario"] = $_SESSION["id"];
			$_POST["ip"] = $this->getRealIP();
			
			$_POST["nombre"] = utf8_encode($_POST["nombre"]);
			//$_POST["rfc"] = utf8_encode($_POST["rfc"]);
			//$_POST["direccion"] = utf8_encode($_POST["direccion"]);
			
			//$usernew=$_POST["correo_contacto"];
			//$passnew=$_POST["password"];
			
			
			

			/*if($_FILES['img']["name"] != ""){
				$name = explode('.', $_FILES['img']['name']);
				$ext = $name[count($name)-1];
				$name = 'img_'.uniqid().'.'.$ext;
				$_POST['img'] = $name;
			}	*/
					
			
			$Table = new Clientes();
			$Result = $Table->find(array(
				"columns" => "id",
			    "conditions" => "correo='".$_POST['correo']."'", 
			    "limit" => 1
			));
			if( count($Result) <= 0 ){
				$Table->assign($this->request->getPost());
				if($Table->save()){

					/*obtenemos id de cliente para crear usuario*/
					$Table = new Clientes();
					$Result = $Table->find(array(
						"columns" => "id",
					    "conditions" => "correo='".$_POST['correo']."'", 
					    "limit" => 1
					));
					$id_cliente=$Result[0]['id'];
					
					//echo "<pre>";print_r($Result);exit();


					/*termina obtenemos id de negocio para crear usuario*/



					///////////impórtante////////////					
					//creamos un usuario tipo Negocio,enviamos correo y damos permisos
					$_POST["pass"]="FiscalistasMD0".$id_cliente;
						$Guardaren = new Usuarios();						
						$Guardaren->assign(array(
							"id_user" =>$_SESSION['id'],
							"id_cliente" =>$id_cliente,
							"email" =>$_POST["correo"],
							"user" =>$_POST["correo"],
							"password" =>sha1($_POST["pass"]),
							"nombre" =>$_POST["nombre"],
							"telefono" =>$_POST["telefono"],
							"tipo" =>'Cliente',
							"fecha" =>date("Y-m-d H:i:s"),														
							"status" =>'0',	
						));
						
						if($Guardaren->save()){	
						///mandamos correo
						$titulo = 'Bienvenido a Fiscalistas MD';
						$mensaje = '<div>
									<table width="100%" border="0" cellspacing="0" cellpadding="0" style="background-color:#d6d6d5;border:0;border-collapse:collapse;border-spacing:0" bgcolor="#d6d6d5">
									<tr>
									<td align="center">
									<!---->
									<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" style="border:0;border-collapse:collapse;border-spacing:0;max-width:700px">
									<tr>
									<td bgcolor="#990000">&nbsp;</td>
									<td bgcolor="#990000">&nbsp;</td>
									<td bgcolor="#990000">&nbsp;</td>
									<td bgcolor="#990000">&nbsp;</td>
									</tr>
									<tr>
									<td width="3%" bgcolor="#FFFFFF">&nbsp;</td>
									<td width="84%" bgcolor="#FFFFFF" style="color:#000000;font-family:\'ClanPro-Book\',\'HelveticaNeue-Light\',\'Helvetica Neue Light\',Helvetica,Arial,sans-serif;font-size:20px;line-height:36px">
									<b>Bienvenido(a) </b>&nbsp;'.$_POST['nombre'].'
									</td>
									<td width="10%" bgcolor="#FFFFFF"><img src="http://md.testingview.com/images/logoprincipal.png" width="150" /></td>
									<td width="3%" bgcolor="#FFFFFF">&nbsp;</td>
									</tr>
									<tr>
									<td bgcolor="#FFFFFF">&nbsp;</td>
									<td bgcolor="#FFFFFF"  style="color:#717172;font-family:\'ClanPro-Book\',\'HelveticaNeue-Light\',\'Helvetica Neue Light\',Helvetica,Arial,sans-serif;font-size:16px;line-height:28px">De parte del equipo de <b>MD</b> recibe una cordial bienvenida.</br> ya eres parte de esta comunidad.</td>
									<td bgcolor="#FFFFFF">&nbsp;</td>
									<td bgcolor="#FFFFFF">&nbsp;</td>
									</tr>
									<tr>
									<td bgcolor="#FFFFFF">&nbsp;</td>
									<td bgcolor="#FFFFFF">&nbsp;</td>
									<td bgcolor="#FFFFFF">&nbsp;</td>
									<td bgcolor="#FFFFFF">&nbsp;</td>
									</tr>
									<tr>
									<td bgcolor="#FFFFFF">&nbsp;</td>
									<td bgcolor="#FFFFFF"><hr></hr></td>
									<td bgcolor="#FFFFFF">&nbsp;</td>
									<td bgcolor="#FFFFFF">&nbsp;</td>
									</tr>
									<tr>
									<td bgcolor="#FFFFFF">&nbsp;</td>
									<td bgcolor="#FFFFFF" align="left" style="color:#000000;font-family:\'ClanPro-Book\',\'HelveticaNeue-Light\',\'Helvetica Neue Light\',Helvetica,Arial,sans-serif;font-size:20px;line-height:20px"><b>Datos de acceso</b></td>
									<td bgcolor="#FFFFFF">&nbsp;</td>
									<td bgcolor="#FFFFFF">&nbsp;</td>
									</tr>
									<tr>
									<td bgcolor="#FFFFFF">&nbsp;</td>
									<td bgcolor="#FFFFFF" align="left">
			                        <b>Usuario:</b>	'.$_POST["correo"].'					
									</td>
									<td bgcolor="#FFFFFF">&nbsp;</td>
									<td bgcolor="#FFFFFF">&nbsp;</td>
									</tr>
									<tr>
									  <td bgcolor="#FFFFFF">&nbsp;</td>
									  <td bgcolor="#FFFFFF" align="left"><b>Password:</b> '.$_POST["pass"].'</td>
									  <td bgcolor="#FFFFFF">&nbsp;</td>
									  <td bgcolor="#FFFFFF">&nbsp;</td>
									  </tr>
									<tr>
									<td bgcolor="#FFFFFF">&nbsp;</td>
									<td bgcolor="#FFFFFF"><hr></hr></td>
									<td bgcolor="#FFFFFF">&nbsp;</td>
									<td bgcolor="#FFFFFF">&nbsp;</td>
									</tr>
									<tr>
									<td bgcolor="#FFFFFF">&nbsp;</td>
									<td bgcolor="#FFFFFF" style="color:#717172;font-family:\'ClanPro-Book\',\'HelveticaNeue-Light\',\'Helvetica Neue Light\',Helvetica,Arial,sans-serif;font-size:14px;line-height:28px">Si tienes alguna pregunta, responde a este correo o escríbenos a contacto@fiscalistas-md.com.mx</td>
									<td bgcolor="#FFFFFF">&nbsp;</td>
									<td bgcolor="#FFFFFF">&nbsp;</td>
									</tr>
									<tr>
									<td bgcolor="#FFFFFF">&nbsp;</td>
									<td bgcolor="#FFFFFF">&nbsp;</td>
									<td bgcolor="#FFFFFF">&nbsp;</td>
									<td bgcolor="#FFFFFF">&nbsp;</td>
									</tr>
									<tr>
									<td bgcolor="#000000">&nbsp;</td>
									<td colspan="2" bgcolor="#000000">
									<table width="100%" border="0" cellspacing="0" cellpadding="0">
									<tr>
									<td width="80%">dd</td>
									<td width="5%">&nbsp;</td>
									<td width="4%">&nbsp;</td>
									<td width="5%">&nbsp;</td>
									<td width="6%">&nbsp;</td>
									</tr>
									<tr>
									<td><img src="http://md.testingview.com/images/logoprincipal_blanco.png" width="150" /></td>
									<td align="center">&nbsp;</td>
									<td align="center">&nbsp;</td>
									<td align="center">&nbsp;</td>
									<td align="center">&nbsp;</td>
									</tr>
									<tr>
									<td>&nbsp;</td>
									<td>&nbsp;</td>
									<td>&nbsp;</td>
									<td>&nbsp;</td>
									<td>&nbsp;</td>
									</tr>
									</table>
									</td>
									<td bgcolor="#000000">&nbsp;</td>
									</tr>
									</table>
									<!---->    
									</td>
									</tr>
									</table>
									</div>
									';

						  
						$cabeceras  = 'MIME-Version: 1.0' . "\r\n";
						$cabeceras .= 'Content-type: text/html; charset=UTF-8; format=flowed\n' . "\r\n";   
						$cabeceras .= 'From:MD Consultoría contacto@fiscalistas-md.com.mx' . "\r\n";


						@mail($datos['correo'], $titulo, $mensaje, $cabeceras ); 
						@mail('luisglezv3@gmail.com', $titulo, $mensaje, $cabeceras ); 

						///termina correo
						///////////////////////
						//damos permisos//////
						//////////////////////

						 $Usuarios = new Usuarios();
						$Resultuser = $Usuarios->find(array(
							"columns" => "id",
						    "conditions" => "id_cliente='".$id_cliente."'", 
						    "limit" => 1
						));
						$id_usuario=$Resultuser[0]['id'];
							
						
							$Saveas = new Permisos();						
							$Saveas->assign(array(
								"id_usuario" =>$id_usuario,
								"mod_home" =>1,
								"sub_home_home" =>1,
								"mod_areaclientes" =>1,
								"sub_areaclientes_areaclientes" =>1,
								"sub_areaclientes_areaclientes_new" =>1,
								"mod_perfil" =>1,
								"sub_perfil_perfil" =>1,
								"sub_perfil_perfil_edit" =>1,					
								
							));
							
							$Saveas->save();
						


					}
					
					
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
		$this->setHeaderMenu("Clientes" , "Nuevo cliente" , $this->Controller , "Registro");		
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
		$_POST["fecha_modificacion"] = date("Y-m-d H:i:s");
		$_POST["id_usuario"] = $_SESSION["id"];
		$Tabla = Clientes::findFirst(array(
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