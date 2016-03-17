<?php

use Phalcon\Mvc\Controller;
use Phalcon\Mvc\Dispatcher;
use Phalcon\Tag as Tag;
use Phalcon\Db\Column;
use Phalcon\Mvc\Model\Criteria;
use Phalcon\Http\Request;
use Phalcon\Mvc\Model;





class ControllerBase extends Controller {
	

	private $Security ;
	var $MenuSlider = array();

	public function initialize() {
		Tag::prependTitle('MD Consultoria | ');
		$this->Security = new Security();
		$this->Modulo = "";
	}




	public function onConstruct(){
		$this->view->baseTag = $this->url->getBaseUri();
		$this->view->session_name = $this->session->get("nombre") . " " . $this->session->get("apellido");
		$this->view->session_id_fb = $this->session->get("id_fb");
		$this->view->session_tipo = $this->session->get("tipo");
		$this->view->session_path = $_SERVER["REQUEST_URI"];
		$this->view->Menu = $this->getMenu();

		$this->setMenu();
		$this->view->Menu = $this->view->menu;

		//echo "<pre>"; print_r($this->view->Menu); echo "</pre>";
		//echo "<pre>"; print_r($_SESSION); echo "</pre>"; exit();

		
		$this->view->getHeaderMenu = "";
		$this->view->getShowSubMenu = "collapsed";
		$this->view->getHtmlSubMenu = "";
		$this->view->titleAjax = "";
		$this->Modulo = "";
		$this->view->jsFile = "js/scripts/clear.js";
		$this->extIMG = array("image/gif" , "image/jpeg" , "image/jpg" , "image/pjpeg" , "image/x-png" , "image/png");
		$this->extensionIMG = array("image/gif" => ".gif" , "image/jpeg" => ".jpg" , "image/jpg" => ".jpg" , "image/pjpeg" => ".jpg" , "image/x-png" => ".png" , "image/png" => ".png");
		//$this->setLogsData();
		//echo $this->view->Menu; exit();
	}




	public function setLogsData(){
		$url = $_SERVER["REQUEST_URI"];
		$data = "3";
		foreach($_REQUEST as $key => $value){
			$data .= $key . "=" . $value . "&";
		}
		$LogsData = new LogsData();
		$LogsData->assign(array(
			"id_usuario" => (int) $_SESSION["id"] , 
			"url" => trim($url) ,
			"data" => trim($data) ,
			"fecha" => date("Y-m-d H:i:s")
		));
		$LogsData->save();
	}













	public function setJS($js){
		$this->view->jsFile = "js/scripts/".$js.".js";
	}


	public function getMenu(){
		$Html = "";
		if( $this->session->has("id") && $this->session->has("menu") ){
			$Html = $this->session->get("menu");
		}
		return $Html;
	}



