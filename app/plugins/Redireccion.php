<?php
use Phalcon\Events\Event,
	Phalcon\Mvc\User\Plugin,
	Phalcon\Mvc\Dispatcher,
	Phalcon\Acl;

class Redireccion extends Plugin
{


	public function forward($uri) {
		$uriParts = explode('/', $uri);
		return $this->dispatcher->forward(
			array(
				'controller' => $uriParts[0],
				'action' => $uriParts[1],
			)
		);
	}



}