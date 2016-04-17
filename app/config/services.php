<?php

use Phalcon\Db\Adapter\Pdo\Mysql as DbAdapter;
use Phalcon\DI\FactoryDefault;
use Phalcon\Mvc\Model\Metadata\Memory as MetaDataAdapter;
use Phalcon\Mvc\Url as UrlResolver;
use Phalcon\Mvc\View;
use Phalcon\Mvc\View\Engine\Volt as VoltEngine;
use Phalcon\Session\Adapter\Files as SessionAdapter;
use \Phalcon\Mvc\Dispatcher as PhDispatcher;


/**
 * The FactoryDefault Dependency Injector automatically register the right services providing a full stack framework
 */

$di = new FactoryDefault();
$dbh = NULL;


$di->set('datosdb',function() use ($config) {
    $DataSql = array(
		'host' => $config->database->host,
		'username' => $config->database->username,
		'password' => $config->database->password,
		'dbname' => $config->database->dbname,
		'charset'   =>'utf8'
    ); 
    return $DataSql;
},true
);

$di->set('config',function() use ($config) {
    $DataSql = array(
		'librerias' => $config->application->libraryDir
    ); 
    return $DataSql;
},true
);

$di->set(
    'dispatcher',
    function() use ($di) {

        $evManager = $di->getShared('eventsManager');

        $evManager->attach(
            "dispatch:beforeException",
            function($event, $dispatcher, $exception)
            {
                switch ($exception->getCode()) {
                    case PhDispatcher::EXCEPTION_HANDLER_NOT_FOUND:
                    case PhDispatcher::EXCEPTION_ACTION_NOT_FOUND:
                        $dispatcher->forward(
                            array(
                                'controller' => 'error',
                                'action'     => 'show404',
                            )
                        );
                        return false;
                }
            }
        );
        $dispatcher = new PhDispatcher();
        $dispatcher->setEventsManager($evManager);
        return $dispatcher;
    },
    true
);



/**
 * The URL component is used to generate all kind of urls in the application
 */
$di->set('url', function () use ($config) {
	$url = new UrlResolver();
	$url->setBaseUri($config->application->baseUri);

	return $url;
}, true);

/**
 * Setting up the view component
 */

$di->set('view', function () use ($config) {
	$view = new View();
	$view->setViewsDir($config->application->viewsDir);
	$view->registerEngines(array(
		'.volt' => function ($view, $di) use ($config) {

			$volt = new VoltEngine($view, $di);

			$volt->setOptions(array(
				'compiledPath' => $config->application->cacheDir,
				'compiledSeparator' => '_',
			));

			return $volt;
		},
		'.phtml' => 'Phalcon\Mvc\View\Engine\Php'
	));
	return $view;
}, true);

/**
 * Database connection is created based in the parameters defined in the configuration file
 */
$di->set('db', function () use ($config) {
	$dbh = new DbAdapter(array(
		'host' => $config->database->host,
		'username' => $config->database->username,
		'password' => $config->database->password,
		'dbname' => $config->database->dbname,
		'charset'   =>'utf8'
	));
	if( !isset($_SESSION["BaseRef"]) ){
		$_SESSION["BaseRef"] = $config->application->baseUri;	
	}
	
	return $dbh;
});

/**
 * If the configuration specify the use of metadata adapter use it or use memory otherwise
 */
$di->set('modelsMetadata', function () {
	return new MetaDataAdapter();
});

/**
 * Start the session the first time some component request the session service
 */
$di->set('session', function () {
	$session = new SessionAdapter();
	$session->start();
	
	return $session;
});



$di->set('router', function(){
    require __DIR__.'/routers.php';
    return $router;
});


