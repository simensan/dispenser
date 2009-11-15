<?php

require_once 'Dispenser\AutoLoader.php';

require_once 'PHPUnit\Framework\TestCase.php';

/**
 * Dispenser_Importer_Variables_Ini test case.
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
	 * Tests ini->loadFromString()
	 */
	public function testLoadFromString() {
		if (function_exists("parse_ini_string") === false) {
			return;
		}
		
		$ini = file_get_contents("_files/ini/Legal.ini");
		$this->ini->loadFromString($ini);
		
		return $this->ini;
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

