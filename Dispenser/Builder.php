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
* @package Dispenser
* @author Simen Sandvær <simen.is@gmail.com>
* @copyright Simen Sandvær <simen.is@gmail.com>. All rights reserved.
* @license MIT License
*/


/**
 * Main interface for building / loading DI definitions. 
 *
 */
class Dispenser_Builder {
	
	/**
	 * Array of components
	 * @var array
	 */
	protected $components = array();

	/**
	 * Array of component instances, used if component is set to be not shared. 
	 * @var array
	 */
	protected $instances = array();
	
	/**
	 * Array of variables used by components
	 * @var array
	 */
	protected $variables = array();
	

	/**
	 * Sets a variable value
	 * @param string $name
	 * @param mixed $value
	 * @returns Dispenser_Builder $this
	 */
	public function setVariable($name, $value) {
		if(is_string($name) === false) {
			throw new Dispenser_Exception("Variable name must be of type string");
		}
		
		$this->variables[$name] = $value;
		
		return $this;
	}
	
	
	/**
	 * Sets an array of variable values
	 * @param array $variables
	 * @returns Dispenser_Builder $this
	 */
	public function setVariables($variables) {
		if(is_array($variables) === false) {
			throw new Dispenser_Exception("Passed argument is not an array.");
		}
		
		foreach($variables as $name=>$value) {
			$this->setVariable($name, $value);
		}
		
		return $this;
	}
	
	
	/**
	* Get parsed variables. If @link $section is set then returns a subset of variables. 
	*
	* @return array
	*/	
	public function getVariable($name) {
		
		if(isset($this->variables[$name]) === true) {			
			return $this->variables[$name];
		}
		
		throw new Dispenser_Exception("Variable '$name' does not exist.");
	}
	
	
	/**
	* Get parsed variables. If @link $section is set then returns a subset of variables. 
	*
	* @return array
	*/	
	public function getVariables() {
		return $this->variables;
	}
	
	
	/**
	 * Registers a new component
	 * 
	 * @param string $id
	 * @return Dispenser_Element_Component
	 */
	public function register($id) {
		$component = new Dispenser_Element_Component($id);
		$this->components[$id] = $component;
		
		return $component;
	}
	
	
	/**
	 * Loads components from an external loader
	 * 
	 * @param Dispenser_Importer_Interface $loader
	 * @return Dispenser_Element_Component
	 */
	public function load(Dispenser_Importer_Interface $loader) {
		$this->components = array_merge($this->components, $loader->getComponents());
	
		return $this;
	}
	
	
	/**
	* Magic function used for component retrieval of format get[component name], 
	* i.e. getMock would be equivalent to doing getComponent("Mock");
	* @see getComponent
	*
	* @return object
	*/	
	public function __call($method, $args)
	{
		if(substr($method, 0, 3) !== "get") {
			throw new Dispenser_Exception("Invalid magic function call, must start with 'get'.");
        }
        
		$componentId = substr($method, 3);
		
		return $this->getComponent($componentId);
    }
    

   /**
	* Returns component, if it exists, given passed component id. 
	*  
	* @params string $componentId
	* @return object
	*/	
    public function getComponent($componentId) {

    	if(isset($this->components[$componentId]) === false) {
    		throw new Dispenser_Exception("Component '$componentId' does not exist.");
    	}
    	
		$component = $this->components[$componentId];

		if($component->isShared() && isset($this->instances[$componentId])) {
			return $this->instances[$componentId];
		}
		
    	$arguments = $this->getParsedArguments($component->getArguments());
    	
    	if($component->hasFactory()) {
    		$factory = $component->getFactory();    		
    		$componentInstance = call_user_func_array(array($factory->getClass(), $factory->getMethod()), $arguments);
    	}
		else {			
			$reflectedClass = new ReflectionClass($component->getClass());
			
			if(empty($arguments) === true) {
				$componentInstance = $reflectedClass->newInstance();
			} else {
				$componentInstance = $reflectedClass->newInstanceArgs($arguments);
			}
		}
		
		$methods = $component->getMethods();
		
		foreach($methods as $method) {
			$arguments = $this->getParsedArguments($method->getArguments());
			call_user_func_array(array($componentInstance, $method->getMethod()), $arguments);
		}
		
		if($component->isShared()) {
			$this->instances[$componentId] = $componentInstance;
		}

    	return $componentInstance;
    }
    
    
    public function getComponents() {
    	return $this->components;
    }
    
    /**
	* Internal function that injects variable values / object references into arguments
	*
	* @param array $arguments
	* @return array
	*/	
    protected function getParsedArguments($arguments) {		
    		
		foreach($arguments as &$argument) {
			if(is_a($argument, "Dispenser_Element_Reference") === true) 
			{
				$argument = $this->getComponent($argument->getName());
			} else if(is_a($argument, "Dispenser_Element_Variable") === true) {
				$argument = $this->getVariable($argument->getName());
			} 
		}

		return $arguments;
    }
}