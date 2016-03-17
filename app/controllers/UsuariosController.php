<?php


use Phalcon\Mvc\Controller,
	Phalcon\Db\Result\Pdo,
	Phalcon\Tag as Tag,
	Phalcon\Db\Column;




class UsuariosController extends ControllerBase {
	
	private $Security ;
	var $Data = array();

	public function initialize() {
		Tag::setTitle('Usuarios');
		parent::initialize();
		$this->setJS("usuarios");
		$this->Security = new Security();
		$this->Modulo = "sub_configuraciones_usuarios";
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
		
		$Result = Usuarios::find(array(
			"columns" => "*",
		    "conditions" => 'status!=3 and tipo="Administrador"  ',
		));				
		if( count($Result) > 0 ){
			foreach($Result as $key => $value){
				$Buttons = '';
				if( (int) $_SESSION["PermisosUser"][$this->Modulo . "_edit"] == 1 ){
					$Buttons .= '<a href="usuarios/edit/'.(int) trim($value->id).'/" class="btn btn-sm btn-icon btn-primary"><i class="fa fa-pencil"></i></a>';
				}
				if( (int) $_SESSION["PermisosUser"][$this->Modulo . "_delete"] == 1 ){
					$Buttons .= '<a href="del/'.(int) trim($value->id).'/" class="delete_usuarios btn btn-sm btn-icon btn-danger"><i class="fa fa-trash-o"></i></a>';
				}

				$Data["aaData"][] = array(
					"id" => trim($value->id),
					"nombre" => trim($value->nombre) . " " . trim($value->apellido),
					"email" => trim($value->email),
					"user" => trim($value->user),
					"pass" => trim($value->pass),
					"tipo" => trim($value->tipo),
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


	public function getnegociosAction(){
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

		$condiciones='status!=3';
		if($_SESSION['tipo']=="Negocio"){ $condiciones='status!=3 and id_negocio='.$_SESSION['id_negocio'];}
		$Result = ViewUserNegocios::find(array(
			"columns" => "*",
		    "conditions" => $condiciones,
		));				
		if( count($Result) > 0 ){
			foreach($Result as $key => $value){
				$Buttons = '';
				if( (int) $_SESSION["PermisosUser"][$this->Modulo . "_edit"] == 1 ){
					$Buttons .= '<a href="usuarios/edit/'.(int) trim($value->id).'/" class="btn btn-sm btn-icon btn-primary"><i class="fa fa-pencil"></i></a>';
				}
				if( (int) $_SESSION["PermisosUser"][$this->Modulo . "_delete"] == 1 ){
					$Buttons .= '<a href="del/'.(int) trim($value->id).'/" class="delete_usuarios btn btn-sm btn-icon btn-danger"><i class="fa fa-trash-o"></i></a>';
				}

				$Data["aaData"][] = array(
					"id" => trim($value->id),
					"estado" => trim($value->estado),	
					"nombre" => trim($value->nombre) . " " . trim($value->apellido),
					"email" => trim($value->email),					
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



	public function getsucursalesAction(){
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

		$condiciones='status!=3';
		if($_SESSION['tipo']=="Negocio"){ $condiciones='status!=3 and id_negocio='.$_SESSION['id_negocio'];}
		$Result = ViewUserSucursales::find(array(
			"columns" => "*",
		    "conditions" => 'status!=3 ',
		));				
		if( count($Result) > 0 ){
			foreach($Result as $key => $value){
				$Buttons = '';
				if( (int) $_SESSION["PermisosUser"][$this->Modulo . "_edit"] == 1 ){
					$Buttons .= '<a href="usuarios/edit/'.(int) trim($value->id).'/" class="btn btn-sm btn-icon btn-primary"><i class="fa fa-pencil"></i></a>';
				}
				if( (int) $_SESSION["PermisosUser"][$this->Modulo . "_delete"] == 1 ){
					$Buttons .= '<a href="del/'.(int) trim($value->id).'/" class="delete_usuarios btn btn-sm btn-icon btn-danger"><i class="fa fa-trash-o"></i></a>';
				}

				$Data["aaData"][] = array(
					"id" => trim($value->id),
					"estado" => trim($value->estado),
					"negocio" => trim($value->negocio),
					"sucursal" => trim($value->sucursal),
					"nombre" => trim($value->nombre) . " " . trim($value->apellido),
					"email" => trim($value->email),
					"telefono" => trim($value->telefono),					
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


	public function getusuariosAction(){
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

		//if($_SESSION['tipo']=="Administrador"){ $condiciones='status!=3 and tipo="Administrador"  ';}
		//if($_SESSION['tipo']!="Administrador"){ $condiciones='status!=3 and id_negocio='.$_SESSION['id_negocio'];}
		$Result = Usuarios::find(array(
			"columns" => "*",
		    "conditions" => 'status!=3 and tipo="APP"  ',
		));				
		if( count($Result) > 0 ){
			foreach($Result as $key => $value){
				$Buttons = '';
				if( (int) $_SESSION["PermisosUser"][$this->Modulo . "_edit"] == 1 ){
					$Buttons .= '<a href="usuarios/edit/'.(int) trim($value->id).'/" class="btn btn-sm btn-icon btn-primary"><i class="fa fa-pencil"></i></a>';
				}
				if( (int) $_SESSION["PermisosUser"][$this->Modulo . "_delete"] == 1 ){
					$Buttons .= '<a href="del/'.(int) trim($value->id).'/" class="delete_usuarios btn btn-sm btn-icon btn-danger"><i class="fa fa-trash-o"></i></a>';
				}

				$Data["aaData"][] = array(
					"id" => trim($value->id),
					"nombre" => trim($value->nombre) . " " . trim($value->apellido),
					"email" => trim($value->email),
					"os" => trim($value->os),					
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
		$this->ajaxBody("Usuarios");
		$this->setHeaderMenu("Configuraciones" , "Usuarios" , "usuarios" , "");
		$this->view->msjResponse = "";
		return true;
	}




	public function getPermisosForm(){
		$Permisos = array();
		$Modulos = array();
		foreach($_SESSION["PermisosUser"] as $key => $value){
			$Permisos[$key] = $key;
		}
		unset($Permisos["id"]);
		unset($Permisos["id_usuario"]);
		foreach($Permisos as $key => $value){
			if( strstr($key , "mod_") ){
				$Modulos[str_replace("mod_" , "mod_" , $key)] = array();
			}
		}
		foreach($Permisos as $key => $value){
			if( strstr($key , "sub_") ){
				$Explode = explode("_" , str_replace("sub_" , "" , $key));	
				$Modulos["mod_" . $Explode[0]][] = $key;
			}
		}		
		return $Modulos;
	}


	



	public function setPermisosPost(){
		$Permisos = array();
		foreach($_POST as $key => $value){
			if( strstr($key , "permiso_") ){
				$Name = str_replace("permiso_" , "" , $key);
				$Permisos[$Name] = $this->request->getPost($key , "int");
			}
		}
		return $Permisos;
	}



	public function activeChecked($name , $Data){
		$js = "<script type=\"text/javascript\">";
		if( isset($Data) ){
			foreach($Data as $key => $value){
				if( strstr($key , "permiso_") && $value == 1 ){
					$js .= "
						if($('#".$name." #".$key."').length){
							$('#".$name." #".$key."').attr('checked' , 'checked');
						}
					";
				}
			}
		}
		$js .= '</script>';
		return $js;	
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

		//sucursales x negocio
		

		$condicion='';
		if($_SESSION['tipo']=='Negocio'){$condicion="and id_negocio='".$_SESSION['id_negocio']."' ";}

		$Sucursales = array();
		$Table = new Sucursales();
		$Result_sucursales = $Table->find(array(
				"columns" => "id,nombre",
			    "conditions" => "status=1 ".$condicion." order by nombre ASC",			    	    
		));
		foreach($Result_sucursales as $value){
			$Sucursales[] = array("id" => $value->id ,"nombre" => $value->nombre);
		}		
		$this->view->Sucursales = $Sucursales;

		





		if($this->request->isPost()){
			$this->clearPost();
			$this->clearPostInt(array("status"));
			$Usuarios = new Usuarios();
			$Result = $Usuarios->find(array(
				"columns" => "id",
			    "conditions" => "user=:user: OR email=:email:",
			    "bind" => array("user" => $this->request->getPost("user" , "string") , "email" => $this->request->getPost("email" , "string")),
			    "bindTypes" => array("user" => Column::BIND_PARAM_STR , "email" => Column::BIND_PARAM_STR),
			    "limit" => 1
			));
			

			if( count($Result) <= 0 ){
				$_POST["fecha"] = date("Y-m-d H:i:s");
				if($_SESSION['tipo']=='Negocio'){$_POST['id_negocio']=$_SESSION['id_negocio'];}
				$_POST['id_estado']=$_SESSION['id_estado'];	
				$_POST['id_user']=$_SESSION['id'];
				$_POST['password']=sha1($_POST['password']);


				//echo "<pre>";
				//print_r($_POST);
				//print_r($_SESSION);
				//exit();



				$Usuarios->assign($this->request->getPost());
							
				


				if($Usuarios->save()){
					//permisos
					if($_SESSION['tipo']=='Administrador'){
						$PermisosData = $this->setPermisosPost();
					}else{

						//checamos el tipo de usuario nuevo					

						if($_POST['tipo']=='Tablet'){							
							$_POST['mod_home']=1;
							$_POST['sub_home_home']=1;
							$_POST['mod_tablet']=1;
							$_POST['sub_tablet_tablet']=1;
						}

						if($_POST['tipo']=='Vendedor'){
							$_POST['mod_home']=1;
							$_POST['sub_home_home']=1;
							$_POST['mod_vendedor']=1;
							$_POST['sub_vendedor_vendedor']=1;
						}

					}



					$PermisosData["id_usuario"] = $Usuarios->id;
					$Permisos = new Permisos();
					//echo '<pre>';print_r($PermisosData);echo "</pre>";exit();
					$Permisos->assign($PermisosData);
					$Permisos->save();
					$this->session->set("mensajeReturn" , $this->msjReturn("&Eacute;xito" , "Se guardo el registro correctamente." , "success"));
					$this->response->redirect("usuarios/");
					$this->view->disable();
					return false;
				}
				$this->view->msjResponse = $this->msjReturn("Error" , "Ocurrio un error , intente de nuevo." , "error");
				$this->view->jsResponse = $this->setValueData("formulario_registro" , $_POST);
				$this->view->jsResponse .= $this->activeChecked("formulario_registro" , $_POST);
			} else {
				$this->view->msjResponse = $this->msjReturn("Error" , "Existe un registro con los mismos datos." , "error");
				$this->view->jsResponse = $this->setValueData("formulario_registro" , $_POST);
				$this->view->jsResponse .= $this->activeChecked("formulario_registro" , $_POST);
			}
		}
		$this->view->Permisos = $this->getPermisosForm();
		$this->ajaxBody("Usuarios");
		$this->setHeaderMenu("Configuraciones" , "Usuarios" , "usuarios" , "Nuevo");
		$this->setActionSubMenu(0 , "usuarios/sub_menu.phtml");
		$this->view->formAction = "usuarios/new/";
		$Permisos = new PermisosData();
		$this->view->jsResponse .= $Permisos->checkForm("PermisosClear" , "formulario_registro" , $Permisos->Get("Alls"));
		$this->view->jsResponse .= $Permisos->checkForm("PermisosAdministrador" , "formulario_registro" , $Permisos->Get("Administrador"));
		$this->view->jsResponse .= $Permisos->checkForm("PermisosAlumno" , "formulario_registro" , $Permisos->Get("Alumno"));
		return true;
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
			$Usuarios = Usuarios::find(array(
				"columns" => "id",
			    "conditions" => "id!=:id: AND (user=:user: OR email=:email:)",
			    "bind" => array("id" => $idPost , "user" => $this->request->getPost("user" , "string") , "email" => $this->request->getPost("email" , "string")),
			    "bindTypes" => array("id" => Column::BIND_PARAM_INT , "user" => Column::BIND_PARAM_STR , "email" => Column::BIND_PARAM_STR),
			    "limit" => 1
			));
			if( count($Usuarios) <= 0 ){
				$_POST["ultimo_acceso"] = date("Y-m-d H:i:s");
				$Usuarios = Usuarios::findFirst(array(
					"columns" => "*",
				    "conditions" => "id=:id:",
				    "bind" => array("id" => $idPost),
				    "bindTypes" => array("id" => Column::BIND_PARAM_INT),
				    "limit" => 1
				));
				$Usuarios->assign($this->request->getPost());
				if($Usuarios->update()){
					$PermisosData = $this->setPermisosPost();
					$PermisosData["id_usuario"] = $Usuarios->id;
					Permisos::findFirst("id_usuario=".$Usuarios->id)->delete();
					$Permisos = new Permisos();
					//echo '<pre>';print_r($PermisosData);echo "</pre>";
					$Permisos->assign($PermisosData);
					//echo '<pre>';print_r($PermisosData);echo "</pre>";
					//if($Permisos->save()) echo 'bien';
					//else echo 'mal';
					//exit();
					//echo '<pre>';print_r($Permisos);echo '</pre>';exit();
					//echo '<pre>';print_r($PermisosData);echo "</pre>";exit();}
					$Permisos->save();
					$this->session->set("mensajeReturn" , $this->msjReturn("&Eacute;xito" , "Se edito el registro correctamente." , "success"));
					$this->response->redirect("usuarios/");
					$this->view->disable();
					return false;
				}
				$this->view->msjResponse = $this->msjReturn("Error" , "Ocurrio un error , intente de nuevo." , "error");
				$this->view->jsResponse = $this->setValueData("formulario_registro" , $_POST);
				$this->view->jsResponse .= $this->activeChecked("formulario_registro" , $_POST);
			} else {
				$this->view->msjResponse = $this->msjReturn("Error" , "Existe un registro con los mismos datos." , "error");
				$this->view->jsResponse = $this->setValueData("formulario_registro" , $_POST);
				$this->view->jsResponse .= $this->activeChecked("formulario_registro" , $_POST);
			}
		}
		$this->view->Permisos = $this->getPermisosForm();
		$this->ajaxBody("Usuarios");
		$this->setHeaderMenu("Configuraciones" , "Usuarios" , "usuarios" , "Editar");
		$this->setActionSubMenu(0 , "usuarios/sub_menu.phtml");
		$this->view->formAction = "usuarios/edit/" .$idPost;
		$DataForm = array("data" => array() , "permisos" => array());
		$Usuarios = new Usuarios();
		$Result = $Usuarios->find(array(
			"columns" => "*",
		    "conditions" => "id=:id:",
		    "bind" => array("id" => $idPost),
		    "bindTypes" => array("id" => Column::BIND_PARAM_INT),
		    "limit" => 1
		));
		if( count($Result) <= 0 ){
			$this->response->redirect("usuarios/");
			$this->view->disable();
			return false;			
		}
		foreach($Result as $value){
			$DataForm["data"] = array(
				"id" => $value->id,
				"nombre" => $value->nombre,
				"apellido" => $value->apellido,
				"user" => $value->user,
				"pass" => $value->pass,
				"pass2" => $value->pass,
				"tipo" => $value->tipo,
				"email" => $value->email,
				"status" => $value->status,
			);
		}
		$Permisos = new Permisos();
		$Result = $Permisos->find(array(
			"columns" => "*",
		    "conditions" => "id_usuario=:id:",
		    "bind" => array("id" => $idPost),
		    "bindTypes" => array("id" => Column::BIND_PARAM_INT),
		    "limit" => 1
		));
		if( count($Result) <= 0 ){
			$this->response->redirect("usuarios/");
			$this->view->disable();
			return false;			
		}
		foreach($Result as $value){
			$DataForm["permisos"] = json_decode(json_encode($value), true);
			unset($DataForm["permisos"]["id"]);
			unset($DataForm["permisos"]["id_usuario"]);
		}
		foreach($DataForm["permisos"] as $key => $value){
			$DataForm["permisos"]["permiso_".$key] = (int) $value;
			unset($DataForm["permisos"][$key]);
		}		
		$this->view->jsResponse = $this->setValueData("formulario_registro" , $DataForm["data"]);
		$this->view->jsResponse .= $this->activeChecked("formulario_registro" , $DataForm["permisos"]);	
		$Permisos = new PermisosData();
		$this->view->jsResponse .= $Permisos->checkForm("PermisosClear" , "formulario_registro" , $Permisos->Get("Alls"));
		$this->view->jsResponse .= $Permisos->checkForm("PermisosAdministrador" , "formulario_registro" , $Permisos->Get("Administrador"));
		$this->view->jsResponse .= $Permisos->checkForm("PermisosAlumno" , "formulario_registro" , $Permisos->Get("Alumno"));		
		return true;
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
		$idPost = str_replace("del/" , "" , $idPost);
		$idPost = str_replace("/" , "" , $idPost);

		$_POST["status"] = 3;
		$Tabla = Usuarios::findFirst(array(
			"columns" => "*",
		    "conditions" => "id=".(int) $idPost,
		    "limit" => 1
		));				
		$Tabla->assign(array("status" => 3));
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