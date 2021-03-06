<?php

/**
 * SMS API para PHP
 * Versi�n 2.0
 * Abril 2008
 * Aurotek
 *
 * Instrucciones:
 * - Este archivo debe colocarse en una ruta dentro del servidor que est� incluida en el INCLUDE_PATH de PHP.
 * - Este archivo no debe ser modificado o editado sin autorizaci�n previa de Aurotek.
 */
if (!defined('LOCAL_WS'))
    require_once('SMS_CONFIG.php');

function _checkValidSession() {
    if (!defined('LOCAL_WS'))
        return checkValidSession();
    else
        return true;
}

class CalixtaAPI {

    private static $propiedades;

    private static function getHashCode($x) {
        if (gettype($x) == 'object' || gettype($x) == 'array')
            return sha1(serialize($x));
        else
            return sha1($x);
    }

    private static function genLock($seed) {
        $lockString = self::getHashCode($seed);
        $pos = strlen($lockString) % 2 ? (strlen($lockString) + 1) / 2 : strlen($lockString) / 2;
        $lockString = substr($lockString, $pos, strlen($lockString)) . substr($lockString, 0, $pos);
        return $lockString;
    }

    private static function getSocket() {
        $host = HOST;
        $port = PORT == 443 ? 80 : PORT;
        if (defined("PROXY_HOST"))
            $host = PROXY_HOST;
        if (defined("PROXY_PORT"))
            $port = PROXY_PORT;
//        debug('CalixtaAPI.getSocket: host=<' . $host . '>');
//        debug('CalixtaAPI.getSocket: port=<' . $port . '>');
        return @fsockopen($host, $port, $errno, $errstr, TIMEOUT);
    }

    private static function headerBasics($action, $boundary = null, $url = null) {
        $header = "POST ";
        if (defined("PROXY_HOST") && (!$url || substr($url, 0, 2) != "ht")) {
            $header .= "http://" . HOST . ":" . PORT;
        }

        if ($url)
            $header .= "$url HTTP/1.1\r\n";
        else
            $header .= "/Controller.php/__a/$action HTTP/1.1\r\n";

        if ($boundary)
            $header .= "Content-Type: multipart/form-data; boundary=$boundary\r\n";
        else
            $header .= "Content-Type: application/x-www-form-urlencoded\r\n";
        $header .= "User-Agent: RemoteSMS_PHP 1.1\r\n";
        $header .= "Host: " . HOST . "\r\n";
        if (defined("PROXY_HOST"))
            $header .= "Proxy-Connection: Keep-Alive\r\n";
        return $header;
    }

    private static function _enviaMensajeCSV($csv, $msg, $mtipo, $fechaInicio = null) {
        if (!_checkValidSession())
            return -1;
        $retVal = 0;

        if ($csv && $msg) {
            $clienteId = CLIENTE;
            $encpwd = PASSWORD;
            $email = USER;
            $id = rand(1, 99999999);
            $key = self::getHashCode($id);
            //$seed=$ip.$id.$key.$clienteId;
            $seed = $id . $key . $clienteId;
            $lock = self::genLock($seed);

            $boundary = "---------------------------7d81282c144055e";

            //$req = "msg=$msg&numtel=$num&cte=$clienteId&id=$id&lock=$lock&encpwd=$encpwd&email=$email&mtipo=$mtipo";

            $req = "--$boundary\r\n";
            $req.= "Content-Disposition: form-data; name=\"encpwd\"\r\n\r\n$encpwd\r\n";
            $req.= "--$boundary\r\n";
            $req.= "Content-Disposition: form-data; name=\"email\"\r\n\r\n$email\r\n";
            $req.= "--$boundary\r\n";
            $req.= "Content-Disposition: form-data; name=\"mensaje\"\r\n\r\n$msg\r\n";
            $req.= "--$boundary\r\n";
            $req.= "Content-Disposition: form-data; name=\"cte\"\r\n\r\n$clienteId\r\n";
            $req.= "--$boundary\r\n";
            $req.= "Content-Disposition: form-data; name=\"id\"\r\n\r\n$id\r\n";
            $req.= "--$boundary\r\n";
            $req.= "Content-Disposition: form-data; name=\"lock\"\r\n\r\n$lock\r\n";
            $req.= "--$boundary\r\n";
            $req.= "Content-Disposition: form-data; name=\"mtipo\"\r\n\r\n$mtipo\r\n";
            $req.= "--$boundary\r\n";

            //Los parametros recibidos se envian al request
            for ($i = 0, $n = count(self::$propiedades); $i < $n; $i++) {

                $parametro = self::$propiedades[$i];
                $nombre = $parametro[0];
                $valor = $parametro[1];
                //$req .= "&"."$nombre=$valor";
                $req.= "Content-Disposition: form-data; name=\"$nombre\"\r\n\r\n$valor\r\n";
                $req.= "--$boundary\r\n";
            }


            if ($fechaInicio) {
                $req.= "Content-Disposition: form-data; name=\"fechaInicio\"\r\n\r\n$fechaInicio\r\n";
                $req.= "--$boundary\r\n";
            }
            $req.= "Content-Disposition: form-data; name=\"tipoDestino\"\r\n\r\n2\r\n";
            $req.= "--$boundary\r\n";
            $req.= "Content-Disposition: form-data; name=\"archivo\"; filename=\"c:\PHP_CSV.csv\"\r\n";
            $req.= "Content-Type: text/plain\r\n\r\n";
            $req.= $csv . "\r\n";
            $req.= "--$boundary--\r\n";

            //Abre la conexi�n.
            $header = self::headerBasics("sms.extsend.remote.sa", $boundary);
            $header .= "Content-Length: " . strlen($req) . "\r\n\r\n"; //Falta contemplar el CSV

            $fp = self::getSocket();

            if (!$fp) {
                //No se pudo establecer la comunicacion.
                $retVal = -2;
            } else {
                $headerReq = $header . $req;
                $res = self::peticion($fp, $headerReq);
                //echo $res;
                $res = urldecode($res);
                if (strpos($res, 'OK') === FALSE) {
                    //Ocurrio un error al procesarlo.
                    debug("CalixtaAPI::_enviaMensaje:res=$res");
                    $retVal = -3;
                } else {
                    $retVal = substr($res, 3);
                }
                //Aqui el mensaje fue enviado.
            }
            @fclose($fp);
        } else {
            //Error de parametros.
            return -4;
        }
        return $retVal;
    }

