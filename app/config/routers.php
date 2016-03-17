<?php

$router = new \Phalcon\Mvc\Router();
$router->removeExtraSlashes(true);


	$router->notFound(array(
	    "controller" => "index",
	    "action" => "index"
	));


	$router->add("/api/:params", array(       
	    'controller' 	=> 'api_login',
	    'action' 		=> 'index',
	    'params'		=> 1
	));




	$router->add("/api/tickets/:params", array(       
	    'controller' 	=> 'api_tickets',
	    'action' 		=> 'index',
	    'params'		=> 1
	));



return $router;