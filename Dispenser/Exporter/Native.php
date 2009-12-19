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
 * Exports a Dispenser_Builder to raw php. Resulting class extends @see Dispenser_Builder. 
 * References / hardcoded arguments are exported, elements of type Dispenser_Element_Variable are not,
 * so you must load variables into the exporter class too if you use them. 
 */
class Dispenser_Exporter_Native implements Dispenser_Exporter_Interface {
	
	/**
	 * Class name of exported class.  
	 * @var string
	 */
	protected $exportedClassName = "Dispenser_Wrapped_Builder"; 
	
	/**
	 * Array of private variables used in exported class for component instances. 
	 * @var array
	 */
	protected $variables = array();
	
	/**
	 * Array of component method definitions. 
	 * @var array
	 */
	protected $methods = array();
	
	
	/**
	* Loads in components from passed Dispenser_Builder instance. 
	*
	* @param Dispenser_Builder
	* @return Dispenser_Exporter_Wrapper
	*/
	public function load(Dispenser_Builder $builder) {
		
		$components = $builder->getComponents();

		foreach($components as $component) {
			$this->generateComponent($component);
		}
		
		return $this;
	}
	
	/**
	 * Exports the loaded in components as a string. 
	 * @return string Generated php class as a string. 
	 */
	public function export() {
		
		return 
			"<?php \n\n".
			"class $this->exportedClassName extends Dispenser_Builder { \n" .
			//'	private $shared = array(); ' . "\n" .
			$this->getVariables() .
			"\n\n" .
			$this->getMethods() .
			'}'
		;
	}
	
	
	/**
	 * Sets name of the exported class. 
	 * @param string $name Name of class
	 * @returns Dispenser_Exporter_Wrapper
	 */
	public function setClassName($name) {
		$this->exportedClassName = $name;
	}
	
	
	/**
	 * Gets name of the exported class. 
	 * @return string Name of exported class. 
	 */
	public function getClassName() {
		return $this->exportedClassName;
	}
	
	
	/**
	 * Internal function used to return string representation of variables used in exported class. 
	 * @return string 
	 */
	protected function getVariables() {
		return implode("\n", $this->variables);
	}
	
	
	/**
	 * Internal function used to return generated component methods as a string. 
	 * @return string 
	 */
	protected function getMethods() {
		return implode("\n\n", $this->methods);
	}
	
	
	/**
	 * Internal function called from @see load to generate a string representation of component method.  
	 */
	protected function generateComponent(Dispenser_Element_Component $component) {
		$methods = $component->getMethods();
		$arguments = $component->getArguments();
		
		if($component->isShared() === true) {
			$this->variables[] = "	private $" . $component->getId() . ";";
		}
		
		$methodString = "	/**\n" .
						"	* @return " . $component->getClass() . "\n" .
						"	*/ \n" .
						"	public function get" . $component->getId() . "() { \n";
		
		$instantiationTabs = "		";
		
		if($component->isShared() === true) {
			$methodString .= '		if($this->' . $component->getId() . " == null) { \n";
			$instantiationTabs = "			";
		}
		
		if($component->hasFactory() === true) {
			$factory = $component->getFactory();
			$methodString .= $instantiationTabs . '$instance = ' . $factory->getClass() . '::' . $factory->getMethod() . "(";
			
		} else {
			$methodString .= $instantiationTabs . '$instance = new ' . $component->getClass() . '(';
		}
		
		if(empty($arguments) === false) {
			$arguments = $this->getParsedArguments($arguments);
			$methodString .= implode(", ", $arguments);
		}
			
		$methodString .= ");\n";
				
		$methods = $component->getMethods();
		if(empty($methods) === false) {
			foreach($methods as $method) {
				$methodString .= $instantiationTabs . '$instance->' . $method->getMethod() . '(';
				$arguments = $method->getArguments();
				if(empty($arguments) === false) {
					$arguments = $this->getParsedArguments($arguments);
					$methodString .= implode(", ", $arguments);
				}
				$methodString .= "); \n";
			}	
		}
		
		if($component->isShared() === true) {
			$methodString .= $instantiationTabs . '$this->' . $component->getId() . ' = $instance;' . "\n";
			$methodString .= "		} \n\n";
			$methodString .= '		return $this->' . $component->getId() . ';' . "\n";
		} else {
			$methodString .= '		return $instance;' . "\n";
		}
				
		$methodString .= "	}\n";
		
		$this->methods[] = $methodString;
	}
	
	
	/**
	 * Internal function used to parse arguments of constructors / methods.  
	 * @return array
	 */
 	protected function getParsedArguments($arguments) {		
    		
		foreach($arguments as &$argument) {
			if(is_a($argument, "Dispenser_Element_Reference") === true) 
			{
				$argument = '$this->get' . $argument->getName() . "()";
			} else if(is_a($argument, "Dispenser_Element_Variable") === true) {
				$argument = '$this->getVariable("' . $argument->getName() . '")';
			} else {
				if (is_object($argument)) {
					throw new Dispenser_Exception("Can not export an object. Make it a component?.");
				} else if(is_resource($argument)) {
					throw new Dispenser_Exception("Can not export a resource.");
				} else {
					$argument = var_export($argument, true);
				}	
			}
		}

		return $arguments;
    }

}