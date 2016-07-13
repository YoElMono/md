<?php
use Phalcon\Events\Event,
	Phalcon\Mvc\User\Plugin,
	Phalcon\Acl;

class Menus extends Plugin
{
	var $Menu = array(
		array(
			"name" => "Inicio",
			"module" => "home",
			"controller" => "home",
			"action" => "index",
			"show" => 1,
			"slider" => 0,
			"slider_name" => "",
			"sub_menu" => 0,
			"icon" => "i-home",
			"data" => array()
		),
		
		array(
			"name" => "Documentos",
			"module" => "documentos",
			"controller" => "documentos",
			"action" => "index",
			"show" => 1,
			"slider" => 0,
			"slider_name" => "",
			"sub_menu" => 0,
			"icon" => "i-file-copy",
			"data" => array()
		),
		
		array(
			"name" => "Clientes",
			"module" => "clientes",
			"controller" => "clientes",
			"action" => "index",
			"show" => 1,
			"slider" => 0,
			"slider_name" => "",
			"sub_menu" => 0,
			"icon" => "i-users3",
			"data" => array()
		),

		array(
			"name" => "Empresas",
			"module" => "negocios",
			"controller" => "negocios",
			"action" => "index",
			"show" => 1,
			"slider" => 0,
			"slider_name" => "",
			"sub_menu" => 0,
			"icon" => "i-tag",
			"data" => array()
		),


		array(
			"name" => "Tramites",
			"module" => "tramites",
			"controller" => "tramites",
			"action" => "index",
			"show" => 1,
			"slider" => 0,
			"slider_name" => "",
			"sub_menu" => 0,
			"icon" => "i-layer",
			"data" => array()
		),

		array(
			"name" => "Pagos | Gastos",
			"module" => "pagos",
			"controller" => "pagos",
			"action" => "index",
			"show" => 1,
			"slider" => 0,
			"slider_name" => "",
			"sub_menu" => 0,
			"icon" => "i-chart",
			"data" => array()
		),

		array(
			"name" => "Configuraciones",
			"module" => "configuraciones",
			"controller" => "configuraciones",
			"icon" => "i-cog2",
			"action" => "",
			"show" => 1,
			"slider" => 0,
			"slider_name" => "",
			"sub_menu" => 1,
			"data" => array(
				array("name" => "Usuarios" , "controller" => "usuarios" , "action" => "index" , "show" => 1 , "slider" => 0 , "slider_name" => "","icon" => "i-user",),
				array("name" => "Departamentos" , "controller" => "departamentos" , "action" => "index" , "show" => 1 , "slider" => 0 , "slider_name" => ""),
				array("name" => "Puestos" , "controller" => "puestos" , "action" => "index" , "show" => 1 , "slider" => 0 , "slider_name" => ""),
			)
		),
	);
}