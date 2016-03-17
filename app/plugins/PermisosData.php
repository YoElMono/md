<?php
use Phalcon\Events\Event,
	Phalcon\Mvc\User\Plugin,
	Phalcon\Acl;

class PermisosData extends Plugin
{


	var $Data;

	public function __construct(){
		$this->Data = array(
			"Alls" => array(
				"mod_home" => 0,
				"sub_home_home" => 0,
				"mod_configuraciones" => 0,
				"sub_configuraciones_usuarios" => 0,
				"sub_configuraciones_secundarios" => 0,
				"sub_configuraciones_secundarios_new" => 0,
				"sub_configuraciones_secundarios_edit" => 0,
				"sub_configuraciones_secundarios_delete" => 0,
				"sub_configuraciones_usuarios_new" => 0,
				"sub_configuraciones_usuarios_edit" => 0,
				"sub_configuraciones_usuarios_delete" => 0,
				"mod_clases" => 0,
				"sub_clases_admin_clases_categorias" => 0,
				"sub_clases_admin_clases_categorias_new" => 0,
				"sub_clases_admin_clases_categorias_edit" => 0,
				"sub_clases_admin_clases_categorias_delete" => 0,
				"sub_clases_admin_clases_subcategorias" => 0,
				"sub_clases_admin_clases_subcategorias_new" => 0,
				"sub_clases_admin_clases_subcategorias_edit" => 0,
				"sub_clases_admin_clases_subcategorias_delete" => 0,
				"sub_clases_admin_clases_clases" => 0,
				"sub_clases_admin_clases_clases_new" => 0,
				"sub_clases_admin_clases_clases_edit" => 0,
				"sub_clases_admin_clases_clases_delete" => 0,
				"sub_clases_clases" => 0,
				"sub_clases_clases_new" => 0,
				"sub_clases_clases_edit" => 0,
				"sub_clases_clases_delete" => 0,
				"mod_foro" => 0,
				"sub_foro_admin_foro_categorias" => 0,
				"sub_foro_admin_foro_categorias_new" => 0,
				"sub_foro_admin_foro_categorias_edit" => 0,
				"sub_foro_admin_foro_categorias_delete" => 0,
				"sub_foro_admin_foro_subcategorias" => 0,
				"sub_foro_admin_foro_subcategorias_new" => 0,
				"sub_foro_admin_foro_subcategorias_edit" => 0,
				"sub_foro_admin_foro_subcategorias_delete" => 0,
				"sub_foro_admin_foro_clases" => 0,
				"sub_foro_admin_foro_clases_new" => 0,
				"sub_foro_admin_foro_clases_edit" => 0,
				"sub_foro_admin_foro_clases_delete" => 0,
				"sub_foro_foro" => 0,
				"sub_foro_foro_new" => 0,
				"sub_foro_foro_edit" => 0,
				"sub_foro_foro_delete" => 0,
				"mod_soporte" => 0,
				"sub_soporte_soporte" => 0,
				"sub_soporte_soporte_new" => 0,
				"sub_soporte_soporte_edit" => 0,
				"sub_soporte_soporte_delete" => 0,
				"mod_wiki" => 0,
				"sub_wiki_admin_wiki_categorias" => 0,
				"sub_wiki_admin_wiki_categorias_new" => 0,
				"sub_wiki_admin_wiki_categorias_edit" => 0,
				"sub_wiki_admin_wiki_categorias_delete" => 0,
				"sub_wiki_admin_wiki_clases" => 0,
				"sub_wiki_admin_wiki_clases_new" => 0,
				"sub_wiki_admin_wiki_clases_edit" => 0,
				"sub_wiki_admin_wiki_clases_delete" => 0,
				"sub_wiki_wiki" => 0,
				"sub_wiki_wiki_new" => 0,
				"sub_wiki_wiki_edit" => 0,
				"sub_wiki_wiki_delete" => 0,
				"mod_recursos" => 0,
				"sub_recursos_recursos" => 0,
				"sub_recursos_recursos_new" => 0,
				"sub_recursos_recursos_edit" => 0,
				"sub_recursos_recursos_delete" => 0,
				"sub_recursos_descargas" => 0,
				"sub_recursos_descargas_new" => 0,
				"sub_recursos_descargas_edit" => 0,
				"sub_recursos_descargas_delete" => 0,
				"mod_herramientas" => 0,
				"sub_herramientas_machinefans" => 0,
				"sub_herramientas_machinefans_new" => 0,
				"sub_herramientas_machinefans_edit" => 0,
				"sub_herramientas_machinefans_delete" => 0,
				"sub_herramientas_machinefans_grupos" => 0,
				"sub_herramientas_machinefans_grupos_new" => 0,
				"sub_herramientas_machinefans_grupos_edit" => 0,
				"sub_herramientas_machinefans_grupos_delete" => 0,
				"sub_herramientas_machinefans_paginas" => 0,
				"sub_herramientas_machinefans_paginas_new" => 0,
				"sub_herramientas_machinefans_paginas_edit" => 0,
				"sub_herramientas_machinefans_paginas_delete" => 0,
				"sub_herramientas_crawler" => 0,
				"sub_herramientas_crawler_new" => 0,
				"sub_herramientas_crawler_edit" => 0,
				"sub_herramientas_crawler_delete" => 0,
				"sub_herramientas_facebook" => 0,
				"sub_herramientas_facebook_new" => 0,
				"sub_herramientas_facebook_edit" => 0,
				"sub_herramientas_facebook_delete" => 0,
				"sub_herramientas_twitter" => 0,
				"sub_herramientas_twitter_new" => 0,
				"sub_herramientas_twitter_edit" => 0,
				"sub_herramientas_twitter_delete" => 0,
				"sub_herramientas_building" => 0,
				"sub_herramientas_building_new" => 0,
				"sub_herramientas_building_edit" => 0,
				"sub_herramientas_building_delete" => 0,
				"sub_herramientas_trafico" => 0,
				"sub_herramientas_trafico_new" => 0,
				"sub_herramientas_trafico_edit" => 0,
				"sub_herramientas_trafico_delete" => 0,
				"sub_herramientas_keyword" => 0,
				"sub_herramientas_keyword_new" => 0,
				"sub_herramientas_keyword_edit" => 0,
				"sub_herramientas_keyword_delete" => 0,
				"sub_herramientas_youtube" => 0,
				"sub_herramientas_youtube_new" => 0,
				"sub_herramientas_youtube_edit" => 0,
				"sub_herramientas_youtube_delete" => 0,
				"sub_herramientas_backlinks" => 0,
				"sub_herramientas_backlinks_new" => 0,
				"sub_herramientas_backlinks_edit" => 0,
				"sub_herramientas_backlinks_delete" => 0,
			

			),




			"Administrador" => array(

				"mod_home" => 1,
				"sub_home_home" => 1,
				"mod_configuraciones" => 1,
				"sub_configuraciones_usuarios" => 1,
				"sub_configuraciones_secundarios" => 1,
				"sub_configuraciones_secundarios_new" => 1,
				"sub_configuraciones_secundarios_edit" => 1,
				"sub_configuraciones_secundarios_delete" => 1,
				"sub_configuraciones_usuarios_new" => 1,
				"sub_configuraciones_usuarios_edit" => 1,
				"sub_configuraciones_usuarios_delete" => 1,
				"mod_clases" => 1,
				"sub_clases_admin_clases_categorias" => 1,
				"sub_clases_admin_clases_categorias_new" => 1,
				"sub_clases_admin_clases_categorias_edit" => 1,
				"sub_clases_admin_clases_categorias_delete" => 1,
				"sub_clases_admin_clases_subcategorias" => 1,
				"sub_clases_admin_clases_subcategorias_new" => 1,
				"sub_clases_admin_clases_subcategorias_edit" => 1,
				"sub_clases_admin_clases_subcategorias_delete" => 1,
				"sub_clases_admin_clases_clases" => 1,
				"sub_clases_admin_clases_clases_new" => 1,
				"sub_clases_admin_clases_clases_edit" => 1,
				"sub_clases_admin_clases_clases_delete" => 1,
				"sub_clases_clases" => 1,
				"sub_clases_clases_new" => 1,
				"sub_clases_clases_edit" => 1,
				"sub_clases_clases_delete" => 1,
				"mod_foro" => 1,
				"sub_foro_admin_foro_categorias" => 1,
				"sub_foro_admin_foro_categorias_new" => 1,
				"sub_foro_admin_foro_categorias_edit" => 1,
				"sub_foro_admin_foro_categorias_delete" => 1,
				"sub_foro_admin_foro_subcategorias" => 1,
				"sub_foro_admin_foro_subcategorias_new" => 1,
				"sub_foro_admin_foro_subcategorias_edit" => 1,
				"sub_foro_admin_foro_subcategorias_delete" => 1,
				"sub_foro_admin_foro_clases" => 1,
				"sub_foro_admin_foro_clases_new" => 1,
				"sub_foro_admin_foro_clases_edit" => 1,
				"sub_foro_admin_foro_clases_delete" => 1,
				"sub_foro_foro" => 1,
				"sub_foro_foro_new" => 1,
				"sub_foro_foro_edit" => 1,
				"sub_foro_foro_delete" => 1,
				"mod_soporte" => 1,
				"sub_soporte_soporte" => 1,
				"sub_soporte_soporte_new" => 1,
				"sub_soporte_soporte_edit" => 1,
				"sub_soporte_soporte_delete" => 1,
				"mod_wiki" => 1,
				"sub_wiki_admin_wiki_categorias" => 1,
				"sub_wiki_admin_wiki_categorias_new" => 1,
				"sub_wiki_admin_wiki_categorias_edit" => 1,
				"sub_wiki_admin_wiki_categorias_delete" => 1,
				"sub_wiki_admin_wiki_clases" => 1,
				"sub_wiki_admin_wiki_clases_new" => 1,
				"sub_wiki_admin_wiki_clases_edit" => 1,
				"sub_wiki_admin_wiki_clases_delete" => 1,
				"sub_wiki_wiki" => 1,
				"sub_wiki_wiki_new" => 1,
				"sub_wiki_wiki_edit" => 1,
				"sub_wiki_wiki_delete" => 1,



			),

			"Alumno" => array(

				"mod_home" => 1,
				"sub_home_home" => 1,
				"mod_clases" => 1,
				"sub_clases_clases" => 1,
				"sub_clases_clases_new" => 1,
				"sub_clases_clases_edit" => 1,
				"sub_clases_clases_delete" => 1,
				"mod_foro" => 1,
				"sub_foro_foro" => 1,
				"sub_foro_foro_new" => 1,
				"sub_foro_foro_edit" => 1,
				"sub_foro_foro_delete" => 1,
				"mod_soporte" => 1,
				"sub_soporte_soporte" => 1,
				"sub_soporte_soporte_new" => 1,
				"sub_soporte_soporte_edit" => 1,
				"sub_soporte_soporte_delete" => 1,
				"mod_wiki" => 1,
				"sub_wiki_wiki" => 1,
				"sub_wiki_wiki_new" => 1,
				"sub_wiki_wiki_edit" => 1,
				"sub_wiki_wiki_delete" => 1,



			),





		);		
	}






	public function Get($type="Alls"){
		$Permisos = $this->Data["Alls"];
		if(isset($this->Data[$type])){
			foreach($this->Data[$type] as $key => $value){
				$Permisos[$key] = $value;		
			}
		}
		return $Permisos;
	}


	public function checkForm($Name="Method" ,$Form="formulario_registro" ,  $Data=array()){
		$html = "\r\n<script type=\"text/javascript\">function " . $Name . "(){\r\n";
		//echo "<pre>"; print_r($Data); echo "</pre>"; exit();
		foreach($Data as $key => $value){
			if( $value == 1 ){
				$html .= "	if($('#".$Form." #permiso_".$key."').length){\r\n";
				$html .= "		$('#".$Form." #permiso_".$key."').attr('checked' , 'checked');\r\n";
				$html .= "	}\r\n";
			} else {
				$html .= "	if($('#".$Form." #permiso_".$key."').length){\r\n";
				$html .= "		$('#".$Form." #permiso_".$key."').removeAttr('checked');\r\n";
				$html .= "	}\r\n";				
			}
		}
		$html .= "}\r\n</script>";
		return $html;
	}



}