	public function setMenu(){
		$Html = "";
		if( $this->session->has("id") ){
			//if( !$this->session->has("menu") ){
			if( !$this->session->has("menu_".uniqid()) ){

				$Menus = new Menus();
				$Menu = $Menus->Menu;
				$Sql = "
					SELECT 
						*
					FROM
						permisos
					WHERE 
						id_usuario=".(int) $this->session->get("id")."
					LIMIT 1
				";
				$DataSql = array();
				$result = $this->db->query($Sql);
				$result->setFetchMode(Phalcon\Db::FETCH_ASSOC);
				while ($Row = $result->fetchArray()){
					$Permisos = $Row;
				}
				$MenuPermisos = array();
				$Html = '';
				$PrimerMenu = "";
				$PermisosUser = array();
				foreach($Permisos as $key => $value){
					$PermisosUser[$key] = (int) $value;
				}				
				foreach($Menu as $key => $value){
					if( isset($Permisos[("sub_".$value["module"]."_".$value["controller"])]) && isset($Permisos[("mod_".$value["module"])]) && $value["show"] == 1 && $value["sub_menu"] == 0 && $Permisos[("mod_".$value["module"])] == 1 && $Permisos[("sub_".$value["module"]."_".$value["controller"])] == 1 ){
						if( $PrimerMenu == "" ){
							if( trim($value["action"]) == "index" ){
								$PrimerMenu = trim($value["controller"]).'/';
							} else {
								$PrimerMenu = trim($value["controller"]).'/'.trim($value["action"]).'/';
							}
						}
						if( trim($value["action"]) == "index" ){
							$Html .= '
									<li class="">
				                      <a href="'.trim($value["controller"]).'/" class="auto">
				                        <i class="i '.trim($value["icon"]).' icon">
				                        </i>
				                        <span class="font-bold">'.trim($value["name"]).'</span>
				                      </a>
				                    </li>
				                   	';
						} else {
							$Html .= '
									<li class="">
				                      <a href="'.trim($value["controller"]).'/'.trim($value["action"]).'/" class="auto">
				                        <i class="i '.trim($value["icon"]).' icon">
				                        </i>
				                        <span class="font-bold">'.trim($value["name"]).'</span>
				                      </a>
				                    </li>
				                   	';							
						}						
						$MenuPermisos[] = $value;
					} else if( $value["sub_menu"] == 1 && $Permisos[("mod_".$value["module"])] == 1 ){
						//$Html .= '<li class="dropdown"><a href="#" class="dropdown-toggle" data-toggle="dropdown">'.trim($value["name"]).' <b class="caret"></b></a><ul class="dropdown-menu">';
						$Html .= '
									<li >
				                      <a href="'.$_SERVER["REQUEST_URI"].'#" class="auto">
				                        <span class="pull-right text-muted">
				                          <i class="i i-circle-sm-o text"></i>
				                          <i class="i i-circle-sm text-active"></i>
				                        </span>
				                        <i class="i i-stack icon">
				                        </i>
				                        <span class="font-bold">'.trim($value["name"]).'</span>
				                      </a>
				                      <ul class="nav dk">
						';
						foreach($value["data"] as $_key => $_value){
							if( isset($Permisos[("sub_".$value["module"]."_".$_value["controller"])]) && ($_value["show"] == 1 && $Permisos[("sub_".$value["module"]."_".$_value["controller"])] == 1) ){
								if( trim($_value["action"]) == "index" ){
									$Html .= '
											<li>
					                          <a href="'.trim($_value["controller"]).'/" class="auto">                                                        
					                            <i class="i i-dot"></i>
					                            <span>'.trim($_value["name"]).'</span>
					                          </a>
					                        </li>
									';
									if( $PrimerMenu == "" ){
										$PrimerMenu = trim($_value["controller"]).'/';
									}
								} else {
									$Html .= '
											<li>
					                          <a href="'.trim($_value["controller"]).'/'.trim($_value["action"]).'/" class="auto">                                                        
					                            <i class="i i-dot"></i>
					                            <span>'.trim($_value["name"]).'</span>
					                          </a>
					                        </li>
									';
									if( $PrimerMenu == "" ){
										$PrimerMenu = trim($_value["controller"]).'/'.trim($_value["action"]).'/';
									}									
								}
							} else {
								unset($value["data"][$_key]);
							}
						}
						$MenuPermisos[] = $value;
						$Html .= '</ul></li>';
					}
				}
				$this->view->PrimerMenu = $PrimerMenu;
				$this->view->menu = $Html;
				$this->view->PermisosUser = $PermisosUser;
				$this->session->set("PrimerMenu" , $PrimerMenu);
				$this->session->set("menu" , $Html);
				$this->session->set("PermisosUser" , $PermisosUser);
			} else {
				$this->view->PrimerMenu = $this->session->get("PrimerMenu");
				$this->view->menu = $this->session->get("menu");
				$this->view->PermisosUser = $_SESSION["PermisosUser"];				
			}
		}
	}



	public function setHeaderMenu($Module="" , $Controller="" , $Url="" , $Action=""){
		if( $Module != "" && $Controller != "" && $Url != "" && $Action == "" ){
			$this->view->getHeaderMenu = '
				<ul class="breadcrumb">
					<li><a href="'.trim($Url).'/">'.trim($Module).'</a></li>
					<li class="active"><a href="'.trim($Url).'/">'.trim($Controller).'</a></li>
				</ul>
			';
		} else if( $Module != "" && $Controller != "" && $Url == "" && $Action == "" ){
			$this->view->getHeaderMenu = '
				<ul class="breadcrumb">
					<li><a href="#">'.trim($Module).'</a></li>
					<li class="active"><a href="#">'.trim($Controller).'</a></li>
				</ul>
			';
		} else if( $Module != "" && $Controller != "" && $Url != "" && $Action != "" ){
			$this->view->getHeaderMenu = '
				<ul class="breadcrumb">
					<li><a href="'.trim($Url).'/">'.trim($Module).'</a></li>
					<li><a href="'.trim($Url).'/">'.trim($Controller).'</a></li>
					<li class="active"><a href="'.trim($Url).'/">'.trim($Action).'</a></li>
				</ul>
			';
		} else {
			$this->view->getHeaderMenu = "";
		}
	}



	public function setActionSubMenu($show=0 , $url=""){
		if( $show == 0 ){
			$this->view->getShowSubMenu = "collapsed";
			$this->view->getHtmlSubMenu = "";
		} else {
			$this->view->getShowSubMenu = "";
			if( $url == "" ){
				$this->view->getHtmlSubMenu = "";
			} else {
				$this->view->getHtmlSubMenu = $url;
			}
		}
	}



	public function newButton($Action){
		return '<a href="'.$Action.'"><button type="button" class="btn btn-default"><i class="fa fa-plus icon-only"></i></button></a>';
	}




	public function clearPost(){
		if( isset($_POST) ){
			foreach($_POST as $key => $value){
				$_POST[$key] = $this->request->getPost($key , "string");
			}
		}
	}

	public function clearPostInt($Data=array()){
		foreach($Data as $key){
			if( isset($_POST[$key]) ){
				$_POST[$key] = $this->request->getPost($key , "int");
			}
		}
	}



