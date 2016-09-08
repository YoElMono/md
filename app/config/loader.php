<?php

$loader = new \Phalcon\Loader();

/**
 * We're a registering a set of directories taken from the configuration file
 */
$loader->registerDirs(
	array(
		$config->application->controllersDir,
		$config->application->modelsDir,
		$config->application->libraryDir,
		$config->application->libraryDir.'PHPExcel/',
		$config->application->pluginsDir
	)
)
->registerClasses(
	array(
			"DOMPDF" => '../vendor/dompdf/dompdf/dompdf_config.inc.php',
			"WORD" => '../vendor/phpoffice/phpword/src/PhpWord/Autoloader.php',
		)
)->register();