    private static function _enviaEmail($cte, $email, $password, $nombreCamp, $to, $from, $fromName, $replyTo, $subject, $incrustarImagen, $textEmail, $htmlEmail, $seleccionaAdjuntos, $fileBase64, $fileNameBase64, $nombreArchivoPersonalizado, $envioSinArchivo, $fechaInicio, $horaInicio, $minutoInicio, $listasNegras) {
        if (!_checkValidSession())
            return -1;
        $retVal = 0;
        $retVal.= "Parametros recibidos:<br>";
        $retVal.= $cte . "<br>";
        $retVal.= $email . "<br>";
        $retVal.= $password . "<br>";
        $retVal.= $nombreCamp . "<br>";
        $retVal.= $to . "<br>";
        $retVal.= $from . "<br>";
        $retVal.= $fromName . "<br>";
        $retVal.= $replyTo . "<br>";
        $retVal.= $subject . "<br>";
        $retVal.= $incrustarImagen . "<br>";
        $retVal.= $textEmail . "<br>";
        $retVal.= $htmlEmail . "<br>";
        $retVal.= $seleccionaAdjuntos . "<br>";
        $retVal.= $fileBase64 . "<br>";
        $retVal.= $fileNameBase64 . "<br>";
        $retVal.= $nombreArchivoPersonalizado . "<br>";
        $retVal.= $envioSinArchivo . "<br>";
        $retVal.= $fechaInicio . "<br>";
        $retVal.= $horaInicio . "<br>";
        $retVal.= $minutoInicio . "<br>";
        $retVal.= $listasNegras . "<br>";
        //debug($retVal);


        if ($to && $from && $subject && $htmlEmail && $textEmail) {

            //$ip=$_SERVER["SERVER_ADDR"];
            $clienteId = CLIENTE;
            $encpwd = PASSWORD;
            //$email = USER;
            $id = rand(1, 99999999);
            $key = self::getHashCode($id);
            //$seed=$ip.$id.$key.$clienteId;
            $seed = $id . $key . $clienteId;
            $lock = self::genLock($seed);
            $boundary = "---------------------------7d81282c144055e";

            $req = "";
            $req.= "--$boundary\r\n";
            $req.= "Content-Disposition: form-data; name=\"cte\"\r\n\r\n$cte\r\n";
            $req.= "--$boundary\r\n";
            $req.= "Content-Disposition: form-data; name=\"email\"\r\n\r\n$email\r\n";
            $req.= "--$boundary\r\n";
            $req.= "Content-Disposition: form-data; name=\"nombreCamp\"\r\n\r\n$nombreCamp\r\n";
            $req.= "--$boundary\r\n";
            $req.= "Content-Disposition: form-data; name=\"to\"\r\n\r\n$to\r\n";
            $req.= "--$boundary\r\n";
            $req.= "Content-Disposition: form-data; name=\"from\"\r\n\r\n$from\r\n";
            $req.= "--$boundary\r\n";
            $req.= "Content-Disposition: form-data; name=\"fromName\"\r\n\r\n$fromName\r\n";
            $req.= "--$boundary\r\n";
            $req.= "Content-Disposition: form-data; name=\"replyTo\"\r\n\r\n$replyTo\r\n";
            $req.= "--$boundary\r\n";
            $req.= "Content-Disposition: form-data; name=\"subject\"\r\n\r\n$subject\r\n";
            $req.= "--$boundary\r\n";
            $req.= "Content-Disposition: form-data; name=\"incrustarImagen\"\r\n\r\n$incrustarImagen\r\n";
            $req.= "--$boundary\r\n";
            $req.= "Content-Disposition: form-data; name=\"textEmail\"\r\n\r\n$textEmail\r\n";
            $req.= "--$boundary\r\n";
            $req.= "Content-Disposition: form-data; name=\"htmlEmail\"\r\n\r\n$htmlEmail\r\n";
            $req.= "--$boundary\r\n";
            $req.= "Content-Disposition: form-data; name=\"seleccionaAdjuntos\"\r\n\r\n$seleccionaAdjuntos\r\n";
            $req.= "--$boundary\r\n";
            $req.= "Content-Disposition: form-data; name=\"fileNameBase64\"\r\n\r\n$fileNameBase64\r\n";
            $req.= "--$boundary\r\n";
            $req.= "Content-Disposition: form-data; name=\"nombreArchivoPersonalizado\"\r\n\r\n$nombreArchivoPersonalizado\r\n";
            $req.= "--$boundary\r\n";
            $req.= "Content-Disposition: form-data; name=\"envioSinArchivo\"\r\n\r\n$envioSinArchivo\r\n";
            $req.= "--$boundary\r\n";
            $req.= "Content-Disposition: form-data; name=\"fechaInicio\"\r\n\r\n$fechaInicio\r\n";
            $req.= "--$boundary\r\n";
            $req.= "Content-Disposition: form-data; name=\"horaInicio\"\r\n\r\n$horaInicio\r\n";
            $req.= "--$boundary\r\n";
            $req.= "Content-Disposition: form-data; name=\"minutoInicio\"\r\n\r\n$minutoInicio\r\n";
            $req.= "--$boundary\r\n";
            $req.= "Content-Disposition: form-data; name=\"listasNegras\"\r\n\r\n$listasNegras\r\n";
            $req.= "--$boundary\r\n";
            $req.= "Content-Disposition: form-data; name=\"encpwd\"\r\n\r\n$encpwd\r\n";
            $req.= "--$boundary\r\n";
            $req.= "Content-Disposition: form-data; name=\"cte\"\r\n\r\n$clienteId\r\n";
            $req.= "--$boundary\r\n";
            $req.= "Content-Disposition: form-data; name=\"id\"\r\n\r\n$id\r\n";
            $req.= "--$boundary\r\n";
            $req.= "Content-Disposition: form-data; name=\"lock\"\r\n\r\n$lock\r\n";
            $req.= "--$boundary\r\n";
            $req.= "Content-Disposition: form-data; name=\"fileBase64\"; filename=\"$fileNameBase64\"\r\n";
            $req.= "Content-Type: text/plain\r\n\r\n";
            $req.= $fileBase64 . "\r\n";
            $req.= "--$boundary\r\n";
            $req.= "Content-Disposition: form-data; name=\"password\"\r\n\r\n$password\r\n";
            $req.= "--$boundary--\r\n";


            //Abre la conexi�n.
            $header = self::headerBasics("gateway.remote.send.email", $boundary);
            $header .= "Content-Length: " . strlen($req) . "\r\n\r\n";

            $fp = self::getSocket();

            if (!$fp) {
                //No se pudo establecer la comunicacion.
                $retVal = -2;
            } else {
                $headerReq = $header . $req;
                $res = self::peticion($fp, $headerReq);
                //echo $res;
                //$res = urldecode($res);
                if (strpos($res, 'OK') === FALSE) {
                    //Ocurrio un error al procesarlo.
                    $retVal = -3;
                } else {
                    $retVal = substr($res, 3);
                    //$retVal = $res;
                }
                //Aqui el mensaje fue enviado.
            }
            @fclose($fp);
        } else {
            //Error de parametros.
            return -4;
        }

        return $retVal;
    }
    private static function _enviaEmailsArchivoCSV($pathTempEmail, $nombreCamp, $pathCSV, $from, $fromName, $replyTo, $subject, $incrustarImagen, $textEmail=NULL, $htmlEmail="", $seleccionaAdjuntos, $nombreArchivosPersonalizados, $envioSinArchivo, $fechaInicio, $horaInicio, $minutoInicio, $listasNegras,$htmlFile,$textFile){
        if (!_checkValidSession())
            return -1;
        $retVal = 0;
        
        if ($pathCSV && $from && $subject && $pathTempEmail) {

            //$ip=$_SERVER["SERVER_ADDR"];
            //$clienteId = CLIENTE;
            $cte=CLIENTE;
            $encpwd = PASSWORD;
            $email = USER;
            $id = rand(1, 99999999);
            $key = self::getHashCode($id);
            //$seed=$ip.$id.$key.$clienteId;
            $seed = $id . $key . $cte;
            $lock = self::genLock($seed);
            
            if(is_file($pathCSV)){
            $csv=file_get_contents($pathCSV);            
            }else{
                return "El path al archivo es invalido o el archivo esta da�ado: $pathCSV";
            }
            $csvName=basename($pathCSV);
            
            $boundary = "---------------------------7d81282c144055e";

            $req = "";
            $req.= "--$boundary\r\n";
            $req.= "Content-Disposition: form-data; name=\"cte\"\r\n\r\n$cte\r\n";
            $req.= "--$boundary\r\n";
            $req.= "Content-Disposition: form-data; name=\"email\"\r\n\r\n$email\r\n";
            $req.= "--$boundary\r\n";
            $req.= "Content-Disposition: form-data; name=\"nombreCamp\"\r\n\r\n$nombreCamp\r\n";            
            $req.= "--$boundary\r\n";
            $req.= "Content-Disposition: form-data; name=\"from\"\r\n\r\n$from\r\n";
            $req.= "--$boundary\r\n";
            $req.= "Content-Disposition: form-data; name=\"tempPathEmail\"\r\n\r\n$pathTempEmail\r\n";
            $req.= "--$boundary\r\n";
            $req.= "Content-Disposition: form-data; name=\"fromName\"\r\n\r\n$fromName\r\n";
            $req.= "--$boundary\r\n";
            $req.= "Content-Disposition: form-data; name=\"replyTo\"\r\n\r\n$replyTo\r\n";
            $req.= "--$boundary\r\n";
            $req.= "Content-Disposition: form-data; name=\"subject\"\r\n\r\n$subject\r\n";
            $req.= "--$boundary\r\n";
            $req.= "Content-Disposition: form-data; name=\"incrustarImagen\"\r\n\r\n$incrustarImagen\r\n";
            $req.= "--$boundary\r\n";
            $req.= "Content-Disposition: form-data; name=\"textEmail\"\r\n\r\n$textEmail\r\n";
            $req.= "--$boundary\r\n";
            $req.= "Content-Disposition: form-data; name=\"htmlEmail\"\r\n\r\n$htmlEmail\r\n";
            $req.= "--$boundary\r\n";
            $req.= "Content-Disposition: form-data; name=\"seleccionaAdjuntos\"\r\n\r\n$seleccionaAdjuntos\r\n";            
            $req.= "--$boundary\r\n";
            $req.= "Content-Disposition: form-data; name=\"nombreArchivoPersonalizado\"\r\n\r\n$nombreArchivosPersonalizados\r\n";
            $req.= "--$boundary\r\n";
            $req.= "Content-Disposition: form-data; name=\"envioSinArchivo\"\r\n\r\n$envioSinArchivo\r\n";
            $req.= "--$boundary\r\n";
            $req.= "Content-Disposition: form-data; name=\"fechaInicio\"\r\n\r\n$fechaInicio\r\n";
            $req.= "--$boundary\r\n";
            $req.= "Content-Disposition: form-data; name=\"horaInicio\"\r\n\r\n$horaInicio\r\n";
            $req.= "--$boundary\r\n";
            $req.= "Content-Disposition: form-data; name=\"minutoInicio\"\r\n\r\n$minutoInicio\r\n";
            $req.= "--$boundary\r\n";
            $req.= "Content-Disposition: form-data; name=\"listasNegras\"\r\n\r\n$listasNegras\r\n";
            $req.= "--$boundary\r\n";
            $req.= "Content-Disposition: form-data; name=\"encpwd\"\r\n\r\n$encpwd\r\n";            
            $req.= "--$boundary\r\n";
            $req.= "Content-Disposition: form-data; name=\"id\"\r\n\r\n$id\r\n";
            $req.= "--$boundary\r\n";
            $req.= "Content-Disposition: form-data; name=\"lock\"\r\n\r\n$lock\r\n";            
            $req.= "--$boundary\r\n";
            $req.= "--$boundary\r\n";
            $req.= "Content-Disposition: form-data; name=\"htmlFile\"\r\n\r\n$htmlFile\r\n";            
            $req.= "--$boundary\r\n"; 
            $req.= "--$boundary\r\n";
            $req.= "Content-Disposition: form-data; name=\"textFile\"\r\n\r\n$textFile\r\n";            
            $req.= "--$boundary\r\n"; 
            $req.= "Content-Disposition: form-data; name=\"csvEmails\"; filename=\"$csvName\"\r\n";
            $req.= "Content-Type: text/plain\r\n\r\n";
            $req.= $csv . "\r\n";
            $req.= "--$boundary--\r\n";


            //Abre la conexi�n.
            $header = self::headerBasics("gateway.remote.send.emails", $boundary);
            $header .= "Content-Length: " . strlen($req) . "\r\n\r\n";

            $fp = self::getSocket();

            if (!$fp) {
                //No se pudo establecer la comunicacion.
                $retVal = -2;
            } else {
                $headerReq = $header . $req;
                //debug($headerReq);
                $res = self::peticion($fp, $headerReq);
                //echo $res;
                //$res = urldecode($res);
                if (strpos($res, 'OK') === FALSE) {
                    //Ocurrio un error al procesarlo.
                    $retVal = -3;
                } else {
                    $retVal = substr($res, 3);
                    //$retVal = $res;
                }
                //Aqui el mensaje fue enviado.
            }
            @fclose($fp);
        } else {
            //Error de parametros.
            return -4;
        }

        return $retVal;
    }
    