	public function msjReturn($title="" , $msj="" , $type="success"){
		return "<script  type=\"text/javascript\">
		//alert(1);
			$.gritter.add({
	            title: '".$title."',
	            text: '".$msj."',
				class_name: 'bg-".$type."',
	            sticky: false
			});
		</script>
		";
	}

	public function Miniaturas($img,$tamanio,$nombre,$directorio){
			//info de la imagen
			$info = getimagesize($img);
			$width= $info[0];
			$height= $info[1];
			$tipo= $info[2];
			//print_r($directorio);
			//exit();
			//---Dependiendo del tipo de imagen crear una nueva imagen
			switch($tipo){
				case IMAGETYPE_JPEG:
					$img = imagecreatefromjpeg($img);
					$ext=".jpg";
					break;
				case IMAGETYPE_PNG:
					$img = imagecreatefrompng($img);
					$ext=".png";
					break;
			}
			//redimencionamos
			$new_width = $tamanio;
			$new_height = floor( $height * ( $tamanio / $width ) );
			$tmp_img = imagecreatetruecolor( $new_width, $new_height );
			imagecopyresized( $tmp_img, $img, 0, 0, 0, 0, $new_width, $new_height, $width, $height );
			$nuevofile=$nombre;
			//exit();

			$Folder =  __DIR__  . "/../../../tmp/".$directorio."/";
			//exit();
			switch($tipo){
				case IMAGETYPE_JPEG:
					imagejpeg( $tmp_img,$Folder.$nuevofile);
					break;
				case IMAGETYPE_PNG:
					imagepng( $tmp_img,$Folder.$nuevofile);
					break;
			}	

	}



	public function setValueData($name="formulario" , $Data=array()){
		$js = "<script type=\"text/javascript\">";
		if( isset($Data) ){
			foreach($Data as $key => $value){
				$js .= "
					if($('#".$name." #".$key."').length){
						$('#".$name." #".$key."').val('".$value."');
					}
				";
			}
		}
		$js .= '</script>';
		return $js;	
	}


	public function setValueDataIndividual($name="formulario" , $Data=array()){
		$js = "<script type=\"text/javascript\">";
		if( isset($Data) ){
			foreach($Data as $key => $value){
				$js .= "
					if($('#".$name." #".$key."').length){
						$('#".$name." #".$key."').val('".$value."');
					}
				";
			}
		}
		$js .= '</script>';
		return $js;	
	}




	public function setSlug($id_slug=0 , $slug=""){
		$Slugs = new Slugs();
		$Slugs->assign(array("id_slug" => $id_slug , "slug" => $slug));
		$Slugs->save();
	}


	public function getDepartamentos(){
		$Data = array();
		$Table = new Departamentos();
		$Result = $Table->find(array(
		    "conditions" => "status=1",
		    "order" => "nombre asc"
		));
		foreach($Result as $value){
			$Data[] = array("id" => $value->id , "name" => $value->nombre);
		}
		return $Data;
	}




	public function getCategorias(){
		$Data = array();
		$Table = new ClasesCategorias();
		$Result = $Table->find(array(
		    "conditions" => "status=1",
		    "order" => "nombre asc"
		));
		foreach($Result as $value){
			$Data[] = array("id" => $value->id , "name" => $value->nombre);
		}
		return $Data;
	}


	public function getCategoriasForo(){
		$Data = array();
		$Table = new ForoCategorias();
		$Result = $Table->find(array(
		    "conditions" => "status=1",
		    "order" => "nombre asc"
		));
		foreach($Result as $value){
			$Data[] = array("id" => $value->id , "name" => $value->nombre);
		}
		return $Data;
	}


	public function getCategoriasWiki(){
		$Data = array();
		$Table = new WikiCategorias();
		$Result = $Table->find(array(
		    "conditions" => "status=1",
		    "order" => "nombre asc"
		));
		foreach($Result as $value){
			$Data[] = array("id" => $value->id , "name" => $value->nombre);
		}
		return $Data;
	}




	public function listado_puestosAction($idPost=0){
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
		$Data = array();
		$Table = new Puestos();
		$Result = $Table->find(array(
			"columns" => "id , nombre",
		    "conditions" => "status=1 and id_departamento=:id_departamento:",
			"bind" => array("id_departamento" => $idPost),
			"bindTypes" => array("id_departamento" => Column::BIND_PARAM_INT),		    
		    "order" => "nombre DESC"
		));
		foreach($Result as $value){
			$Data[] = array("id" => $value->id , "name" => $value->nombre);
		}
		$this->view->disable();
		$this->response->setContentType('application/json', 'UTF-8');
        $response = new \Phalcon\Http\Response();
        $response->setContent(json_encode($Data));
        return $response;
	}	




