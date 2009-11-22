<?php
/* Very rough and ugly examples. */ 

include 'Dispenser/AutoLoader.php';

Dispenser_AutoLoader::register();

class Test {
	public function __construct($test) {
		echo $test;
	}
	
	public function setSomeShit($var) {
		var_dump($var);
	}
	
	public function setSomeOtherShit($var) {
		echo $var;
	}
}

class PassByRefClass {
	public function __construct() {
		
	}
}

class FactoryReturnedClass {
	
}

class FactoryClass {
	
	public static function getFactory($name) {
		return new FactoryReturnedClass();
	}
	
}


$dispenser = new Dispenser_Builder();

$dispenser->setVariable('test.config', 'TEST.CONFIG');


$dispenser	->register('RefClass2')
			->setClass('PassByRefClass')
			->setShared(true);
			
$dispenser	->register('RefClass')
			->setClass('PassByRefClass')
			->setShared(true);
			
$dispenser	->register('Test')
			->setClass('Test')
			->addArgument(new Dispenser_Element_Variable('test.config'))
			->addMethod(
				new Dispenser_Element_Method(
					'setSomeShit', 
					array(
						new Dispenser_Element_Reference('RefClass')
					)
				)
			)
			->addMethod(
				new Dispenser_Element_Method(
					'setSomeOtherShit', 
					new Dispenser_Element_Variable('test.config')	
				)
			)
			->setShared(false)			
;
$dispenser	->register('Renderer')
			->setClass('FactoryReturnedClass')
			->setFactory(new Dispenser_Element_Factory('FactoryClass', 'getFactory'))
			->addArgument('viewRenderer')
			->setFactory(new Dispenser_Element_Factory('FactoryClass', 'getFactory'));

$defs = array(
	'Renderer' => array(
		'class' => 'FactoryReturnedClass',
		'factory' => array(
			'class' => 'FactoryClass',
			'method' => 'getFactory',
		),
		'arguments' => array(
			'viewRenderer'
		)
	),
	'RefClass2' => array(
		'class' => 'PassByRefClass',
		'shared' => true
	),
	'RefClass' => array(
		'class' => 'PassByRefClass',
		'shared' => true
	),
	'Test' => array(
		'class' => 'Test',
		'shared' => true,
		'arguments' => array(
			'blah'
		),
		'methods' => array(
			'setSomeShit' => array(
				array(
					'type' => 'reference',
					'name' => 'RefClass'
				)
			),
			'setSomeOtherShit' => array(
				array(
					'type' => 'variable',
					'name' => 'test.config'
				)
			)
		)
	),
);

$dispenser2 	= new Dispenser_Builder();
$arrayLoader 	= new Dispenser_Importer_Array($defs);
$dispenser2->load($arrayLoader);

$dispenser->getTest();	

$renderer = $dispenser->getRenderer();

$exporter = new Dispenser_Exporter_Wrapper();
$exporter->setClassName("BuiltDI");
$exporter->load($dispenser2);
file_put_contents("BuiltDI.php", $exporter->export());
require_once "BuiltDI.php";


$wrapped = new BuiltDI();
$wrapped->setVariable('test.config', 'TEST.CONFIG');
$wrapped->getTest();