    private static function _preparaEnvioEmail() {
        if (!_checkValidSession())
            return -1;

        $cte = CLIENTE;
        $encpwd = PASSWORD;
        $email = USER;
        $id = rand(1, 99999999);
        $key = self::getHashCode($id);
        $seed = $id . $key . $cte;
        $lock = self::genLock($seed);
        $boundary = "---------------------------7d81282c144055e";

        $req = "";
        $req.= "--$boundary\r\n";
        $req.= "Content-Disposition: form-data; name=\"cte\"\r\n\r\n$cte\r\n";
        $req.= "--$boundary\r\n";
        $req.= "Content-Disposition: form-data; name=\"email\"\r\n\r\n$email\r\n";
        $req.= "--$boundary\r\n";
        $req.= "Content-Disposition: form-data; name=\"encpwd\"\r\n\r\n$encpwd\r\n";
        $req.= "--$boundary\r\n";
        $req.= "Content-Disposition: form-data; name=\"id\"\r\n\r\n$id\r\n";
        $req.= "--$boundary\r\n";
        $req.= "Content-Disposition: form-data; name=\"lock\"\r\n\r\n$lock\r\n";
        $req.= "--$boundary--\r\n";


        //Abre la conexi�n.
        $header = self::headerBasics("gateway.remote.prepare.send.email", $boundary);
        $header .= "Content-Length: " . strlen($req) . "\r\n\r\n";

        $fp = self::getSocket();

        if (!$fp) {
            //No se pudo establecer la comunicacion.
            $retVal = -2;
        } else {
            $headerReq = $header . $req;
            //debug($headerReq);
            $res = self::peticion($fp, $headerReq);
            //echo $res;
            //$res = urldecode($res);
            if (strpos($res, 'OK') === FALSE) {
                //Ocurrio un error al procesarlo.
                $retVal = -3;
            } else {
                $retVal = substr($res, 3);
                //$retVal = $res;
            }
            //Aqui el mensaje fue enviado.
        }
        @fclose($fp);


        return $retVal;
    }