	public function clases_subcategoriasAction($idPost=0){
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
		$Data = array();
		$Table = new ClasesSubcategorias();
		$Result = $Table->find(array(
			"columns" => "id , nombre",
		    "conditions" => "status=1 and id_categoria=:id_categoria:",
			"bind" => array("id_categoria" => $idPost),
			"bindTypes" => array("id_categoria" => Column::BIND_PARAM_INT),		    
		    "order" => "nombre DESC"
		));
		foreach($Result as $value){
			$Data[] = array("id" => $value->id , "name" => $value->nombre);
		}
		$this->view->disable();
		$this->response->setContentType('application/json', 'UTF-8');
        $response = new \Phalcon\Http\Response();
        $response->setContent(json_encode($Data));
        return $response;
	}	




	public function foro_subcategoriasAction($idPost=0){
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
		$Data = array();
		$Table = new ForoSubcategorias();
		$Result = $Table->find(array(
			"columns" => "id , nombre",
		    "conditions" => "status=1 and id_categoria=:id_categoria:",
			"bind" => array("id_categoria" => $idPost),
			"bindTypes" => array("id_categoria" => Column::BIND_PARAM_INT),		    
		    "order" => "nombre DESC"
		));
		foreach($Result as $value){
			$Data[] = array("id" => $value->id , "name" => $value->nombre);
		}
		$this->view->disable();
		$this->response->setContentType('application/json', 'UTF-8');
        $response = new \Phalcon\Http\Response();
        $response->setContent(json_encode($Data));
        return $response;
	}	







	public function getClasesTags($id=0){
		$Data = array();
		$Table = new ClasesTags();
		$Result = $Table->find(array(
		    "conditions" => "id_clase=".(int) $id,
		));
		foreach($Result as $value){
			$Data[] = array("id" => $value->id , "slug" => $value->slug , "titulo" => $value->titulo);
		}
		return $Data;
	}



	public function getForoTags($id=0){
		$Data = array();
		$Table = new ForoTags();
		$Result = $Table->find(array(
		    "conditions" => "id_clase=".(int) $id,
		));
		foreach($Result as $value){
			$Data[] = array("id" => $value->id , "slug" => $value->slug , "titulo" => $value->titulo);
		}
		return $Data;
	}




	public function create_slug($string){
	   $slug=preg_replace('/[^A-Za-z0-9-]+/', '-', $string);
	   return $slug;
	}



	public function createMenuSliderClases($Cat="" , $Sub=""){
		$Tabla = ViewSubcategorias::find(array(
			"columns" => "*",
		    "conditions" => "status=1",
		    "order" => "categoria asc , nombre asc",
		    //"limit" => 1
		));
		$Data = array();

		if( $_SESSION["tipo"] == "Administrador" ){
			$Data["Administraci&oacute;n|Administraci&oacute;n"][] = array(
				"slug_categoria" => "Administraci&oacute;n",
				"slug" => "admin_clases_categorias",
				"nombre" => "Categorias",
				"type" => "Admin"
			);
			$Data["Administraci&oacute;n|Administraci&oacute;n"][] = array(
				"slug_categoria" => "Administraci&oacute;n",
				"slug" => "admin_clases_subcategorias",
				"nombre" => "SubCategorias",
				"type" => "Admin"
			);
			$Data["Administraci&oacute;n|Administraci&oacute;n"][] = array(
				"slug_categoria" => "Administraci&oacute;n",
				"slug" => "admin_clases_clases",
				"nombre" => "Clases",
				"type" => "Admin"
			);
		}
		$Iconos = array();
		$Iconos["Administraci&oacute;n"] = "fa-user";
		foreach($Tabla as $key => $value){
			$Data[trim($value->categoria) . "|" . trim($value->slug_categoria)][] = array(
				"id" => trim($value->id),
				"id_categoria" => trim($value->id_categoria),
				"slug" => trim($value->slug),
				"slug_categoria" => trim($value->slug_categoria),
				"categoria" => trim($value->categoria),
				"nombre" => trim($value->nombre),
				"fecha" => trim($value->fecha),
				"status_categoria" => trim($value->status_categoria),
				"status" => trim($value->status),
				"icono" => trim($value->icono),
				"type" => "normal"
			);
			$Iconos[trim($value->slug_categoria)] = trim($value->icono);
		}
		$Html = "";
		$X = 0;
		foreach($Data as $key => $value){
			$Explode = explode("|" , $key);
			$key = trim($Explode[0]);
			$keyAdmin = trim($Explode[1]);
			$openCat = "";
			$openSub = "";
			if( $keyAdmin == $Cat ){
				$openCat = "open";
			}
			if( $_SESSION["tipo"] != "Administrador" ){
				if( $X == 0 ){
					$openSub = "";
					if( "inicio" == $Sub ){
						$openSub = "active";
					}	
					$Html .= '<li>';
					$Html .= '<a class="'.$openSub.'" href="./clases/">';
					$Html .= '<i class="fa fa-home"></i> Inicio';
					$Html .= '</a>';
					$Html .= '</li>';
					$X++;
				}
			}
			$Html .= '<li class="panel '.$openCat.'">';
			$Html .= '<a href="javascript:;" data-parent="#side" data-toggle="collapse" class="accordion-toggle" data-target="#'.$key.'">';
			$Html .= '<i class="fa '.$Iconos[$keyAdmin].'"></i> '.$key.' ';
			$Html .= '<span class="fa arrow"></span></a>';
			$Html .= '<ul class="collapse nav" id="'.$key.'">';
			foreach($value as $_key => $_value){
				if( $_value["slug"] == $Sub ){
					$openSub = "active";
				}				
				$Html .= '<li>';
				if( $_value["type"] == "Admin" ){
					$Html .= '	<a class="'.$openSub.'" href="./'.$_value["slug"].'">';
				} else {
					$Html .= '	<a class="'.$openSub.'" href="./clases/?cat='.$_value["slug_categoria"].'&sub='.$_value["slug"].'">';	
				}
				$Html .= '		<i class="fa fa-angle-double-right"></i> '.$_value["nombre"].'';
				$Html .= '	</a>';
				$Html .= '</li>';
				$openCat = "";
				$openSub = "";
			}
			$Html .= '</ul></li>';
			if( $_SESSION["tipo"] == "Administrador" ){
				if( $keyAdmin == "Administraci&oacute;n" ){
					$openSub = "";
					if( "inicio" == $Sub ){
						$openSub = "active";
					}	
					$Html .= '<li>';
					$Html .= '<a class="'.$openSub.'" href="./clases/">';
					$Html .= '<i class="fa fa-home"></i> Inicio';
					$Html .= '</a>';
					$Html .= '</li>';				
				}
			}
		}
		$this->view->MenuSlider = $this->MenuSlider;
		$this->view->menuCategoriasClases = $Html;
	}








