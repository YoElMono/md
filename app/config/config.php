<?php


if( strstr($_SERVER['SERVER_NAME'] , 'localhost') ){
	$BaseRef = "http://localhost:8080/md/";
	$DataSql = array(
        "server" => "testingview.com",
        "username" => "md",
        "password" => "2ene4epu8",
        "dbname" => "zadmin_mdsystem",
    ); 
} else if( strstr($_SERVER['SERVER_NAME'] , '.com') ){
	$BaseRef = "http://videokreaciones.com/";
	$DataSql = array(
        "server" => "videokreaciones.com",
        "username" => "md",
        "password" => "2ene4epu8",
        "dbname" => "zadmin_mdsystem",
    ); 
} 



return new \Phalcon\Config(array(
	'database' => array(
		'adapter' => 'Mysql',
		'host' => $DataSql["server"],
		'username' => $DataSql["username"],
		'password' => $DataSql["password"],
		'dbname' => $DataSql["dbname"]
	),
	'application' => array(
		'controllersDir' => __DIR__ . '/../../app/controllers/',
		'modelsDir' => __DIR__ . '/../../app/models/',
		'viewsDir' => __DIR__ . '/../../app/views/',
		'layoutsDir' => __DIR__ . '/../../app/views/layouts/',
		'pluginsDir' => __DIR__ . '/../../app/plugins/',
		'libraryDir' => __DIR__ . '/../../app/library/',
		'cacheDir' => __DIR__ . '/../../app/cache/',
		'baseUri' => $BaseRef
	),
	//http://www.sitepoint.com/sending-confirmation-emails-phalcon-swift/
	//http://uno-de-piera.com/envio-de-emails-con-php-la-libreria-phpmailer/
	'mail' => array(
		'fromName' => 'pingshop',
		'fromEmail' => 'correo@gmail.com',
		'smtp' => array(
					'server' => 'smtp.gmail.com',
					'port' => 465,
					'security' => 'ssl',
					'username' => 'correo@gmail.com',
					'password' => 'password'
		)
	),
));