    private static function _agregarArchivoEnvioEmail($idTemp, $tipo, $filePath) {
        if (!_checkValidSession())
            return -1;

        $cte = CLIENTE;
        $encpwd = PASSWORD;
        $email = USER;
        $id = rand(1, 99999999);
        $key = self::getHashCode($id);
        $seed = $id . $key . $cte;
        $lock = self::genLock($seed);
        $boundary = "---------------------------7d81282c144055e";

        
        if(is_file($filePath)){
            $file=file_get_contents($filePath);            
        }else{
            return "El path al archivo es invalido o el archivo esta da�ado: $filePath";
        }
        $fileName=basename($filePath);
        
        $req = "";
        $req.= "--$boundary\r\n";
        $req.= "Content-Disposition: form-data; name=\"cte\"\r\n\r\n$cte\r\n";
        $req.= "--$boundary\r\n";
        $req.= "Content-Disposition: form-data; name=\"email\"\r\n\r\n$email\r\n";
        $req.= "--$boundary\r\n";
        $req.= "Content-Disposition: form-data; name=\"encpwd\"\r\n\r\n$encpwd\r\n";
        $req.= "--$boundary\r\n";
        $req.= "Content-Disposition: form-data; name=\"id\"\r\n\r\n$id\r\n";
        $req.= "--$boundary\r\n";
        $req.= "Content-Disposition: form-data; name=\"lock\"\r\n\r\n$lock\r\n";
        $req.= "--$boundary\r\n";
        $req.= "Content-Disposition: form-data; name=\"tipo\"\r\n\r\n$tipo\r\n";
        $req.= "--$boundary\r\n";
        $req.= "Content-Disposition: form-data; name=\"tempPathEmail\"\r\n\r\n$idTemp\r\n";
        $req.= "--$boundary\r\n";
        $req.= "Content-Disposition: form-data; name=\"fileUpload\"; filename=\"$fileName\"\r\n";
        $req.= "Content-Type: text/plain\r\n\r\n";
        $req.= $file . "\r\n";
        $req.= "--$boundary--\r\n";

        //Abre la conexi�n.
        $header = self::headerBasics("gateway.remote.add.file.email", $boundary);
        $header .= "Content-Length: " . strlen($req) . "\r\n\r\n";

        $fp = self::getSocket();

        if (!$fp) {
            //No se pudo establecer la comunicacion.
            $retVal = -2;
        } else {
            $headerReq = $header . $req;
            //debug($headerReq);
            $res = self::peticion($fp, $headerReq);
            //echo $res;
            //$res = urldecode($res);
            if (strpos($res, 'OK') === FALSE) {
                //Ocurrio un error al procesarlo.
                $retVal = -3;
            } else {
                $retVal = substr($res, 3);
                //$retVal = $res;
            }
            //Aqui el mensaje fue enviado.
        }
        @fclose($fp);


        return $retVal;
    }

    private static function _enviaMensajeArchivoCSV($path, $msg, $mtipo, $fechaInicio = null) {
        if (!_checkValidSession())
            return -1;
        $retVal = 0;

        if ($path && $msg) {
            $clienteId = CLIENTE;
            $encpwd = PASSWORD;
            $email = USER;
            $id = rand(1, 99999999);
            $key = self::getHashCode($id);
            //$seed=$ip.$id.$key.$clienteId;
            $seed = $id . $key . $clienteId;
            $lock = self::genLock($seed);

            $stringFile = "";
            $archivo = fopen($path, "r");
            while (($linea = fgets($archivo, 1024)) !== FALSE) {
                $stringFile .= $linea;
            }
            @fclose($archivo);
            //echo $stringFile;				

            $boundary = "---------------------------7d81282c144055e";

            //$req = "msg=$msg&numtel=$num&cte=$clienteId&id=$id&lock=$lock&encpwd=$encpwd&email=$email&mtipo=$mtipo";

            $req = "--$boundary\r\n";
            $req.= "Content-Disposition: form-data; name=\"encpwd\"\r\n\r\n$encpwd\r\n";
            $req.= "--$boundary\r\n";
            $req.= "Content-Disposition: form-data; name=\"email\"\r\n\r\n$email\r\n";
            $req.= "--$boundary\r\n";
            $req.= "Content-Disposition: form-data; name=\"mensaje\"\r\n\r\n$msg\r\n";
            $req.= "--$boundary\r\n";
            $req.= "Content-Disposition: form-data; name=\"cte\"\r\n\r\n$clienteId\r\n";
            $req.= "--$boundary\r\n";
            $req.= "Content-Disposition: form-data; name=\"id\"\r\n\r\n$id\r\n";
            $req.= "--$boundary\r\n";
            $req.= "Content-Disposition: form-data; name=\"lock\"\r\n\r\n$lock\r\n";
            $req.= "--$boundary\r\n";
            $req.= "Content-Disposition: form-data; name=\"mtipo\"\r\n\r\n$mtipo\r\n";
            $req.= "--$boundary\r\n";

            //Los parametros recibidos se envian al request
            for ($i = 0, $n = count(self::$propiedades); $i < $n; $i++) {

                $parametro = self::$propiedades[$i];
                $nombre = $parametro[0];
                $valor = $parametro[1];
                //$req .= "&"."$nombre=$valor";
                $req.= "Content-Disposition: form-data; name=\"$nombre\"\r\n\r\n$valor\r\n";
                $req.= "--$boundary\r\n";
            }


            if ($fechaInicio) {
                $req.= "Content-Disposition: form-data; name=\"fechaInicio\"\r\n\r\n$fechaInicio\r\n";
                $req.= "--$boundary\r\n";
            }
            $req.= "Content-Disposition: form-data; name=\"tipoDestino\"\r\n\r\n2\r\n";
            $req.= "--$boundary\r\n";
            $req.= "Content-Disposition: form-data; name=\"archivo\"; filename=\"$path\"\r\n";
            $req.= "Content-Type: text/plain\r\n\r\n";
            $req.= $stringFile . "\r\n";
            $req.= "--$boundary--\r\n";

            //Abre la conexi�n.
            $header = self::headerBasics("sms.extsend.remote.sa", $boundary);
            $header .= "Content-Length: " . strlen($req) . "\r\n\r\n"; //Falta contemplar el CSV

            $fp = self::getSocket();

            if (!$fp) {
                //No se pudo establecer la comunicacion.
                $retVal = -2;
            } else {
                $headerReq = $header . $req;
                $res = self::peticion($fp, $headerReq);
                //echo $res;
                $res = urldecode($res);
                if (strpos($res, 'OK') === FALSE) {
                    //Ocurrio un error al procesarlo.
                    $retVal = -3;
                } else {
                    $retVal = substr($res, 3);
                }
                //Aqui el mensaje fue enviado.
            }
            @fclose($fp);
        } else {
            //Error de parametros.
            return -4;
        }
        return $retVal;
    }

    // Recibe un arreglo de parametros
    /**
     * Asigna propiedades que se enviaran al momento de realizar env�os.
     *
     * @param array $arrayProps
     */
    public static function setPropiedades($arrayProps) {
        self::$propiedades = $arrayProps;
    }

