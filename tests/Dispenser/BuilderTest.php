<?php

require_once 'PHPUnit/Framework.php';
require_once 'Dispenser/AutoLoader.php';
require_once '_files/MockClasses.php';


class Dispenser_BuilderTest extends PHPUnit_Framework_TestCase
{
	protected $builder;

	public function __construct() {
		Dispenser_Autoloader::register();
	}
	
	
	protected function setUp() {
		$this->builder = new Dispenser_Builder();
	}
	
	
	public function testSetVariable()
    {
		$this->builder->setVariable("test.variable", 0);
		
		return $this->builder;
	}	
	
       
    /** 
     * @depends testSetVariable
     */
    public function testSetVariables() {
    	$mockVariables = array(
    		"variable.int" => 0,
    		"variable.string" => "test",
    		"variable.array" => array("test")
    	);
    	
    	$this->builder->setVariables($mockVariables);
    	
    	return $this->builder;
    }
    
    
    /** 
     * @depends testSetVariable
     */
    public function testGetVariableSetBySetVariable($builder) {
    	$variable = $builder->getVariable("test.variable");
    	
    	$this->assertEquals(0, $variable);
    }
    
    
    /** 
     * @depends testSetVariables
     */
    public function testGetVariablesSetBySetVariables($builder) {
    	$variables = $builder->getVariables();
    	
    	$this->assertEquals(3, count($variables));
    	$this->assertType("int", $builder->getVariable("variable.int"));
    	$this->assertType("string", $builder->getVariable("variable.string"));
    	$this->assertType("array", $builder->getVariable("variable.array"));
    }
    
    
    public function testSettingAlreadyExistingVariableCorrectlyOverwrites() {
    	$this->builder->setVariable("test.variable", "1");
    	$originalValue = $this->builder->getVariable("test.variable");
    	
    	$this->builder->setVariable("test.variable", "2");
    	$newValue = $this->builder->getVariable("test.variable");
    	
    	$this->assertNotSame($originalValue, $newValue);
    }
    
    
	/**
     * @expectedException Dispenser_Exception
     */
    public function testSettingNonStringNameThrowsExceptionInSetVariable() {
    	$this->builder->setVariable(1, "");
    }
    
    
	/**
     * @expectedException Dispenser_Exception
     */
    public function testSettingNonArrayThrowsExceptionInSetVariables() {
    	$this->builder->setVariables("");
    }
    
    
    /**
     * @expectedException Dispenser_Exception
     */
    public function testGettingNonExistantVariableThrowsException() {
    	$this->builder->getVariable("_non.existant.variable_");
    }
    
    
    public function testRegisterMinimal() {
    	$this->builder	->register("Test")
    					->setClass("MockClass");
    	
    	return $this->builder;
    }
    
 	public function testRegisterShared() {
    	$this->builder	->register("Test")
    					->setClass("MockClass")
    					->setShared(true);
    	return $this->builder;
    }
    
 	public function testRegisterNotShared() {
    	$this->builder	->register("test")
    					->setClass("MockClass");
    					
    	
    	return $this->builder;
    }
    
    
	public function testRegisterFactory() {
    	$this->builder	->register("Factory")
    					->setClass("FactoryReturnedClass")
						->setFactory(new Dispenser_Element_Factory('FactoryClass', 'getFactory'))
						->addArgument("1");
    	
    	return $this->builder;
    }
    	
    
	/**
     * @depends testRegisterFactory
     */
	public function testGetFactory($builder) {
		$factory = $builder->getFactory();
		
		$this->assertType("FactoryReturnedClass", $factory);
		$this->assertEquals("1", $factory->getArgument());
	}
	
	
	public function testRegisterWithVariableArgument() {
		$this->builder->setVariable("test.variable", 1);
		
		$this->builder	->register("Mock")
						->setClass("MockClass")
						->addArgument(new Dispenser_Element_Variable("test.variable"));
		
		return $this->builder;
	}
	
	
	public function testRegisterWithReferenceArgument() {
		$this->builder	->register("AnotherMock")
						->setClass("AnotherMockClass");
		
		$this->builder	->register("Mock")
						->setClass("MockClass")
						->addArgument(new Dispenser_Element_Reference("AnotherMock"));
		
		return $this->builder;
	}
	
	
	public function testRegisterWithMethods() {
		$this->builder	->register("Mock")
						->setClass("MockClass")
						->addMethod(new Dispenser_Element_Method('setArgument', "1"));
		return $this->builder;
	}
	
	
	/**
     * @depends testRegisterWithMethods
     */
	public function testGetWithMethods($builder) {
		$mockComponent = $builder->getMock();
		
		$this->assertEquals("1", $mockComponent->getArgument());
	}
	
	
	/**
     * @depends testRegisterWithVariableArgument
     */
	public function testGetComponentWithVariableArgument($builder) {
		$mockComponent = $builder->getMock();
		
		$this->assertEquals(1, $mockComponent->getArgument());
	}
	
	
	 /**
     * @depends testRegisterWithReferenceArgument
     */
	public function testGetComponentWithReferenceArgument($builder) {
		$mockComponent = $builder->getMock();
		
		$this->assertType("AnotherMockClass", $mockComponent->getArgument());
	}
    
	
    /**
     * @depends testRegisterMinimal
     */
    public function testGetComponentMinimal($builder) {
    	$test = $builder->getComponent("Test");
    }
    
    
    /**
     * @depends testRegisterShared
     */
    public function testGetComponentShared($builder) {
    	$instance1 = $builder->getComponent("Test");
    	$instance2 = $builder->getComponent("Test");

    	$this->assertSame($instance1, $instance2);
    }
    
    
    /**
     * @depends testRegisterNotShared
     */
    public function testGetComponentNotShared($builder) {
    	$instance1 = $builder->getComponent("test");
    	$instance2 = $builder->getComponent("test");
    	
    	$this->assertNotSame($instance1, $instance2);
    }
    
    
	/**
     * @depends testRegisterShared
     */
    public function testMagicFunctionCall($builder) {
    	$component = $builder->getTest();
    }
    
    
    /**
     * @expectedException Dispenser_Exception
     */
 	public function testInvalidMagicFunctionCall() {	
    	$this->builder->invalidFunctionCall();
    }
    
    
    /**
     * @provider _getMockArrayDefinition
     */
    public function testLoad() {
    	$arrayLoader 	= new Dispenser_Importer_Array($this->_getMockArrayDefinition());
		$this->builder->load($arrayLoader);
    }
    
    
    public function testLoadCorrectlyOverwritesExistingComponent() {
    	$this->builder	->register("Mock" )
    					->setClass("AnotherMockClass");
    	
    	$arrayLoader 	= new Dispenser_Importer_Array($this->_getMockArrayDefinition());
		$this->builder->load($arrayLoader);
		
		$this->assertType("MockClass", $this->builder->getComponent("Mock"));
    }
    
    
    private function _getMockArrayDefinition() {
    	return array(
			'Mock' => array(
				'class' => 'MockClass'
			)
		);
    }
}