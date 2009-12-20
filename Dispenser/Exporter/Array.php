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
* @package Dispenser_Exporter
* @author Simen Sandvær <simen.is@gmail.com>
* @copyright Simen Sandvær <simen.is@gmail.com>. All rights reserved.
* @license MIT License
*/

/**
 * Exports a Dispenser_Builder to array definitions. Useful if you want to
 * convert programatically defined wiring to array based, or converting a 
 * external format to Dispenser format. 
 */
class Dispenser_Exporter_Array implements Dispenser_Exporter_Interface {
	
	/**
	 * Array of definitions. 
	 * @var array
	 */
	protected $array = array();

	const DISPENSER_CLASS 		= "class";
	const DISPENSER_SHARED 		= "shared";
	const DISPENSER_METHOD 		= "method";
	const DISPENSER_METHODS		= "methods";
	const DISPENSER_ARGUMENTS	= "arguments";
	const DISPENSER_REFERENCE	= "reference";
	const DISPENSER_VARIABLE	= "variable";
	const DISPENSER_TYPE		= "type";
	const DISPENSER_FACTORY		= "factory";
	const DISPENSER_NAME		= "name";
	
	/**
	* Loads in components from passed Dispenser_Builder instance. 
	*
	* @param Dispenser_Builder
	* @return Dispenser_Exporter_Array
	*/
	public function load(Dispenser_Builder $builder) {
		
		$components = $builder->getComponents();

		foreach($components as $component) {
			$this->array[$component->getId()] = $this->generateComponent($component);
		}
		
		return $this;
	}
	
	/**
	 * Exports the components as a array string representation. Use this with f.ex. 
	 * file_put_contents to make a wiring.php file.  
	 * @return string Generated array definitions as a string. 
	 */
	public function export() {
		$arr = var_export($this->array, true);
		//$arr = preg_replace("/(?:\n)\\s+[^'] \\d+ => /", "", $arr); //Gone for now, till i make a better regexp. 
		return $arr;
	}
	
	/**
	 * Internal function called from @see load to generate a array representation of component method.  
	 */
	protected function generateComponent(Dispenser_Element_Component $component) {
		$componentArray = array();
		$methods = $component->getMethods();
		$arguments = $component->getArguments();
		
		$componentArray[self::DISPENSER_CLASS] =  $component->getClass();
		
		if(empty($arguments) === false) {
			$componentArray[self::DISPENSER_ARGUMENTS] = $this->getParsedArguments($arguments);
		}
	
		$methods = $component->getMethods();
		if(empty($methods) === false) {
			$componentArray[self::DISPENSER_METHODS] = array();
			foreach($methods as $method) {
				$arguments = $method->getArguments();
				if(empty($arguments) === false) {
					$arguments = $this->getParsedArguments($arguments);
				}
				
				$componentArray[self::DISPENSER_METHODS][$method->getMethod()] = $arguments;
			}	
		}
		
		if($component->isShared() === true) {
			$componentArray[self::DISPENSER_SHARED] = "true";
		}
		
		if($component->hasFactory() === true) {
			$factory = $component->getFactory();
			$componentArray[self::DISPENSER_FACTORY] = array(
				self::DISPENSER_CLASS => $factory->getClass(),
				self::DISPENSER_METHOD => $factory->getMethod()
			);
		} 
				
		return $componentArray;
	}
	
	
	/**
	 * Internal function used to parse arguments of constructors / methods.  
	 * @return array
	 */
 	protected function getParsedArguments($arguments) {		
    		
		foreach($arguments as &$argument) {
			if(is_a($argument, "Dispenser_Element_Reference") === true) 
			{
				$argument = array(
					self::DISPENSER_TYPE => self::DISPENSER_REFERENCE,
					self::DISPENSER_NAME => $argument->getName()
				);
			} else if(is_a($argument, "Dispenser_Element_Variable") === true) {
				$argument = array(
					self::DISPENSER_TYPE => self::DISPENSER_VARIABLE,
					self::DISPENSER_NAME => $argument->getName()
				);
			} else {
				if (is_object($argument)) {
					throw new Dispenser_Exception("Can not export an object. Make it a component?.");
				} else if(is_resource($argument)) {
					throw new Dispenser_Exception("Can not export a resource.");
				} 
			}
		}

		return $arguments;
    }
}