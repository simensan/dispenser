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
* @package Dispenser_Element
* @author Simen Sandvær <simen.is@gmail.com>
* @copyright Simen Sandvær <simen.is@gmail.com>. All rights reserved.
* @license MIT License
*/


/**
 * Loose representation of a component. Used by @see Dispeser_Builder 
 *
 */
class Dispenser_Element_Component {
	
	/**
	 * Id of component, i.e. "Mock"
	 * @var string
	 */
	private $id;
	
	/**
	 * Class name of component
	 * @var string
	 */
	private $class;

	/**
	 * Wether or not the instance of component is shared or not. 
	 * @var boolean
	 */
	private $shared = false;
	
	/**
	 * Array of arguments to constructor of component
	 * @var array
	 */
	private $arguments = array();
	
	/**
	 * Methods to be called on instantiation of component
	 * @var array
	 */
	private $methods = array();
	
	/**
	 * Reference to factory object.
	 * @var Dispenser_Element_Factory
	 */
	private $factory = null;

	
	/**
	* Constructor
	*
	* @param id|null $id Id of component. @see setId
	*/
	public function __construct($id = null) {
		$this->setId($id);	
	}
	
	
	/**
	* Get id of component
	*
	* @return string
	*/
	public function getId() {
		return $this->id;
	}
	
	
	/**
	* Sets id of component
	*
	* @param string $id Id of component.
	* @return Dispenser_Element_Component
	*/
	public function setId($id) {
		$this->id = $id;
		
		return $this;
	}
	
	/**
	* Sets class name of component
	*
	* @param string $class
	* @return Dispenser_Element_Component
	*/
	public function setClass($class) {
		$this->class = $class;
		
		return $this;
	}
	
	
	/**
	* Get class name of component
	*
	* @return string
	*/
	public function getClass() {
		return $this->class;
	}
	
	
	/**
	* Add argument to be called with object constructor. 
	*
	* @param Dispenser_Element_Reference|Dispenser_Element_Variable|object $argument
	* @return Dispenser_Element_Component
	*/
	public function addArgument($argument) {
		$this->arguments[] = $argument;	
		
		return $this;
	}
	
	
	/**
	* Add arguments to be called with object constructor. 
	*
	* @param array $array Array of arguments
	* @return Dispenser_Element_Component
	*/
	public function addArguments($arguments) {
		foreach($arguments as $argument) {
			$this->addArgument($argument);
		}
		
		return $this;
	}
	
	
	/**
	* Get arguments called with component constructor.
	*
	* @return array
	*/
	public function getArguments() {
		return $this->arguments;
	}
	
	
	/**
	* Add method to be called after object instantiation. 
	*
	* @param Dispenser_Element_Method $method
	*/
	public function addMethod(Dispenser_Element_Method $method) {
		$this->methods[] = $method;
		
		return $this;
	}
	
	
	/**
	* Sets array to import, alternative to passing in constructor
	*
	* @param array $array Array of methods
	* @return Dispenser_Element_Component
	*/
	public function addMethods($methods) {
		foreach($methods as $method) {
			$this->addMethod($method);
		}
		
		return $this;
	}
	
	
	/**
	* Get array of methods called after object instantiation. 
	*
	* @return array
	*/
	public function getMethods() {
		return $this->methods;
	}
	
	
	/**
	* Sets the factory to be used to get instance of object. 
	*
	* @param Dispenser_Element_Factory $factory
	* @return Dispenser_Element_Component
	*/
	public function setFactory(Dispenser_Element_Factory $factory) {
		$this->factory = $factory;
		
		return $this;
	}
	
	
	/**
	* Gets factory. 
	*
	* @return Dispenser_Element_Factory
	*/
	public function getFactory() {
		return $this->factory;
	}
	
	
	/**
	* Returns whether or not component uses factory instantiation
	*
	* @return boolean
	*/
	public function hasFactory() {
		return ($this->factory === null)?false:true;
	}
	
	
	/**
	* Sets whether the instance of the component is shared or not.
	*  If set to false a new instance is returned each getComponent call. 
	*
	* @param boolean $shared
	* @return Dispenser_Element_Component
	*/
	public function setShared($shared) {
		$this->shared = (boolean)$shared;
		
		return $this;
	}
	
	
	/**
	* Returns true if component instance is shared. 
	*
	* @return boolean
	*/
	public function isShared() {
		return $this->shared;
	}
}
