<?php
/**
* Copyright (c) 2009 Simen Sandvær <simen.is@gmail.com>
* 
* Permission is hereby granted, free of charge, to any person
* obtaining a copy of this software and associated documentation
* files (the "Software"), to deal in the Software without
* restriction, including without limitation the rights to use,
* copy, modify, merge, publish, distribute, sublicense, and/or sell
* copies of the Software, and to permit persons to whom the
* Software is furnished to do so, subject to the following
* conditions:
* 
* The above copyright notice and this permission notice shall be
* included in all copies or substantial portions of the Software.

* THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
* EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES
* OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
* NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT
* HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY,
* WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
* FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR
* OTHER DEALINGS IN THE SOFTWARE.
*
* @package Dispenser_Importer_Variables
* @author Simen Sandvær <simen.is@gmail.com>
* @copyright Simen Sandvær <simen.is@gmail.com>. All rights reserved.
* @license MIT License
*/


/**
 * Loads variables from an array. 
 *
 */
class Dispenser_Importer_Variables_Ini implements Dispenser_Importer_Variables_Interface {

	/**
	 * Parsed variables from ini file/string
	 * @var array
	 */
	protected $variables = array();
	
	
	/**
	 * Section to load from
	 */
	protected $section = null;
	
	
	/**
	* Get parsed variables. If @link $section is set then returns a subset of variables. 
	*
	* @return array
	*/	
	public function getVariables() {
		if($this->section === null) {
			return $this->variables;
		}
		
		if(isset($this->variables[$this->section]) === true) {
			return $this->variables[$this->section];
		}
		
		throw new Dispenser_Exception("Section '{$this->section}' does not exist.");
	}
	
	
	/**
	* Sets section to load from.  
	*
	* @params string $section @see $section
	*/
	public function setSection($section) {
		$this->section = $section;
	}

	/**
	* Get section to load from
	*
	* @returns string @see $section
	*/
	public function getSection() {
		return $this->section;
	}
	
	
	/**
	* Load ini variables from file
	*
	* @param string $file File to load ini variables from
	* @param string $section Section to load settigns from (Optional) 
	*/
	public function loadFromFile($file, $section = null) {		
		if(file_exists($file) === false) {
			throw new Dispenser_Exception("'$file' does not exist.");
		}
				
		$iniArray = parse_ini_file($file, true);
		
		if ($iniArray === false) {
			throw new Dispenser_Exception("Malformed ini file.");
		}
		
		$this->section = $section;
	
		$this->parseIniArray($iniArray);
	}

	
	/**
	* Load ini variables from string
	*
	* @param string $file String to load ini settings from
	* @param string $section Section to load settigns from (Optional) 
	*/
	public function loadFromString($iniString, $section = null) {
		if (function_exists("parse_ini_string") === false) {
			throw new Dispenser_Exception("This function requires php >= 5.3");
		}
		
		$iniArray = parse_ini_string($iniString, true);
		
		if ($iniArray === false) {
			throw new Dispenser_Exception("Malformed ini string.");
		}
		
		$this->section = $section;
		
		$this->parseIniArray($iniArray);
	}
	
	/**
	* Parse the sections of ini array. Handle extended sections. 
	*
	* @param array $iniArray Ini array to parse
	*/
	protected function parseIniArray($iniArray) {
		$variables = array();
		
        foreach ($iniArray as $section => $values)
        {
        	$sections = explode(":", $section);
			$sectionName = trim($sections[0]); 
			
        	if(count($sections) == 1) { //No extend
        		$variables[$sectionName] = $values;
        	} else if(count($sections) == 2) { // One extend, of form [name : extendedSection]
        		$extendedSection = trim($sections[1]);
        		
        		if(isset($variables[$extendedSection]) === true) {
        			$variables[$sectionName] = array_merge($variables[$extendedSection], $values);
        		} else {
        			throw new Dispenser_Exception("Attempt to extend nonexistant section '$extendedSection'");
        		}
        	} else {
        		 throw new Dispenser_Exception($sectionName . ' can only extend one section.');	
        	}
        }
        
        $this->variables = $variables;
	}
}