	public function createMenuSliderForo($Cat="" , $Sub=""){
		$Tabla = ViewSubcategoriasForo::find(array(
			"columns" => "*",
		    "conditions" => "status=1",
		    "order" => "categoria asc , nombre asc",
		    //"limit" => 1
		));
		$Data = array();

		if( $_SESSION["tipo"] == "Administrador" ){
			$Data["Administraci&oacute;n|Administraci&oacute;n"][] = array(
				"slug_categoria" => "Administraci&oacute;n",
				"slug" => "admin_foro_categorias",
				"nombre" => "Categorias",
				"type" => "Admin"
			);
			$Data["Administraci&oacute;n|Administraci&oacute;n"][] = array(
				"slug_categoria" => "Administraci&oacute;n",
				"slug" => "admin_foro_subcategorias",
				"nombre" => "SubCategorias",
				"type" => "Admin"
			);
			$Data["Administraci&oacute;n|Administraci&oacute;n"][] = array(
				"slug_categoria" => "Administraci&oacute;n",
				"slug" => "admin_foro_clases",
				"nombre" => "Foro",
				"type" => "Admin"
			);
		}
		$Iconos = array();
		$Iconos["Administraci&oacute;n"] = "fa-user";
		foreach($Tabla as $key => $value){
			$Data[trim($value->categoria) . "|" . trim($value->slug_categoria)][] = array(
				"id" => trim($value->id),
				"id_categoria" => trim($value->id_categoria),
				"slug" => trim($value->slug),
				"slug_categoria" => trim($value->slug_categoria),
				"categoria" => trim($value->categoria),
				"nombre" => trim($value->nombre),
				"fecha" => trim($value->fecha),
				"status_categoria" => trim($value->status_categoria),
				"status" => trim($value->status),
				"icono" => trim($value->icono),
				"type" => "normal"
			);
			$Iconos[trim($value->slug_categoria)] = trim($value->icono);
		}
		$Html = "";
		$X = 0;
		foreach($Data as $key => $value){
			$Explode = explode("|" , $key);
			$key = trim($Explode[0]);
			$keyAdmin = trim($Explode[1]);
			$openCat = "";
			$openSub = "";
			if( $keyAdmin == $Cat ){
				$openCat = "open";
			}
			if( $_SESSION["tipo"] != "Administrador" ){
				if( $X == 0 ){
					$openSub = "";
					if( "inicio" == $Sub ){
						$openSub = "active";
					}	
					$Html .= '<li>';
					$Html .= '<a class="'.$openSub.'" href="./foro/">';
					$Html .= '<i class="fa fa-home"></i> Inicio';
					$Html .= '</a>';
					$Html .= '</li>';
					$openSub = "";	
					if( "add" == $Sub ){
						$openSub = "active";
					}
					$Html .= '<li>';
					$Html .= '<a class="'.$openSub.'" href="./foro/add">';
					$Html .= '<i class="fa fa-home"></i> Crear Tema';
					$Html .= '</a>';
					$Html .= '</li>';					
					$X++;
				}
			}
			
			$Html .= '<li class="panel '.$openCat.'">';
			$Html .= '<a href="javascript:;" data-parent="#side" data-toggle="collapse" class="accordion-toggle" data-target="#'.$key.'">';
			$Html .= '<i class="fa '.$Iconos[$keyAdmin].'"></i> '.$key.' ';
			$Html .= '<span class="fa arrow"></span></a>';
			$Html .= '<ul class="collapse nav" id="'.$key.'">';
			foreach($value as $_key => $_value){
				if( $_value["slug"] == $Sub ){
					$openSub = "active";
				}				
				$Html .= '<li>';
				if( $_value["type"] == "Admin" ){
					$Html .= '	<a class="'.$openSub.'" href="./'.$_value["slug"].'">';
				} else {
					$Html .= '	<a class="'.$openSub.'" href="./foro/?cat='.$_value["slug_categoria"].'&sub='.$_value["slug"].'">';	
				}
				$Html .= '		<i class="fa fa-angle-double-right"></i> '.$_value["nombre"].'';
				$Html .= '	</a>';
				$Html .= '</li>';
				$openCat = "";
				$openSub = "";
			}
			$Html .= '</ul></li>';
			if( $_SESSION["tipo"] == "Administrador" ){
				if( $keyAdmin == "Administraci&oacute;n" ){
					$openSub = "";
					if( "inicio" == $Sub ){
						$openSub = "active";
					}	
					$Html .= '<li>';
					$Html .= '<a class="'.$openSub.'" href="./foro/">';
					$Html .= '<i class="fa fa-home"></i> Inicio';
					$Html .= '</a>';
					$Html .= '</li>';
					if( "inicio" == $Sub ){
						$openSub = "active";
					}
					$openSub = "";	
					if( "add" == $Sub ){
						$openSub = "active";
					}
					$Html .= '<li>';
					$Html .= '<a class="'.$openSub.'" href="./foro/add">';
					$Html .= '<i class="fa fa-home"></i> Crear Tema';
					$Html .= '</a>';
					$Html .= '</li>';

				}
			}
		}
		//exit();
		$this->view->MenuSlider = $this->MenuSlider;
		$this->view->menuCategoriasClases = $Html;
	}





