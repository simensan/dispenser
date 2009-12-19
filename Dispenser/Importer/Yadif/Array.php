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
* @package Dispenser_Importer_External_Yadif
* @author Simen Sandvær <simen.is@gmail.com>
* @copyright Simen Sandvær <simen.is@gmail.com>. All rights reserved.
* @license MIT License
*/


/**
 * Loads variables from a yadif array. 
 *
 */
class Dispenser_Importer_Yadif_Array implements Dispenser_Importer_Interface  {
	
	/**
	 * Dispenser array importer
	 * @var Dispenser_Importer_Array
	 */
	protected $dispenserArray;
	
	/**
	 * Yadif array
	 * @var array
	 */
	protected $array;
	
	/**
	 * Parsed array
	 * @var array
	 */
	protected $parsedArray;
	
	const YADIF_CLASS 		= "class";
	const YADIF_SHARED 		= "scope";
	const YADIF_SHARED_DEF	= "singleton";
	const YADIF_ARGUMENTS	= "arguments";
	const YADIF_PARAMS 		= "params";
	const YADIF_METHOD 		= "method";
	const YADIF_METHODS		= "methods";
	const YADIF_FACTORY		= "factory";
	
	
	/**
	* Constructor
	*
	* @param array $array Yadif array of definitions
	*/
	public function __construct($array = array()) {
		$this->dispenserArray = new Dispenser_Importer_Array();
		$this->setArray($array);
	}
	
	/**
	* Sets yadif array to import, alternative to passing in constructor
	*
	* @param array $array Yadif array of definitions
	*/
	public function setArray($array) {
		$this->array = $array;
		
		$this->parse();
	}

	/**
	* Get parsed components. 
	*
	* @return array
	*/	
	public function getComponents() {
		
		return $this->dispenserArray->getComponents();	
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
			$parsedComponent = array();
			
			if(isset($componentItem[self::YADIF_CLASS]) === true) {
				$parsedComponent[Dispenser_Importer_Array::DISPENSER_CLASS] = $componentItem[self::YADIF_CLASS];
			} else {
				$parsedComponent[Dispenser_Importer_Array::DISPENSER_CLASS] = $id;
			}
			
			if(isset($componentItem[self::YADIF_METHODS]) === true) {
				if(empty($componentItem[self::YADIF_METHODS]) === false) {
					$parsedComponent[Dispenser_Importer_Array::DISPENSER_METHODS] = $this->_parseMethods($componentItem[self::YADIF_METHODS]);	
				}
			}
			
			$params = array();
			
			if(isset($componentItem[self::YADIF_PARAMS]) === true) {
				$params = $componentItem[self::YADIF_PARAMS];
			}
			
			if(isset($componentItem[self::YADIF_ARGUMENTS]) === true) {
				if(empty($componentItem[self::YADIF_ARGUMENTS]) === false) {
					$parsedComponent[Dispenser_Importer_Array::DISPENSER_ARGUMENTS] = $this->_parseArguments($componentItem[self::YADIF_ARGUMENTS], $params);	
				}
			}
			
			if(isset($componentItem[self::YADIF_FACTORY]) === true) {
				$parsedComponent[Dispenser_Importer_Array::DISPENSER_FACTORY] = array(
					Dispenser_Importer_Array::DISPENSER_CLASS => $componentItem[self::YADIF_FACTORY][0],
					Dispenser_Importer_Array::DISPENSER_METHOD => $componentItem[self::YADIF_FACTORY][1],
				);
			}
			
			if(isset($componentItem[self::YADIF_SHARED]) === true) {
				if($componentItem[self::YADIF_SHARED] == self::YADIF_SHARED_DEF) {
					$parsedComponent[Dispenser_Importer_Array::DISPENSER_SHARED] = true;
				}
			}
			
			$this->parsedArray[$id] = $parsedComponent;
		}
		
		$this->dispenserArray->setArray($this->parsedArray);
	}
	
	private function _parseArguments($arguments, $params) {
		$parsedArguments = array();
		
		foreach($arguments as $argument) {
			
			if($argument[0] === ":") {
				$parsedArguments[] = $params[$argument];
			} else if($argument[0] === "%") {
				$parsedArguments[] = array(Dispenser_Importer_Array::DISPENSER_TYPE=>Dispenser_Importer_Array::DISPENSER_VARIABLE, Dispenser_Importer_Array::DISPENSER_NAME=>$argument);
			} else if(is_string($argument)){
				$parsedArguments[] = array(Dispenser_Importer_Array::DISPENSER_TYPE=>Dispenser_Importer_Array::DISPENSER_REFERENCE, Dispenser_Importer_Array::DISPENSER_NAME=>$argument);	
			} else {
				$parsedArguments[] = $argument;
			}
		}
		
		return $parsedArguments;
	}
	
	private function _parseMethods($methods) {
		$parsedMethods = array();
		
		foreach($methods as $method) {
			$parsedMethod = array();
			
			$params = array();
			
			if(isset($method[self::YADIF_PARAMS]) === true) {
				$params = $method[self::YADIF_PARAMS];
			}
			
			if(isset($method[self::YADIF_ARGUMENTS]) === true) {
				if(empty($method[self::YADIF_ARGUMENTS]) === false) {
					$parsedMethod = $this->_parseArguments($method[self::YADIF_ARGUMENTS], $params);	
				}
			}
			
			$parsedMethods[$method[self::YADIF_METHOD]] = $parsedMethod;
		}
		
		return $parsedMethods;
	}
}