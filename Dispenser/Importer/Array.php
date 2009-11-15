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
		
			if(isset($componentItem['class']) === false) {
				throw new Dispenser_Exception("Class must be set in component definition.");
			}
			
			$component = new Dispenser_Element_Component($id);
						
			$component->setClass($componentItem['class']);
			
			unset($componentItem['class']);
			
			if(isset($componentItem['shared'])) {
				$component->setShared($componentItem['shared']);
				unset($componentItem['shared']);
			}	
					
			if(isset($componentItem['factory'])) {
				$component->setFactory($this->parseFactory($componentItem['factory']));
				unset($componentItem['factory']);
			}
			
			if(isset($componentItem['methods'])) {
				$component->addMethods($this->parseMethods($componentItem['methods']));
				unset($componentItem['methods']);
			}
			
			if(isset($componentItem['arguments'])) {
				$component->addArguments($this->parseArguments($componentItem['arguments']));
				unset($componentItem['arguments']);
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
				
		$factory->setClass($factoryArray['class']);
		$factory->setMethod($factoryArray['method']);
		
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
		
		foreach($argumentsArray as $argumentItem) {
			if(is_array($argumentItem)) {
				if($argumentItem['type'] === "reference") {
					$argument = new Dispenser_Element_Reference($argumentItem['name']);
				} else if($argumentItem['type'] === "variable") {
					$argument = new Dispenser_Element_Variable($argumentItem['name']);
				}  else {
					throw new Dispenser_Exception("Unknown argument type '{$argumentItem['type']}");
				}
				
				//$argument->setName($argumentItem['name']);
				$arguments[] = $argument;
			} else {
				$arguments[] = $argumentItem;
			}
		}
		
		return $arguments;
	}
}