    /**
     * Devuelve el estado de un grupo de env�os.
     *
     * @param string $idEnvios Cadena con los identificadores de env�os separados por comas.
     * @return unknown Devuelve un arreglo con los saldos, o un entero negativo en caso de error.
     */
    public static function estadoEnvios($idEnvios) {
        if (!_checkValidSession())
            return -1;
        $retVal = 0;

        if ($idEnvios) {
            $clienteId = CLIENTE;
            $encpwd = PASSWORD;
            $email = USER;
            $id = rand(1, 99999999);
            $key = self::getHashCode($id);
            $seed = $id . $key . $clienteId;
            $lock = self::genLock($seed);

            $clienteId = urlencode($clienteId);
            $id = urlencode($id);
            $lock = urlencode($lock);

            $req = "idenvios=$idEnvios&cte=$clienteId&id=$id&lock=$lock&encpwd=$encpwd&email=$email&version=3";
            //Abre la conexi�n.
            $header = self::headerBasics("sms.remote.campanas.sa");
            $header .= "Content-Length: " . strlen($req) . "\r\n\r\n";

            $fp = self::getSocket();

            if (!$fp) {
                //No se pudo establecer la comunicacion.
                $retVal = -2;
            } else {

                $headerReq = $header . $req;
                $res = self::peticion($fp, $headerReq);

                $res = urldecode($res);
                if (strpos($res, 'ERROR') === 0) {
                    //Ocurrio un error al procesarlo.
                    $retVal = -3;
                } else {
                    $retVal = substr($res, 5);
                }
                //Aqui el mensaje fue enviado.
            }
            @fclose($fp);
        } else {
            //Error de parametros.
            return -4;
        }
        return $retVal;
    }

    public static function detalleEnvio($idEnvio) {
        if (!_checkValidSession())
            return -1;
        $retVal = 0;


        if ($idEnvio) {
            $clienteId = CLIENTE;
            $encpwd = PASSWORD;
            $email = USER;
            $id = rand(1, 99999999);
            $key = self::getHashCode($id);
            $seed = $id . $key . $clienteId;
            $lock = self::genLock($seed);

            $clienteId = urlencode($clienteId);
            $id = urlencode($id);
            $lock = urlencode($lock);

            $req = "filtro_idCampana=$idEnvio&cte=$clienteId&id=$id&lock=$lock&encpwd=$encpwd&email=$email";
            //Abre la conexi�n.
            $header = self::headerBasics("sms.remote.logs.sa");
            $header .= "Content-Length: " . strlen($req) . "\r\n\r\n";

            $fp = self::getSocket();

            if (!$fp) {
                //No se pudo establecer la comunicacion.
                $retVal = -2;
            } else {
                $headerReq = $header . $req;
                $res = self::peticion($fp, $headerReq);

                $res = urldecode($res);
                $retVal = $res;
                //Aqui el mensaje fue enviado.
            }
            @fclose($fp);
        } else {
            //Error de parametros.
            return -4;
        }
        return $retVal;
    }

    public static function getEstadosMensajes($idEnvios) {
        if (!_checkValidSession())
            return -1;
        $retVal = 0;


        if ($idEnvios) {
            $clienteId = CLIENTE;
            $encpwd = PASSWORD;
            $email = USER;
            $id = rand(1, 99999999);
            $key = self::getHashCode($id);
            $seed = $id . $key . $clienteId;
            $lock = self::genLock($seed);

            $clienteId = urlencode($clienteId);
            $id = urlencode($id);
            $lock = urlencode($lock);

            $req = "filtro_idCampanas=$idEnvios&cte=$clienteId&id=$id&lock=$lock&encpwd=$encpwd&email=$email&version=2";
            //Abre la conexi�n.
            $header = self::headerBasics("sms.remote.logs.sa");
            $header .= "Content-Length: " . strlen($req) . "\r\n\r\n";

            $fp = self::getSocket();

            if (!$fp) {
                //No se pudo establecer la comunicacion.
                $retVal = -2;
            } else {
                $headerReq = $header . $req;
                $res = self::peticion($fp, $headerReq);

                $res = urldecode($res);
                $retVal = $res;
                //Aqui el mensaje fue enviado.
            }
            @fclose($fp);
        } else {
            //Error de parametros.
            return -4;
        }
        return $retVal;
    }

    public static function getReporteArchivo($idEnvio, $filePath) {
        if (!_checkValidSession())
            return -1;
        $retVal = 0;


        if ($idEnvio) {
            $clienteId = CLIENTE;
            $encpwd = PASSWORD;
            $email = USER;
            $id = rand(1, 99999999);
            $key = self::getHashCode($id);
            $seed = $id . $key . $clienteId;
            $lock = self::genLock($seed);

            $clienteId = urlencode($clienteId);
            $id = urlencode($id);
            $lock = urlencode($lock);


            $req = "idEnvio=$idEnvio&cte=$clienteId&id=$id&lock=$lock&encpwd=$encpwd&email=$email&forceGen=1&parcial=1";
            //Abre la conexi�n.
            $header = self::headerBasics("gateway.remote.result.url");
            $header .= "Content-Length: " . strlen($req) . "\r\n\r\n";

            $fp = self::getSocket();

            if (!$fp) {
                //No se pudo establecer la comunicacion.
                $retVal = -2;
            } else {
                $headerReq = $header . $req;
                $res = self::peticion($fp, $headerReq);
                $res = urldecode($res);

                $subc = split('[|]', $res);
                if ($subc[0] === FALSE) {
                    if ($subc[1] === 100) {
                        return $retVal;
                    }
                }

                if (strpos($res, 'OK') === FALSE) {
                    //Ocurrio un error al procesarlo.
                    return -3;
                } else {
                    $subcadena = "/Controller.php";
                    $inicio = strpos($res, $subcadena);
                    $url = substr($res, ($inicio));
                    //echo $url;					
                }
                $rep = self::reporteArchivo($url, $filePath);
                if (strpos($res, '/parcial/1/') > 0) {
                    return 2;
                } else {
                    return 1;
                }
            }
            @fclose($fp);
        } else {
            //Error de parametros.
            return -4;
        }
        return $retVal;
    }

    public static function reporteArchivo($url, $filePath) {
        if (!_checkValidSession())
            return -1;
        $retVal = 0;


        if ($url) {
            $clienteId = CLIENTE;
            $encpwd = PASSWORD;
            $email = USER;
            $clienteId = urlencode($clienteId);


            $req = "cte=$clienteId&encpwd=$encpwd&email=$email";
            //Abre la conexi�n.
            $header = self::headerBasics(null, null, $url);
            $header .= "Content-Length: " . strlen($req) . "\r\n\r\n";

            $fp = self::getSocket();

            if (!$fp) {
                //No se pudo establecer la comunicacion.
                $retVal = -2;
            } else {
                $headerReq = $header . $req;
                $res = self::peticion($fp, $headerReq);

                $archivo = fopen("$filePath", "w");
                if ($archivo) {
                    fputs($archivo, $res);
                }
                @fclose($archivo);

                $res = urldecode($res);
                $retVal = $res;
                //Aqui el mensaje fue enviado.
            }
            @fclose($fp);
        } else {
            //Error de parametros.
            return -4;
        }
        return $retVal;
    }

