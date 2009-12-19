<?php

set_include_path(implode(PATH_SEPARATOR, array(
 	realpath(dirname(__FILE__) . "\..\.."), //Point this to where Dispenser folder resides
    get_include_path(),
)));

require_once 'PHPUnit\Framework\TestSuite.php';

require_once 'Element\ComponentTest.php';
require_once 'BuilderTest.php';
require_once 'Importer\ArrayTest.php';
require_once 'Importer\Variables\IniTest.php';


class DispenserTestSuite extends PHPUnit_Framework_TestSuite {
	
	public function __construct() {
		$this->setName ('Dispenser Test Suite');
		chdir(dirname(__FILE__));	//Fix (hack?) for eclipse PHPUnit making getcwd() some eclipse dir
		$this->addTestSuite ('Dispenser_Element_ComponentTest');
		$this->addTestSuite ('Dispenser_BuilderTest');
		$this->addTestSuite ('Dispenser_Importer_ArrayTest');
		$this->addTestSuite ('Dispenser_Importer_Variables_IniTest');
	}
	
	public static function suite() {
		return new self ( );
	}
}
