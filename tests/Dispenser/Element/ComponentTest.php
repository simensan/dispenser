<?php

require_once 'PHPUnit\Framework\TestCase.php';
require_once '_files\MockClasses.php';
require_once 'Dispenser\AutoLoader.php';


/**
 * Dispenser_Component test case.
 */
class Dispenser_Element_ComponentTest extends PHPUnit_Framework_TestCase {
	
	/**
	 * @var Dispenser_Element_Component
	 */
	private $component;
	
	public function __construct() {
		Dispenser_Autoloader::register();
	}
	
	protected function setUp() {
		parent::setUp ();
		
		$this->component = new Dispenser_Element_Component();	
	}
	
	public function test__construct() {
		$this->component->__construct("Test");

		return $this->component;
	}

	public function testSetId() {
		$this->component->setId("Test");
		
		return $this->component;
	}
	
	/**
	 * @depends testSetId
	 */
	public function testGetId($component) {
		$this->assertEquals("Test", $component->getId());
	}
	
	/**
	 * @depends testSetId
	 */
	public function testSetClass($component) {
		$component->setClass("MockClass");
		
		return $component;
	}
	
	/**
	 * @depends testSetClass
	 */
	public function testGetClass($component) {
		$this->assertEquals("MockClass", $component->getClass());
	}
	
	
	public function testAddArgument() {
		$this->component->addArgument("test");
		
		return $this->component;
	}

	
	public function testAddArguments() {
		
		$this->component->addArguments(
			array(
				"argument1",
				new Dispenser_Element_Reference("some.reference"),
				new Dispenser_Element_Variable("some.variable")
			)
		);
		
		return $this->component;
	}
	
	/**
	 * @depends testAddArguments
	 */
	public function testGetArguments($component) {
		$arguments = $component->getArguments();

		$this->assertEquals(3, count($arguments));
		$this->assertType("string", $arguments[0]);
		$this->assertType("Dispenser_Element_Reference", $arguments[1]);
		$this->assertType("Dispenser_Element_Variable", $arguments[2]);		
	}

	
	public function testAddMethod() {
		$this->component
			->setClass("MockClass")		
			->addMethod(new Dispenser_Element_Method("setArgument", 0));
		
	}
	
	
	public function testAddMethods() {
		$this->component
			->setClass("MockClass")
			->addMethods(array(
				new Dispenser_Element_Method("setArgument", 0),
				new Dispenser_Element_Method("getArgument")
			))
		;
		
		return $this->component;
	}
	
	
	/**
	 * @depends testAddMethods
	 */
	public function testGetMethods($component) {
		$methods = $component->getMethods();

		$this->assertEquals(2, count($methods));
		$this->assertType("Dispenser_Element_Method", $methods[0]);
		$this->assertType("Dispenser_Element_Method", $methods[1]);
	}
	
	
	public function testSetFactory() {
		$this->component->setFactory(new Dispenser_Element_Factory("FactoryClass", "getFactory"));
		
		return $this->component;
	}
	
	
	/**
	 * @depends testSetFactory
	 */
	public function testGetFactory($component) {
		$this->assertNotNull($component->getFactory());
	}
	
	
	/**
	 * @depends testSetFactory
	 */
	public function testHasFactory($component) {
		$this->assertTrue($component->hasFactory());
	}
	
	
	public function testSetShared() {
		$this->component->setShared(true);
		
		return $this->component;
	}
	
	/**
	 * @depends testSetShared
	 */
	public function testIsShared($component) {
		$this->component->setShared(false);
		
		$this->assertFalse($this->component->isShared());
		
		$this->assertTrue($component->isShared());
	}

}