	public function createMenuSliderSoporte($Cat="" , $Sub=""){
		$Html = "";
		$Html .= '<li>';
		$Html .= '<a class="active" href="./soporte/">';
		$Html .= '<i class="fa fa-home"></i> Soporte';
		$Html .= '</a>';
		$Html .= '</li>';
		//exit();
		$this->view->MenuSlider = $this->MenuSlider;
		$this->view->menuCategoriasClases = $Html;
	}










	public function createMenuSliderWiki($Cat="" , $Sub=""){
		$Data = array();

		if( $_SESSION["tipo"] == "Administrador" ){
			$Data["Administraci&oacute;n|Administraci&oacute;n"][] = array(
				"slug_categoria" => "Administraci&oacute;n",
				"slug" => "admin_wiki_categorias",
				"nombre" => "Categorias",
				"type" => "Admin"
			);
			$Data["Administraci&oacute;n|Administraci&oacute;n"][] = array(
				"slug_categoria" => "Administraci&oacute;n",
				"slug" => "admin_wiki_clases",
				"nombre" => "Wiki",
				"type" => "Admin"
			);
		}
		$Iconos = array();
		$Iconos["Administraci&oacute;n"] = "fa-user";

		$Html = "";
		$X = 0;
		foreach($Data as $key => $value){
			$Explode = explode("|" , $key);
			$key = trim($Explode[0]);
			$keyAdmin = trim($Explode[1]);
			$openCat = "";
			$openSub = "";
			if( $keyAdmin == $Cat ){
				$openCat = "open";
			}
			if( $_SESSION["tipo"] != "Administrador" ){
				if( $X == 0 ){
					$openSub = "";
					if( "inicio" == $Sub ){
						$openSub = "active";
					}	
					$Html .= '<li>';
					$Html .= '<a class="'.$openSub.'" href="./wiki/">';
					$Html .= '<i class="fa fa-home"></i> Inicio';
					$Html .= '</a>';
					$Html .= '</li>';
					$X++;
				}
			}
			$Html .= '<li class="panel '.$openCat.'">';
			$Html .= '<a href="javascript:;" data-parent="#side" data-toggle="collapse" class="accordion-toggle" data-target="#'.$key.'">';
			$Html .= '<i class="fa '.$Iconos[$keyAdmin].'"></i> '.$key.' ';
			$Html .= '<span class="fa arrow"></span></a>';
			$Html .= '<ul class="collapse nav" id="'.$key.'">';
			foreach($value as $_key => $_value){
				if( $_value["slug"] == $Sub ){
					$openSub = "active";
				}				
				$Html .= '<li>';
				if( $_value["type"] == "Admin" ){
					$Html .= '	<a class="'.$openSub.'" href="./'.$_value["slug"].'">';
				}
				$Html .= '		<i class="fa fa-angle-double-right"></i> '.$_value["nombre"].'';
				$Html .= '	</a>';
				$Html .= '</li>';
				$openCat = "";
				$openSub = "";
			}
			$Html .= '</ul></li>';
			if( $_SESSION["tipo"] == "Administrador" ){
				if( $keyAdmin == "Administraci&oacute;n" ){
					$openSub = "";
					if( "inicio" == $Sub ){
						$openSub = "active";
					}	
					$Html .= '<li>';
					$Html .= '<a class="'.$openSub.'" href="./wiki/">';
					$Html .= '<i class="fa fa-home"></i> Inicio';
					$Html .= '</a>';
					$Html .= '</li>';				
				}
			}
		}
		$this->view->MenuSlider = $this->MenuSlider;
		$this->view->menuCategoriasClases = $Html;
	}







