<?php

include 'Dispenser/AutoLoader.php';

Dispenser_AutoLoader::register();

$defs = array(
	'Config' => array(
		'class' => 'Zend_Config_Ini',
		'arguments' => array(':configFile', true),
		'params' => array(
			':configFile' =>  '/config/config.ini'
		),
		'scope' => 'singleton'
	),

	'Request' => array(
		'class' => 'Zend_Controller_Request_Http',
	),
	'Response' => array(
		'class' => 'Zend_Controller_Response_Http',
	),
	
	'Router' => array(
		'class' => 'Zend_Controller_Router_Rewrite',
		'methods' => array(
			array(
				'method' => 'addRoute',
				'arguments' => array(':routeName', 'ChainRoute'),
				'params' => array(':routeName' => 'username')
			),
		)
	),
    'RouteHostname' => array(
        'class' => 'Zend_Controller_Router_Route_Hostname',
        'arguments' => array(':hostnameRoutePattern'),
		'params' => array(':hostnameRoutePattern' => ':_profileUsername.:domain.:tdl')
    ),
    'RouterRoute' => array(
    	'class' => 'Zend_Controller_Router_Route',
    	'arguments' => array(
    		':routePattern',
    		':routeDefaultRoute'
    	),
    	'params' => array(
    		':routePattern' => ':controller/:action',
    		':routeDefaultRoute' => array('module' => 'default', 'controller'=> 'profile', 'action' => 'index')
    	)
    ),
    'ChainRoute' => array(
    	'class' => 'Zend_Controller_Router_Route_Chain',
    	'methods' => array(
    		array('method' => 'chain', 'arguments' => array('RouteHostname')),
    		array('method' => 'chain', 'arguments' => array('RouterRoute'))
    	)
    )
);


$yadifImporter = new Dispenser_Importer_Yadif_Array($defs);
$dispenser 	= new Dispenser_Builder();
$dispenser->load($yadifImporter);

var_dump($yadifImporter->getComponents());

$exporter = new Dispenser_Exporter_Native();
$exporter->setClassName("BuiltYadif");
$exporter->load($dispenser);

file_put_contents("BuiltYadif.php", $exporter->export());

include "BuiltYadif.php";

