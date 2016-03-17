<?php
use Phalcon\Events\Event,
	Phalcon\Mvc\User\Plugin,
	Phalcon\Acl;

class Pagofacil extends Plugin
{



	public function __construct(){
		$this->idSucursal = "d498bee722a32fc4a6e6a1bb83b82c6aa4587281";
		$this->idUsuario = "2e8a7bdf89b132f06742cb767cfb77490949f53a";
		$this->idPedido = uniqid();
		$this->dataSend = array(
			"idServicio" => urlencode("3"),
			"idSucursal" => urlencode($this->idSucursal),
			"idUsuario" => urlencode($this->idUsuario),
			"nombre" => "",
			"apellidos" => "",
			"numeroTarjeta" => "",
			"cvt" => "",
			"mesExpiracion" => "",
			"anyoExpiracion" => "",
			"monto" => "",
			"email" => "",
			"cp" => urlencode("44150"),
			"telefono" => urlencode("3333351298"),
			"celular" => urlencode("3333351298"),
			"calleyNumero" => urlencode("Calle"),
			"colonia" => urlencode("Arcos"),
			"municipio" => urlencode("Guadalajara"),
			"estado" => urlencode("Jalisco"),
			"pais" => urlencode("Mexico"),
			"idPedido" => urlencode($this->idPedido),
			"ip" => urlencode($_SERVER["REMOTE_ADDR"]),
			"httpUserAgent" => urlencode($_SERVER["HTTP_USER_AGENT"])
		);	
	}


	public function getDataSend(){
		return $this->dataSend;
	}


	public function shop(){
		$cadena='';
		foreach ($this->dataSend as $key=>$valor){
			$cadena .= "&data[".$key."]=".str_replace(" " , "_" , $valor);
		}
		$url = 'https://www.pagofacil.net/ws/public/Wsrtransaccion/index/format/json/?method=transaccion'.$cadena;
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		$response = curl_exec($ch);
		curl_close($ch);
		$response = json_decode($response,true);
		$response = $response['WebServices_Transacciones']['transaccion'];
		$Msj = "" ;
		if( $response["autorizado"] == 0 ){
			if(isset($response["texto"])){
				if( is_array($response["texto"]) ){
					foreach ($response["texto"] as $key => $value) {
						$Msj .= $value . "\n" ;
					}
				} else {
					$Msj .= $response["texto"] . "\n" ;
				}
			}
			if(isset($response["error"])){
				if( is_array($response["error"]) ){
					foreach ($response["error"] as $key => $value) {
						$Msj .= $value . "\n" ;
					}
				} else {
					$Msj .= $response["error"] . "\n" ;
				}
			}
		}
		$response["mensaje"] = $Msj ;
		return $response;
	}



	








}