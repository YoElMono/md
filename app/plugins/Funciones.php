<?php
use Phalcon\Events\Event,
	Phalcon\Mvc\User\Plugin,
	Phalcon\Acl;

class Funciones extends Plugin
{



	public function sacar($TheStr, $sLeft, $sRight){
		$pleft = strpos($TheStr, $sLeft, 0);
		if ($pleft !== false){
			$pright = strpos($TheStr, $sRight, $pleft + strlen($sLeft));
			if ($pright !== false) {
				return trim(  (substr($TheStr, $pleft + strlen($sLeft), ($pright - ($pleft + strlen($sLeft)))))  );
			}
		}
		return '';
	}


	public function FileGetContents($Url="" , $Cantidad=15){
		$Contador = 0;
		$ctx = stream_context_create(array(
		    'http' => array(
				'method' => 'GET',
		        'timeout' => $Cantidad
		        )
		    )
		);
		$O = "";
		while($Contador < 10 ){
			$O = @file_get_contents($Url, false, $ctx);
			if( strlen($O) > 6 ){
				break;
			}
			$Contador++;
		}
		return $O;
	}






}