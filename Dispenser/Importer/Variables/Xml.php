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
 * Loads variables from an XML file. 
 *
 */
class Dispenser_Importer_Variables_Xml implements Dispenser_Importer_Variables_Interface {

	/**
	 * Parsed variables from xml.
	 * @var array
	 */
	protected $variables = array();
	
	/**
	 * Array of sections
	 * @var array
	 */
	protected $sections = array();
	
	
	/**
	 * Section to load from
	 */
	protected $section = null;
	
	/**
	 * Parse errors reported from libxml_get_errors.
	 */
	protected $parseErrors = array();
	
	
	const VARIABLE = "variable";
	
	
	/**
	* Get parsed variables. If @link $section is set then returns a subset of variables. 
	*
	* @return array
	*/	
	public function getVariables() {
		if($this->section === null) {
			return $this->variables;
		}
		
		if(isset($this->sections[$this->section]) === true) {
			return array_merge($this->variables, $this->sections[$this->section]);
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
	* Get parse errors if any. Returns empty array if none
	*
	* @returns array Empty array if no errors. 
	*/
	public function getErrors() {
		return $this->parseErrors;
	}
	
	
	/**
	* Load variables from file
	*
	* @param string $file File to load variables from
	* @param string $section Section to load variables from (Optional) (eg. production/testing) 
	*/
	public function loadFromFile($file, $section = null) {		
		if(file_exists($file) === false) {
			throw new Dispenser_Exception("'$file' does not exist.");
		}
		
		$this->section = $section;
		$this->_loadXML($file);
	}
	
	/**
	* Load variables from xml string.
	*
	* @param string $string XML string to load from. 
	* @param string $section Section to load variables from (Optional) (eg. production/testing) 
	*/
	public function loadFromString($string, $section = null) {		
		if(is_string($string) === false) {
			throw new Dispenser_Exception("First argument must be of type string. ");
		}
		
		$this->section = $section;
		$this->_loadXML($string, false);
	}
	
	
	/**
	* Internal function used to load XML from file/string. 
	* Called by @see loadFromString and @see loadFromFile
	*
	* @param string $obj Either filename of the XML file or a XML string. 
	* @param boolean $isFromFile True if loading from file, false if from string. 
	*/
	private function _loadXML($obj, $isFromFile = true) {
		$previousUseInteralErrorsValue = libxml_use_internal_errors(true); //Disable errors spewing out.
		
		if($isFromFile === true) {
			$xml = simplexml_load_file($obj);
		} else {
			$xml = simplexml_load_string($obj);
		}
		
		$xmlParseErrors = libxml_get_errors();
		
		if (count($xmlParseErrors) > 0) {
			$this->parseErrors = $xmlParseErrors;
			libxml_clear_errors();
			
			throw new Dispenser_Exception("Malformed xml file.");
		}
		
		libxml_use_internal_errors($previousUseInteralErrorsValue); //Make sure to return it to previous value
		
		$this->parseXml($xml);
	}

	
	/**
	* Parse the xml SimpleXMLElement object.  
	*
	* @param SimpleXMLElement $xml SimpleXMLElement of loaded xml. 
	*/
	protected function parseXml($xml) {
	   	$nodes = $xml->children();
      	
      	foreach($nodes as $node) {
      		if($node->getName() == self::VARIABLE) {
      			  $this->variables[(string)$node['key']] = (string)($node['value']);
      		} else {
      			$nodeChildren = $node->children();
      			
      			$vars = array();
      			
      			if(isset($node['extends']) === true) {
      				$extends = (string)$node['extends'];
      				
      				if(isset($this->sections[$extends]) === true) {
      					$vars = $this->sections[$extends];
      				}
      			}
      			
      			foreach($nodeChildren as $child) {
      				$vars[(string)$child['key']] = (string)$child['value'];	
      			}
      			
      			$this->sections[$node->getName()] = $vars;
      		}
      	}
	}
}

