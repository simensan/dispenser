<?php

require_once 'Dispenser\AutoLoader.php';

require_once 'PHPUnit\Framework\TestCase.php';

/**
 * Dispenser_Importer_Variables_Ini test case.
 * Huge difference in how php 5.3 and 5.2 handles errors in parse_ini_file. Currently tests work against 5.2. 
 */
class Dispenser_Importer_Variables_IniTest extends PHPUnit_Framework_TestCase {
	
	/**
	 * @var Dispenser_Importer_Variables_Ini
	 */
	private $ini;
	
	public function __construct() {
		Dispenser_Autoloader::register();
	}
	
	
	protected function setUp() {
		$this->ini = new Dispenser_Importer_Variables_Ini();
	}
	
	
	
	/**
	 * Tests ini->getVariables()
	 */
	public function testGetVariables() {
		$this->ini->getVariables();
	}
	
		
	/**
	 * Tests ini->loadFromFile()
	 */
	public function testLoadFromFile() {
		$this->ini->loadFromFile("_files/ini/Legal.ini", "test");
		$variables = $this->ini->getVariables();
		
		$this->assertEquals($variables['test.variable'], "a");
		
		return $this->ini;
	}
	
	
	/**
	 * @expectedException Dispenser_Exception
	 */
	public function testLoadMalformedIniDefinitionThrowsException() {
		@$this->ini->loadFromFile("_files/ini/Malformed.ini");
	}
	
	
	/**
	 * @expectedException Dispenser_Exception
	 */
	public function testLoadIllegalIniDefinition() {
		$this->ini->loadFromFile("_files/ini/IllegalExtend.ini");
	}
	
	
	/**
	 * @expectedException Dispenser_Exception
	 */
	public function testLoadlIniDefinitionWithIllegalExtend() {
		$this->ini->loadFromFile("_files/ini/IllegalExtend.ini");
	}
	
	/**
	 * @expectedException Dispenser_Exception
	 */
	public function testLoadlIniDefinitionWithIllegalDoubleExtend() {
		$this->ini->loadFromFile("_files/ini/DoubleExtend.ini");
	}
	
	
	/**
	 * @expectedException Dispenser_Exception
	 */
	public function testLoadNonExistantFileThrowsException() {
		$this->ini->loadFromFile("ThisIsntAExistantFile.ini");
	}
	
	
	/**
	 * Tests ini->setSection()
	 * @depends testLoadFromFile
	 */
	public function testSetSection($ini) {
		$ini->setSection("production");
		
		$section = $ini->getVariables();
		
		$this->assertEquals("b", $section['test.variable']);
		
		return $ini;
	}
	
	
	/**
	 * @expectedException Dispenser_Exception
	 */
	public function testSettingNonExistantSectionThenCallingGetVariablesThrowsException() {
		$this->ini->setSection("non.existant.section");
		
		$section = $this->ini->getVariables();
	}
	
	
	/**
	 * Tests ini->getSection()
	 * @depends testSetSection
	 */
	public function testGetSection($ini) {
		$this->assertEquals("production", $ini->getSection());
	}
	
}

