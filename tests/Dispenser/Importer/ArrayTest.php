<?php

require_once 'PHPUnit\Framework\TestCase.php';
require_once 'Dispenser\AutoLoader.php';

/**
 * Dispenser_Importer_Array test case.
 */
class Dispenser_Importer_ArrayTest extends PHPUnit_Framework_TestCase {
	
	/**
	 * @var Dispenser_Importer_Array
	 */
	private $array;
	
	public function __construct() {
		Dispenser_Autoloader::register();
	}
	
	protected function setUp() {
		$this->array = new Dispenser_Importer_Array();
	}
	
	/**
	 * Tests array->setArray()
	 */
	public function testSetArray() {
		$data = $this->getBaseLegalData();
		$this->array->setArray($data);
	}
		
	public function testAddArray() {
		$data = $this->getBaseLegalData();
		$this->array->addArray($data);
	}
	
	/**
     * @expectedException Dispenser_Exception
     */
	public function testAddArrayWithoutClassThrowException() {
		$data = $this->getDefinitionWithoutClass();
		$this->array->setArray($data);
	}
	
	/**
     * @expectedException Dispenser_Exception
     */
	public function testAddArrayWithInvalidEntryThrowsException() {
		$data = $this->getDefinitionWithInvalidEntry();
		$this->array->setArray($data);
	}
	
	/**
     * @expectedException Dispenser_Exception
     */
	public function testAddArrayWithInvalidArgumentTypeThrowException() {
		$data = $this->getDefinitionWithInvalidArgumentType();
		$this->array->setArray($data);	
	}
	
	/**
	 * Tests array->getComponents()
	 */
	public function testGetComponents() {
		$component = $this->array->getComponents();
	}
	
	public function getDefinitionWithoutClass() {
		$data = array(
			"Test" => array()
		);	
		
		return $data;
	}
	
	public function getDefinitionWithInvalidEntry() {
		$data = array(
			"unknownkey" => ""
		);	
		
		return $data;
	}
	
	public function getDefinitionWithInvalidArgumentType() {
		$data = array(
			"arguments" => array("unknowntype" => "")
		);	
		
		return $data;
	}
	
	
	public function getBaseLegalData() {
		$data = array(
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
				'shared' => 'true'
			),
			'Test' => array(
				'class' => 'Test',
				'shared' => 'false',
				'arguments' => array(
					'arg'
				),
				'methods' => array(
					'setSomething' => array(
						array(
							'type' => 'reference',
							'name' => 'RefClass'
						),
						array(
							'type' => 'variable',
							'name' => 'a.test'
						)
					),
					'setSomeOtherThing' => array(
						array(
							'type' => 'variable',
							'name' => 'test.config'
						)
					)
				)
			),
		);
		
		return $data;
	}
}
