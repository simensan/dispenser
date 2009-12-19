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
	public function testAddNonArrayThrowsException() {
		$this->array->setArray(0);
	}
	
	/**
     * @expectedException Dispenser_Exception
     */
	public function testAddArrayWithoutClassThrowsException() {
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
	public function testAddArrayWithInvalidArgumentTypeThrowsException() {
		$data = $this->getDefinitionWithInvalidArgumentType();
		$this->array->setArray($data);	
	}
	
	/**
     * @expectedException Dispenser_Exception
     */
	public function testAddArrayWithNonArrayComponentThrowsException() {
		$data = $this->getNonArrayComponent();
		$this->array->setArray($data);
	}
	
	/**
     * @expectedException Dispenser_Exception
     */
	public function testAddArrayWithIllegalComponentKeyException() {
		$data = $this->getIllegalComponentKey();
		$this->array->setArray($data);
	}
	
	
	
	/**
	 * Tests array->getComponents()
	 */
	public function testGetComponents() {
		$component = $this->array->getComponents();
	}
	
	private function getDefinitionWithoutClass() {
		$data = array(
			'Test' => array()
		);	
		
		return $data;
	}
	
	private function getDefinitionWithInvalidEntry() {
		$data = array(
			'Test' => array(
				'class' => 'MockClass',
				'unknownkey' => ''
			)
		);	
		
		return $data;
	}
	
	private function getDefinitionWithInvalidArgumentType() {
		$data = array(
			'Test' => array(
				'class' => 'MockClass',
				'arguments' => array(array('type' => 'unknowntype'))
			)
		);	
		
		return $data;
	}
	
	private function getIllegalComponentKey() {
		$data = array(
			'Renderer' => array(
				'class' => 'FactoryReturnedClass',
				'Illegal Key' => ''
			)
		);

		return $data;
	}
	
	private function getNonArrayComponent() {
		$data = array(
			'Renderer' => 'FactoryReturnedClass'
		);

		return $data;
	}
	
	private function getBaseLegalData() {
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