    public static function getSaldos() {
        if (!_checkValidSession())
            return -1;
        $retVal = 0;

        $clienteId = CLIENTE;
        $encpwd = PASSWORD;
        $email = USER;
        $id = rand(1, 99999999);
        $key = self::getHashCode($id);
        $seed = $id . $key . $clienteId;
        $lock = self::genLock($seed);

        $clienteId = urlencode($clienteId);
        $id = urlencode($id);
        $lock = urlencode($lock);

        $req = "cte=$clienteId&id=$id&lock=$lock&encpwd=$encpwd&email=$email";
        //Abre la conexi�n.
        $header = self::headerBasics("sms.remote.getsaldo.sa");
        $header .= "Content-Length: " . strlen($req) . "\r\n\r\n";

        $fp = self::getSocket();

        //$time = new DateTime("now");
        date_default_timezone_set('America/Mexico_City');
        $time = new DateTime();
        $dTime = self::timeToDouble($time);
        if (!$fp) {
            //No se pudo establecer la comunicacion.
            $retVal = -2;
        } else {
            $headerReq = $header . $req;
            $res = self::peticion($fp, $headerReq);

            $ind = 0;
            $arrSaldos[] = new Saldo();
            $res = urldecode($res);
            if (strpos($res, 'OK') === FALSE) {
                //Ocurrio un error al procesarlo.
                $retVal = -3;
            } else {
                $retVal = substr($res, 5);
                str_ireplace("\r", "", $retVal);
                $strSaldos = explode("\n", $retVal);
                $saldos = array();
                foreach ($strSaldos as $strSaldo) {
                    $saldoParts = explode(",", $strSaldo);
                    //echo "$saldoParts[1]\n";
                    if (count($saldoParts) >= 3) { //rengl�n v�lido
                        if ($saldoParts[0] == "0") { //Es dinero
                            $dinero = (double) $saldoParts[1] + (double) $saldoParts[2];
                            if ($dinero > 0) {
                                $saldos[0] = $dinero;
                                $arrSaldos[$ind] = new Saldo();
                                $arrSaldos[$ind]->getId(0);
                                $arrSaldos[$ind]->getDisponible($dinero);
                                $ind++;
                            }
                        } else {
                            $vencimiento = $saldoParts[2];
                            if ($vencimiento > $dTime) {
                                $monto = (double) $saldoParts[1];
                                //echo "$monto\n";
                                if ($monto > 0) {
                                    //echo "$monto\n";
                                    $saldos[(int) $saldoParts[0]] = $monto;
                                    //echo "$saldoParts[0]";
                                    $arrSaldos[$ind] = new Saldo();
                                    $arrSaldos[$ind]->getId($saldoParts[0]);
                                    $arrSaldos[$ind]->getDisponible($monto);
                                    $ind++;
                                    //echo $saldoParts[2];
                                    //echo "\n";			                    
                                }
                            }
                        }
                    }
                }
                $retVal = $saldos;
            }
            //Aqui el mensaje fue enviado.
        }
        @fclose($fp);
        return $arrSaldos;
    }

    public static function timeToDouble($time) {
        $zoneMexico = new DateTimeZone("America/Mexico_City");
        $timeOffset = $zoneMexico->getOffset($time);
        $timesec = time();
        $timesec = $timesec + $timeOffset;
        $dias = $timesec / (60 * 60 * 24) + 25569;
        return $dias;
    }

    private static function _enviaMensaje($dest, $msg, $mtipo, $fechaInicio = null) {
        if (!_checkValidSession())
            return -1;
        $retVal = 0;

        if ($dest && $msg) {
            $num = $dest;
            //$ip=$_SERVER["SERVER_ADDR"];
            $clienteId = CLIENTE;
            $encpwd = PASSWORD;
            $email = USER;
            $id = rand(1, 99999999);
            $key = self::getHashCode($id);
            //$seed=$ip.$id.$key.$clienteId;
            $seed = $id . $key . $clienteId;
            $lock = self::genLock($seed);

            $msg = urlencode($msg);
            $num = urlencode($num);
            $clienteId = urlencode($clienteId);
            $id = urlencode($id);
            $lock = urlencode($lock);

            $req = "msg=$msg&numtel=$num&cte=$clienteId&id=$id&lock=$lock&encpwd=$encpwd&email=$email&mtipo=$mtipo";

            //Los parametros recibidos se envian al request
            for ($i = 0, $n = count(self::$propiedades); $i < $n; $i++) {

                $parametro = self::$propiedades[$i];
                $nombre = $parametro[0];
                $valor = $parametro[1];
                $req .= "&" . "$nombre=$valor";
            }


            if ($fechaInicio) {
                $req.="&fechaInicio=$fechaInicio";
            }
            //Abre la conexi�n.
            $header = self::headerBasics("sms.send.remote.portal");
            $header .= "Content-Length: " . strlen($req) . "\r\n\r\n";

            $fp = self::getSocket();

            if (!$fp) {
                //No se pudo establecer la comunicacion.
                $retVal = -2;
            } else {

                $headerReq = $header . $req;
                $res = self::peticion($fp, $headerReq);

                $res = urldecode($res);
                if (strpos($res, 'OK') === FALSE) {
                    //Ocurrio un error al procesarlo.
                    $retVal = -3;
                } else {
                    $retVal = substr($res, 3);
                }

                //Aqui el mensaje fue enviado.
            }
            @fclose($fp);
        } else {
            //Error de parametros.
            return -4;
        }
        return $retVal;
    }

    private static function peticion($fp, $headerReq) {
        fputs($fp, $headerReq);

        $res = '';
        $strTE = "Transfer-Encoding: chunked" . "\r\n";
        $chunked = FALSE;
        $headerdone = false;
        $flag = false;
        $chunk1 = true;
        $size = 0;
        $cont = 0;
        while (!feof($fp)) {
            $line = fgets($fp, 1024);
            if (strcmp($line, $strTE) == 0) {
                $chunked = TRUE;
            }
            if (strcmp($line, "\r\n") == 0) {
                $headerdone = true;
            } else if (($headerdone) && ($chunked == FALSE)) {
                $res .= $line;
            }
            if (($headerdone) && ($chunked == TRUE)) {
                if ((strcmp($line, "\r\n") != 0 ) && $chunk1) {
                    //echo " primer chunk ";
                    $size = hexdec($line);
                    //echo $size;
                    $flag = true;
                    $chunk1 = false;
                } else if ($cont < $size && $flag) {
                    $res .= $line;
                    $cont = $cont + strlen($line);
                } else if ((strcmp($line, "\r\n") != 0) && $flag) {
                    $size = hexdec($line);
                    $cont = 0;
                }
            }
        }
        //@fclose ($fp);
        return $res;
    }

