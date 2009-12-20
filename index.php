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
			->addArgument('viewRenderer')
			->setFactory(new Dispenser_Element_Factory('FactoryClass', 'getFactory'));
			
$dispenser->getTest();	
$renderer = $dispenser->getRenderer();


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
var_dump($dispenser2->getComponents());


$exporter = new Dispenser_Exporter_Native();
$exporter->setClassName("BuiltDI");
$exporter->load($dispenser2);
file_put_contents("BuiltDI.php", $exporter->export());
require_once "BuiltDI.php";


$wrapped = new BuiltDI();
$wrapped->setVariable('test.config', 'TEST.CONFIG');
$wrapped->getTest();


$xml = <<<XML
<variables>

	<variable key="test.key" value="wut" />
	
	<production>
		<variable key="test.key" value="production" />
		<variable key="extended.key" value="production" />
	</production>
	
	<testing extends="production">
		<variable key="test.key" value="testing" />
	</testing>
	
</variables>
XML;

$xmlVariablesImporter = new Dispenser_Importer_Variables_Xml();
$xmlVariablesImporter->loadFromString($xml);

var_dump($xmlVariablesImporter->getVariables());

$xmlVariablesImporter->setSection("production");
var_dump($xmlVariablesImporter->getVariables());

$xmlVariablesImporter->setSection("testing");
var_dump($xmlVariablesImporter->getVariables());


$xml = <<<XML
<components>

	<component id="Renderer" class="FactoryReturnedClass">
		<factory class="FactoryClass" method="getFactory" />
		<arguments>
			<argument>viewRenderer</argument>
		</arguments>
	</component>
	
	<component id="Test" class="Test" shared="true">
		<methods>
			<method name="setSomeShit">
				<arguments>
					<argument type="reference">Renderer</argument>
					<argument type="variable">test.config</argument>
				</arguments>
			</method>
			<method name="setSomeOtherShit">
				<arguments>
					<argument type="variable">test.config</argument>
				</arguments>
			</method>
		</methods>
		<arguments>
			<argument>blah</argument>
		</arguments>
	</component>
</components>
XML;

$xmlImporter = new Dispenser_Importer_Xml();
$xmlImporter->loadFromString($xml);
$components = $xmlImporter->getComponents();

$xmlBuilder = new Dispenser_Builder();
$xmlBuilder->load($xmlImporter);
$xmlBuilder->setVariable("test.config", "test");
$xmlBuilder->getTest();
