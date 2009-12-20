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
* @package Dispenser_Importer
* @author Simen Sandvær <simen.is@gmail.com>
* @copyright Simen Sandvær <simen.is@gmail.com>. All rights reserved.
* @license MIT License
*/


/**
 * Loads variables from an array. 
 *
 */
class Dispenser_Importer_Xml implements Dispenser_Importer_Interface  {
	
	const DISPENSER_COMPONENT	= "component";
	const DISPENSER_ID			= "id";
	const DISPENSER_CLASS 		= "class";
	const DISPENSER_SHARED 		= "shared";
	const DISPENSER_METHOD 		= "method";
	const DISPENSER_ARGUMENT	= "argument";
	const DISPENSER_REFERENCE	= "reference";
	const DISPENSER_VARIABLE	= "variable";
	const DISPENSER_TYPE		= "type";
	const DISPENSER_NAME		= "name";
	
	/**
	 * Contains the imported components
	 * @var array
	 */
	protected $components = array();
	
	/**
	 * Imported array
	 * @var array Array of definitions
	 */
	protected $array = array();
	
	
	/**
	* Load components from XML file
	*
	* @param string $file File to load components from.
	*/
	public function loadFromFile($file) {		
		if(file_exists($file) === false) {
			throw new Dispenser_Exception("'$file' does not exist.");
		}
		
		$this->_loadXML($file);
	}
	
	
	/**
	* Load components from xml string.
	*
	* @param string $string XML string to load from. 
	*/
	public function loadFromString($string) {		
		if(is_string($string) === false) {
			throw new Dispenser_Exception("First argument must be of type string. ");
		}

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
	* Get parsed components. 
	*
	* @return array
	*/	
	public function getComponents() {
		return $this->components;	
	}
	
	
	/**
	* Parses XML object.
	*
	* @param SimpleXMLElement $xml
	*/	
	protected function parseXml($xml) {
		$nodes = $xml->children();
		
		foreach($nodes as $node) {
			if($node->getName() == self::DISPENSER_COMPONENT) {
				$this->parseComponent($node);
      		}
		}
	}
	
	
	/**
	* Parses a xml component node.
	*
	* @param  SimpleXMLElement $componentNode
	*/
	protected function parseComponent($componentNode) {
		if(isset($componentNode[self::DISPENSER_ID]) === false) {
			throw new Dispenser_Exception("Id must be set in component definition.");
		}
		
		if(isset($componentNode[self::DISPENSER_CLASS]) === false) {
			throw new Dispenser_Exception("Class must be set in component definition.");
		}
		
		$id = (string)$componentNode[self::DISPENSER_ID];
		$component = new Dispenser_Element_Component($id);;
		$component->setClass((string)$componentNode[self::DISPENSER_CLASS]);
		
		if(isset($componentNode[self::DISPENSER_SHARED])) {
			$component->setShared((string)$componentNode[self::DISPENSER_SHARED]);
		}	

		if(isset($componentNode->factory) === true) {
			$component->setFactory($this->parseFactory($componentNode->factory));
		}
		
		if(isset($componentNode->arguments) === true) {
			$component->addArguments($this->parseArguments($componentNode->arguments));
		}
		
		if(isset($componentNode->methods) === true) {
			$component->addMethods($this->parseMethods($componentNode->methods));
		}
				
		$this->components[$id] = $component;
	}
	
	/**
	* Parses a factory array definition
	*
	* @param  SimpleXMLElement $factoryXml Factory definition
	* @return Dispenser_Element_Factory
	*/	
	protected function parseFactory($factoryXml) {
		$factory = new Dispenser_Element_Factory();
				
		$factory->setClass((string)$factoryXml[self::DISPENSER_CLASS]);
		$factory->setMethod((string)$factoryXml[self::DISPENSER_METHOD]);
		
		return $factory;
	}
	
	/**
	* Parses method nodes. 
	*
	* @param SimpleXMLElement $methodsNode 
	* @return array
	*/
	protected function parseMethods($methodsNode) {
		$methods = array();
		$methodNodes = $methodsNode->children();
		
		foreach($methodNodes as $methodNode) {
			$method = new Dispenser_Element_Method();
			$method->setMethod((string)$methodNode[self::DISPENSER_NAME]);
			
			if(isset($methodNode->arguments) === true) {
				$method->setArguments($this->parseArguments($methodNode->arguments));	
			}	
	
			$methods[] = $method;
		}
		
		return $methods;
	}
	
	/**
	* Parses argument nodes. 
	*
	* @param SimpleXMLElement $argumentsArray
	* @return array
	*/
	protected function parseArguments($argumentsNode) {
		$arguments = array();
		$argumentNodes = $argumentsNode->children();

		foreach($argumentNodes as $argumentNode) {
			
			if($argumentNode->getName() !== self::DISPENSER_ARGUMENT) {
				throw new Dispenser_Exception("Invalid node type ({$argumentNode->getName()} under arguments node.");	
			}
			
			if(isset($argumentNode[self::DISPENSER_TYPE]) === true) {
				if((string)$argumentNode[self::DISPENSER_TYPE] === self::DISPENSER_REFERENCE) {
					$argument = new Dispenser_Element_Reference((string)$argumentNode);
				} else if((string)$argumentNode[self::DISPENSER_TYPE] === self::DISPENSER_VARIABLE) {
					$argument = new Dispenser_Element_Variable((string)$argumentNode);
				}  else {
					throw new Dispenser_Exception("Unknown argument type '{$argumentNode[self::DISPENSER_TYPE]}");
				}
				
				$arguments[] = $argument;
			} else {
				$arguments[] = (string)$argumentNode;
			}
		}
		
		return $arguments;
	}
}