	public function createMenuSliderMachineFans($Cat="" , $Sub=""){
		$openSub = "";
		if( "inicio" == $Sub ){
			$openSub = "active";
		}	
		$Html .= '<li>';
		$Html .= '<a class="'.$openSub.'" href="./machinefans/">';
		$Html .= '<i class="fa fa-home"></i> Inicio';
		$Html .= '</a>';
		$Html .= '</li>';
		$openSub = "";
		if( "grupos" == $Sub ){
			$openSub = "active";
		}	
		$Html .= '<li>';
		$Html .= '<a class="'.$openSub.'" href="./machinefans_grupos/">';
		$Html .= '<i class="fa fa-users"></i> Grupos';
		$Html .= '</a>';
		$Html .= '</li>';
		$openSub = "";
		if( "paginas" == $Sub ){
			$openSub = "active";
		}	
		$Html .= '<li>';
		$Html .= '<a class="'.$openSub.'" href="./machinefans_paginas/">';
		$Html .= '<i class="fa fa-facebook"></i> Paginas';
		$Html .= '</a>';
		$Html .= '</li>';
		$this->view->MenuSlider = $this->MenuSlider;
		$this->view->menuCategoriasClases = $Html;
	}



















	function haceCuanto($time) {
	    
	    $SECOND = 1;
	    $MINUTE= 60 * $SECOND;
	    $HOUR = 60 * $MINUTE;
	    $DAY = 24 * $HOUR;
	    $MONTH = 30 * $DAY;

	    $dif = time() - strtotime($time);
	    switch($dif){
	    case $dif < (1 * $MINUTE):
	    return $dif == 1 ? "hace un momento" : "hace " . $dif . " segundos ";
	    break;

	    case $dif < (2 * $MINUTE):
	    return "hace un minuto";
	    break;

	    case $dif < (45 * $MINUTE):
	    return "hace " . floor($dif / $MINUTE) . " minutos";
	    break;

	    case $dif < (90 * $MINUTE):
	    return "hace una hora";
	    break;

	    case $dif < (24 * $HOUR):
	    return "hace " . floor($dif / $HOUR) . " horas";
	    break;

	    case $dif < (48 * $HOUR):
	    return "ayer";
	    break;

	    case $dif < (30 * $DAY):
	    return "hace " . floor($dif / $DAY) . " dias";
	    break;

	    case $dif < (12 * $MONTH):
	    $months = floor($dif / $DAY / 30);
	    return $months <= 1 ? "el mes pasado" : "hace " . $months . " meses";
	    break;

	    default:
	    $years = floor($dif / $DAY / 365);
	    return $years <= 1 ? "el año pasado" : "hace " . $years . " años";
	    break;

	    }


	}





	public function getDomain(){
		if( strstr($_SERVER['SERVER_NAME'] , 'ingresosense.com') ){
			$BaseRef = "http://ingresosense.com/";
		} else if( strstr($_SERVER['SERVER_NAME'] , 'ingresosense.io') ){
			$BaseRef = "http://ingresosense.io/";
		} else if( strstr($_SERVER['SERVER_NAME'] , 'powersensesystem.com') ){
			$BaseRef = "http://powersensesystem.com/";
		} else if( strstr($_SERVER['SERVER_NAME'] , 'powersensesystem.io') ){
			$BaseRef = "http://powersensesystem.io/";
		} else if( strstr($_SERVER['SERVER_NAME'] , 'localhost') ){
			$BaseRef = "http://localhost/ingresosense/admin";
		} else if( strstr($_SERVER['SERVER_NAME'] , 'ingresosense') ){
			$BaseRef = "http://ingresosense/";	
		} else if( strstr($_SERVER['SERVER_NAME'] , '192.168.1.115') ){
			$BaseRef = "http://192.168.1.115/ingresosense/admin";
		} else if( strstr($_SERVER['SERVER_NAME'] , '192.168.1.81') ){
			$BaseRef = "http://192.168.1.81/ingresosense/admin";		
		} else {
			$BaseRef = "http://localhost/ingresosense/admin";
		}
		return $BaseRef;
	}





	public function selectStatus($status=0){
		if( $status == 0 ){
			$status = "Inactivo";
		} else if( $status == 1 ){
			$status = "Activo";
		} else if( $status == 2 ){
			$status = "Otro";
		} else if( $status == 3 ){
			$status = "Eliminado";
		} else {
			$status = "Inactivo";
		}
		return $status;
	}











