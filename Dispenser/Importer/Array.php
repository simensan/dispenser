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
class Dispenser_Importer_Array implements Dispenser_Importer_Interface  {
	
	const DISPENSER_CLASS 		= "class";
	const DISPENSER_SHARED 		= "shared";
	const DISPENSER_ARGUMENTS	= "arguments";
	const DISPENSER_METHOD		= "method";
	const DISPENSER_METHODS		= "methods";
	const DISPENSER_FACTORY		= "factory";
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
	* Constructor
	*
	* @param array $array Array of definitions
	*/
	public function __construct($array = array()) {
		$this->setArray($array);
	}
	
	/**
	* Sets array to import, alternative to passing in constructor
	*
	* @param array $array Array of definitions
	*/
	public function setArray($array) {
		$this->array = $array;
		
		$this->parse();
	}

	/**
	* Merges with exisiting array if one, overwrites existing. 
	*
	* @param array $array Array of definitions
	*/
	public function addArray($array) {
		$this->array = array_merge($this->array, $array);
		
		$this->parse();
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
	* Parses passed array.  
	*
	*/	
	protected function parse() {
		
		if(is_array($this->array) === false) {
			throw new Dispenser_Exception("Passed argument is not an array.");
		}
		
		foreach($this->array as $id=>$componentItem) {
			if(is_array($componentItem) === false) {
				throw new Dispenser_Exception("Passed array is malformed, component definition is not an array.");
			}
		
			if(isset($componentItem[self::DISPENSER_CLASS]) === false) {
				throw new Dispenser_Exception("Class must be set in component definition.");
			}
			
			$component = new Dispenser_Element_Component($id);
						
			$component->setClass($componentItem[self::DISPENSER_CLASS]);
			
			unset($componentItem[self::DISPENSER_CLASS]);
			
			if(isset($componentItem[self::DISPENSER_SHARED])) {
				$component->setShared($componentItem[self::DISPENSER_SHARED]);
				unset($componentItem[self::DISPENSER_SHARED]);
			}	
					
			if(isset($componentItem[self::DISPENSER_FACTORY])) {
				$component->setFactory($this->parseFactory($componentItem[self::DISPENSER_FACTORY]));
				unset($componentItem[self::DISPENSER_FACTORY]);
			}
			
			if(isset($componentItem[self::DISPENSER_METHODS])) {
				$component->addMethods($this->parseMethods($componentItem[self::DISPENSER_METHODS]));
				unset($componentItem[self::DISPENSER_METHODS]);
			}
			
			if(isset($componentItem[self::DISPENSER_ARGUMENTS])) {
				$component->addArguments($this->parseArguments($componentItem[self::DISPENSER_ARGUMENTS]));
				unset($componentItem[self::DISPENSER_ARGUMENTS]);
			}
			
			if(empty($componentItem) === false) {
				$unknownKeys = array();
				foreach($componentItem as $key=>$value) {
					$unknownKeys[] = $key;
				}
				
				throw new Dispenser_Exception("Unknown keys: (" . implode(", ", $unknownKeys) . ")");
			}
			
			$this->components[$id] = $component;
		}
	}
	
	/**
	* Parses a factory array definition
	*
	* @param  array $factoryArray Factory definition
	* @return Dispenser_Element_Factory
	*/	
	protected function parseFactory($factoryArray) {
		$factory = new Dispenser_Element_Factory();
				
		$factory->setClass($factoryArray[self::DISPENSER_CLASS]);
		$factory->setMethod($factoryArray[self::DISPENSER_METHOD]);
		
		return $factory;
	}
	
	/**
	* Parses an array of method definitions
	*
	* @param array $methodsArray Array of method definitions
	* @return array
	*/
	protected function parseMethods($methodsArray) {
		$methods = array();
		
		foreach($methodsArray as $methodName=>$methodArguments) {
			$method = new Dispenser_Element_Method();
			$method->setMethod($methodName);
			
			if(is_array($methodArguments)) {
				$method->setArguments($this->parseArguments($methodArguments));	
			}	
	
			$methods[] = $method;
		}
		
		return $methods;
	}
	
	/**
	* Parses an array of method definitions
	*
	* @param array $argumentsArray Array of argument definitions
	* @return array
	*/
	protected function parseArguments($argumentsArray) {

		$arguments = array();
		/*print ".";
		var_dump($argumentsArray);
		print ".";*/
		foreach($argumentsArray as $argumentItem) {
			
			if(is_array($argumentItem)&& isset($argumentItem[self::DISPENSER_TYPE]) === true) {
				if($argumentItem[self::DISPENSER_TYPE] === self::DISPENSER_REFERENCE) {
					$argument = new Dispenser_Element_Reference($argumentItem[self::DISPENSER_NAME]);
				} else if($argumentItem[self::DISPENSER_TYPE] === self::DISPENSER_VARIABLE) {
					$argument = new Dispenser_Element_Variable($argumentItem[self::DISPENSER_NAME]);
				}  else {
					throw new Dispenser_Exception("Unknown argument type '{$argumentItem[self::DISPENSER_TYPE]}");
				}
				
				$arguments[] = $argument;
			} else {
				$arguments[] = $argumentItem;
			}
		}
		
		return $arguments;
	}
}