    /*
     * Metodo para registrar un app
     *
     */
    private function _agregarApp($nombre,$plataforma,$descripcion){
        if (!_checkValidSession())
            return -1;

        $cte = CLIENTE;
        $encpwd = PASSWORD;
        $email = USER;
        $id = rand(1, 99999999);
        $key = self::getHashCode($id);
        $seed = $id . $key . $cte;
        $lock = self::genLock($seed);
        
        $nombre=trim($nombre);
        $descripcion=trim($descripcion);
        
        $boundary = "---------------------------7d81282c144055e";

        $req = "";
        $req.= "--$boundary\r\n";
        $req.= "Content-Disposition: form-data; name=\"cte\"\r\n\r\n$cte\r\n";
        $req.= "--$boundary\r\n";
        $req.= "Content-Disposition: form-data; name=\"email\"\r\n\r\n$email\r\n";
        $req.= "--$boundary\r\n";
        $req.= "Content-Disposition: form-data; name=\"encpwd\"\r\n\r\n$encpwd\r\n";
        $req.= "--$boundary\r\n";
        $req.= "Content-Disposition: form-data; name=\"id\"\r\n\r\n$id\r\n";
        $req.= "--$boundary\r\n";
        $req.= "Content-Disposition: form-data; name=\"nombreApp\"\r\n\r\n$nombre\r\n";
        $req.= "--$boundary\r\n";
        $req.= "Content-Disposition: form-data; name=\"plataforma\"\r\n\r\n$plataforma\r\n";
        $req.= "--$boundary\r\n";
        $req.= "Content-Disposition: form-data; name=\"descripcionApp\"\r\n\r\n$descripcion\r\n";
        $req.= "--$boundary\r\n";
        $req.= "Content-Disposition: form-data; name=\"apiKey\"\r\n\r\n$apiKey\r\n";
        $req.= "--$boundary\r\n";
        $req.= "Content-Disposition: form-data; name=\"passwdCertificadoP12\"\r\n\r\n$passwdCertificadoP12\r\n";
        $req.= "--$boundary\r\n";
        $req.= "Content-Disposition: form-data; name=\"lock\"\r\n\r\n$lock\r\n";
        $req.= "--$boundary--\r\n";


        //Abre la conexi�n.
        $header = self::headerBasics("gateway.remote.prepare.send.email", $boundary);
        $header .= "Content-Length: " . strlen($req) . "\r\n\r\n";

        $fp = self::getSocket();

        if (!$fp) {
            //No se pudo establecer la comunicacion.
            $retVal = -2;
        } else {
            $headerReq = $header . $req;
            //debug($headerReq);
            $res = self::peticion($fp, $headerReq);
            //echo $res;
            //$res = urldecode($res);
            if (strpos($res, 'OK') === FALSE) {
                //Ocurrio un error al procesarlo.
                $retVal = -3;
            } else {
                $retVal = substr($res, 3);
                //$retVal = $res;
            }
            //Aqui el mensaje fue enviado.
        }
        @fclose($fp);


        return $retVal;
    }
    
    public function getNombresApp(){
        
    }
    /**
     * Metodo para enviar mensajes de texto a traves del gateway utilizando una cadena CSV.
     *
     * @param unknown_type $csv
     * @param string $csv Registros separada por comas.
     * @param string $msg Mensaje a enviar.
     * @param string $fechaInicio Fecha y hora en la que iniciar� el env�o. Formato: "dia/mes/a�o(4 digitos)/hora(24hrs)/minuto"
     * @return integer Devuelve el ticket en caso satisfactorio, y un n�mnero menor que cero en caso de error.
     */
    public static function enviaMensajeCSV($csv, $msg, $fechaInicio = null) {
        return self::_enviaMensajeCSV($csv, $msg, 'SMS', $fechaInicio);
    }

    /**
     * Metodo para enviar mensajes de texto a traves del gateway utilizando un archivo CSV.
     *
     * @param string $path Registros separada por comas.
     * @param string $msg Mensaje a enviar.
     * @param string $fechaInicio Fecha y hora en la que iniciar� el env�o. Formato: "dia/mes/a�o(4 digitos)/hora(24hrs)/minuto"
     * @return integer Devuelve el ticket en caso satisfactorio, y un n�mnero menor que cero en caso de error.
     */
    public static function enviaMensajeArchivoCSV($path, $msg, $fechaInicio = null) {
        return self::_enviaMensajeArchivoCSV($path, $msg, 'SMS', $fechaInicio);
    }

    /**
     * Metodo para enviar mensajes de texto a traves del gateway.
     *
     * @param string $dest Numeros de celular de los destinatarios, a 10 digitos y separados por comas.
     * @param string $msg Mensaje a enviar.
     * @param string $fechaInicio Fecha y hora en la que iniciar� el env�o. Formato: "dia/mes/a�o(4 digitos)/hora(24hrs)/minuto"
     * @return integer Devuelve 0 (cero) en caso satisfactorio, y devuelve cualquier otra cosa en caso de error.
     */
    public static function enviaMensaje($dest, $msg, $fechaInicio = null) {
        return self::_enviaMensaje($dest, $msg, 'SMS', $fechaInicio);
    }

    /**
     * Metodo para enviar mensajes de voz a traves del gateway.
     *
     * @param string $dest Numeros telefonicos de los destinatarios, a 10 digitos y separados por comas.
     * @param string $msg Mensaje a enviar.
     * @param string $fechaInicio Fecha y hora en la que iniciar� el env�o. Formato: "dia/mes/a�o(4 digitos)/hora(24hrs)/minuto"
     * @return integer Devuelve 0 (cero) en caso satisfactorio, y devuelve cualquier otra cosa en caso de error.
     */
    public static function enviaMensajeVoz($dest, $msg, $fechaInicio = null) {
        $msg = '<texto voz="Carlos">' . $msg . '</texto>';
        return self::_enviaMensaje($dest, $msg, 'VOZ', $fechaInicio);
    }

    /**
     * Metodo para enviar mensajes de voz a traves del gateway utilizando una cadena CSV.
     *
     * @param unknown_type $csv
     * @param string $csv Registros separada por comas.
     * @param string $msg Mensaje a enviar.
     * @param string $fechaInicio Fecha y hora en la que iniciar� el env�o. Formato: "dia/mes/a�o(4 digitos)/hora(24hrs)/minuto"
     * @return integer Devuelve el ticket en caso satisfactorio, y un n�mnero menor que cero en caso de error.
     */
    public static function enviaMensajeCSVvoz($csv, $msg, $fechaInicio = null, $xml = false) {
        if (!$xml)
            $msg = '<texto voz="Carlos">' . $msg . '</texto>';
        return self::_enviaMensajeCSV($csv, $msg, 'VOZ', $fechaInicio);
    }

    /**
     * Metodo para enviar mensajes de voz a traves del gateway utilizando un archivo CSV.
     *
     * @param string $path Registros separada por comas.
     * @param string $msg Mensaje a enviar.
     * @param string $fechaInicio Fecha y hora en la que iniciar� el env�o. Formato: "dia/mes/a�o(4 digitos)/hora(24hrs)/minuto"
     * @return integer Devuelve el ticket en caso satisfactorio, y un n�mnero menor que cero en caso de error.
     */
    public static function enviaMensajeArchivoCSVvoz($path, $msg, $fechaInicio = null, $xml = false) {
        if (!$xml)
            $msg = '<texto voz="Carlos">' . $msg . '</texto>';
        return self::_enviaMensajeArchivoCSV($path, $msg, 'VOZ', $fechaInicio);
    }

