<?php
ini_set('display_errors', "On");
@set_time_limit(0) ;
ini_set("memory_limit","2048M");
error_reporting(E_ALL);


//echo 1; exit();

try {

	date_default_timezone_set("America/Mexico_City");

	/**
	 * Read the configuration
	 */
	$config = include __DIR__ . "/../app/config/config.php";

	/**
	 * Read auto-loader
	 */
	include __DIR__ . "/../app/config/loader.php";

	/**
	 * Read services
	 */
	include __DIR__ . "/../app/config/services.php";

	


	/**
	 * Handle the request
	 */
	$application = new \Phalcon\Mvc\Application($di);

	echo $application->handle()->getContent();

} catch (\Exception $e) {
	echo $e->getMessage();
}