	public function estadosAction($idPost=0){
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
		$Data = array();
		$Table = new Estados();
		$Result = $Table->find(array(
			"columns" => "id , nombre",
		    "conditions" => "status=1 and id_pais=:id_pais:",
			"bind" => array("id_pais" => $idPost),
			"bindTypes" => array("id_pais" => Column::BIND_PARAM_INT),		    
		    "order" => "nombre DESC"
		));
		foreach($Result as $value){
			$Data[] = array("id" => $value->id , "name" => $value->nombre);
		}
		$this->view->disable();
		$this->response->setContentType('application/json', 'UTF-8');
        $response = new \Phalcon\Http\Response();
        $response->setContent(json_encode($Data));
        return $response;
	}	



	public function municipiosAction($idPost=0){
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
		$Data = array();
		$Table = new Municipios();
		$Result = $Table->find(array(
			"columns" => "id , nombre",
		    "conditions" => "status=1 and id_estado=:id_estado:",
			"bind" => array("id_estado" => $idPost),
			"bindTypes" => array("id_estado" => Column::BIND_PARAM_INT),		    
		    "order" => "nombre DESC"
		));
		foreach($Result as $value){
			$Data[] = array("id" => $value->id , "name" => $value->nombre);
		}
		$this->view->disable();
		$this->response->setContentType('application/json', 'UTF-8');
        $response = new \Phalcon\Http\Response();
        $response->setContent(json_encode($Data));
        return $response;
	}	



	public function establecimientosAction($idPost=0){
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
		$Data = array();
		$Table = new Establecimientos();
		$Result = $Table->find(array(
			"columns" => "id , nombre",
		    "conditions" => "status=1 and id_municipio=:id_municipio:",
			"bind" => array("id_municipio" => $idPost),
			"bindTypes" => array("id_municipio" => Column::BIND_PARAM_INT),		    
		    "order" => "nombre DESC"
		));
		foreach($Result as $value){
			$Data[] = array("id" => $value->id , "name" => $value->nombre);
		}
		$this->view->disable();
		$this->response->setContentType('application/json', 'UTF-8');
        $response = new \Phalcon\Http\Response();
        $response->setContent(json_encode($Data));
        return $response;
	}	


	public function modulosAction($idPost=0){
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
		$Data = array();
		$Table = new Modulos();
		$Result = $Table->find(array(
			"columns" => "id , nombre",
		    "conditions" => "status=1 and id_establecimiento=:id_establecimiento:",
			"bind" => array("id_establecimiento" => $idPost),
			"bindTypes" => array("id_establecimiento" => Column::BIND_PARAM_INT),		    
		    "order" => "nombre DESC"
		));
		foreach($Result as $value){
			$Data[] = array("id" => $value->id , "name" => $value->nombre);
		}
		$this->view->disable();
		$this->response->setContentType('application/json', 'UTF-8');
        $response = new \Phalcon\Http\Response();
        $response->setContent(json_encode($Data));
        return $response;
	}	


	public function getSaldo($id){
		$Saldo = 0 ;
		$Tabla = new Saldos();
		$Result = $Tabla->find(array(
			"columns" => "saldo",
		    "conditions" => "id_usuario=:id:",
		    "bind" => array("id" => $id),
		    "bindTypes" => array("id" => Column::BIND_PARAM_INT),
		    "limit" => 1
		));
		foreach($Result as $value){
			$Saldo = $value->saldo;
		}
		return $Saldo;
	}




	public function createFolder($Folder){
		$Trash = $Folder . "trash/";
		$Thumbnail = $Folder . "thumbnail/";
		$Img = $Folder . "img/";
		@mkdir($Trash, 0777, true);
		@mkdir($Thumbnail, 0777, true);
		@mkdir($Img, 0777, true);
		return false;
	}


	public function ajaxBody($title="Power Sense System" , $home="home" , $ajax="ajax"){
		if($this->request->isAjax() == true){
			//$this->view->setLayout($ajax);
			$this->view->setLayout($home);
		} else {
			$this->view->setLayout($home);
		}
		$this->view->titleAjax = "Power Sense System | " . $title;
	}


	public function getHora($F){
		return mktime(date("H" , strtotime($F)) , date("i" , strtotime($F)) , date("s" , strtotime($F)) , date("m" , strtotime($F)) , date("d" , strtotime($F)) , date("Y" , strtotime($F))); 
	}


	public function getMinutos($fecha){
		$fecha1 = $this->getHora($fecha); 
		$fecha2 = $this->getHora(date("Y-m-d H:i:s")); 
		$diferencia = $fecha2-$fecha1; 
		$diff = array();
		$diff['minutos'] = (int)($diferencia/(60));
		$diff['horas'] = (int)($diferencia/(60*60)); 
		$diff['dias'] = (int)($diferencia/(60*60*24));
		return $diff;
	}

	public function getMinutosTwo($fecha , $fecha2){
		$fecha1 = $this->getHora($fecha); 
		$fecha2 = $this->getHora($fecha2); 
		$diferencia = $fecha2-$fecha1; 
		$diff = array();
		$diff['minutos'] = (int)($diferencia/(60));
		$diff['horas'] = (int)($diferencia/(60*60)); 
		$diff['dias'] = (int)($diferencia/(60*60*24));
		return $diff;
	}


}