    /**
     * Metodo para enviar mensajes de voz a traves del gateway. Construyendo el XML directamente.
     *
     * @param string $dest Numeros telefonicos de los destinatarios, a 10 digitos y separados por comas.
     * @param string $msg XML del mensaje a enviar.
     * @param string $fechaInicio Fecha y hora en la que iniciar� el env�o. Formato: "dia/mes/a�o(4 digitos)/hora(24hrs)/minuto"
     * @return integer Devuelve 0 (cero) en caso satisfactorio, y devuelve cualquier otra cosa en caso de error.
     */
    public static function enviaMensajeXmlVoz($dest, $msg, $fechaInicio = null) {
        return self::_enviaMensaje($dest, $msg, 'VOZ', $fechaInicio);
    }

    public static function enviaEmail($cte, $email, $password, $nombreCamp, $to, $from, $fromName, $replyTo, $subject, $incrustarImagen, $textEmail, $htmlEmail, $seleccionaAdjuntos, $fileBase64, $fileNameBase64, $nombreArchivoPersonalizado, $envioSinArchivo, $fechaInicio, $horaInicio, $minutoInicio, $listasNegras) {
        return self::_enviaEmail($cte, $email, $password, $nombreCamp, $to, $from, $fromName, $replyTo, $subject, $incrustarImagen, $textEmail, $htmlEmail, $seleccionaAdjuntos, $fileBase64, $fileNameBase64, $nombreArchivoPersonalizado, $envioSinArchivo, $fechaInicio, $horaInicio, $minutoInicio, $listasNegras);
    }

    public static function preparaEnvioEmail() {
        return self::_preparaEnvioEmail();
    }

    public static function agregarArchivoEnvioEmail($idTemp, $tipo, $filePath) {
        return self::_agregarArchivoEnvioEmail($idTemp, $tipo, $filePath);
    }
    
    public static function enviaEmailsArchivoCSV($pathTempEmail, $nombreCamp, $pathCSV, $from, $fromName, $replyTo, $subject, $incrustarImagen, $textEmail=NULL, $htmlEmail, $seleccionaAdjuntos, $nombreArchivosPersonalizados, $envioSinArchivo, $fechaInicio, $horaInicio, $minutoInicio, $listasNegras,$htmlFile=NULL,$textFile=NULL){
        return self::_enviaEmailsArchivoCSV($pathTempEmail, $nombreCamp, $pathCSV, $from, $fromName, $replyTo, $subject, $incrustarImagen, $textEmail, $htmlEmail, $seleccionaAdjuntos, $nombreArchivosPersonalizados, $envioSinArchivo, $fechaInicio, $horaInicio, $minutoInicio, $listasNegras,$htmlFile,$textFile);
    }
    
    public static function agregarApp($nombre,$plataforma,$descripcion){
        return self::_agregarApp($nombre,$plataforma,$descripcion);
    }

    private static function _enviaTransMPH($nombreTrans, $params) {
        if (!_checkValidSession())
            return -1;
        $retVal = 0;

        //$ip=$_SERVER["SERVER_ADDR"];
        $clienteId = CLIENTE;
        $encpwd = PASSWORD;
        $email = USER;
        $id = rand(1, 99999999);
        $key = self::getHashCode($id);
        //$seed=$ip.$id.$key.$clienteId;
        $seed = $id . $key . $clienteId;
        $lock = self::genLock($seed);

        $clienteId = urlencode($clienteId);
        $id = urlencode($id);
        $lock = urlencode($lock);

        $req = "cte=$clienteId&id=$id&lock=$lock&encpwd=$encpwd&email=$email&nombreTrans=$nombreTrans";

        //Los parametros recibidos se envian al request
        if ($params && is_array($params))
            foreach ($params as $nombre => $valor) {
                $req .= '&' . "$nombre=$valor";
            }

        //Los parametros recibidos se envian al request
        for ($i = 0, $n = count(self::$propiedades); $i < $n; $i++) {
            $parametro = self::$propiedades[$i];
            $nombre = $parametro[0];
            $valor = $parametro[1];
            $req .= '&' . "$nombre=$valor";
        }


        //Abre la conexi�n.
        $header = self::headerBasics("api.mph.trans");
        $header .= "Content-Length: " . strlen($req) . "\r\n\r\n";

        $fp = self::getSocket();

        if (!$fp) {
            //No se pudo establecer la comunicacion.
            $retVal = -2;
        } else {

            $headerReq = $header . $req;
            $res = self::peticion($fp, $headerReq);

            $res = urldecode($res);
            if (strpos($res, 'OK') === FALSE) {
                //Ocurrio un error al procesarlo.
                $retVal = -3;
            } else {
                $retVal = substr($res, 3);
            }
        }
        @fclose($fp);
        return $retVal;
    }

    /**
     * 
     * @param String $nombreMarcador Nombre del marcador predictivo.
     * @param int $idCampana Identificador de la campa�a.
     * @param array $telefonos Contiene un arreglo, las llaves son n�meros consecutivos y cada elemento es un arreglo de dos posiciones, la primera tiene el tipo de tel�fono (0-casa,1-oficina,2-movil y 3-otro) y la segunda el tel�fono
     * @param array $campos Contiene un arreglo de los campos del registros, la llave es el nombre del campo y el valor del elemento es el valor del campo.
     */
    public static function MPH_agregaRegistro($nombreMarcador, $idCampana, $campos, $telefonos) {
        //http://.../Controller.php/__a/campana.cargaRegIndividualApi?usuario=bestel016@auronix.com&encpsw=abc1234**&marcador=bestel016&idCampana=1&csvCamposRegistros=nombre,saldo|Armando%20Dominguez,123456&csvTelefonos=0,5553711107
        $nombreCampos = '';
        $valoresCampos = '';
        if ($campos && is_array($campos))
            foreach ($campos as $campo => $valor) {
                $nombreCampos.=($nombreCampos == '' ? '' : ',') . $campo;
                $valoresCampos.=($valoresCampos == '' ? '' : ',') . $valor;
            }
        $csvTelefonos = '';
        if ($telefonos && is_array($telefonos))
            foreach ($telefonos as $datosTelefono) {
                if (!$datosTelefono || !is_array($datosTelefono) || count($datosTelefono) != 2)
                    continue;
                $csvTelefonos.=($csvTelefonos == '' ? '' : '|') . $datosTelefono[0] . ',' . $datosTelefono[1];
            }
        $respuesta = self::_enviaTransMPH('agregaRegistro', array('marcador' => $nombreMarcador, 'idCampana' => $idCampana, 'csvCamposRegistros' => ($nombreCampos . '|' . $valoresCampos), 'csvTelefonos' => $csvTelefonos));


        $valsRep = explode('|', $respuesta);

        $returnArr = array();
        if (count($valsRep) >= 3) {
            $returnArr['idRegistro'] = $valsRep[0];
            $returnArr['telsInsertados'] = $valsRep[1];
            $returnArr['telsTotal'] = $valsRep[2];
            $returnArr['error'] = null;
        } else {
            $returnArr['idRegistro'] = 0;
            $returnArr['telsInsertados'] = 0;
            $returnArr['telsTotal'] = 0;
            $returnArr['error'] = $respuesta;
        }
        return $returnArr;
    }

}

class Saldo {

    private $id;
    private $disponible;

    public function getId($id) {
        $this->id = $id;
    }

    public function getDisponible($disponible) {
        $this->disponible = $disponible;
    }

    public function setSaldos() {
        return "$this->id  --> $this->disponible";
    }

}

?>