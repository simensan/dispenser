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
 * Loads variables from an array. 
 * 
 */
class Dispenser_Element_Factory {
	
	/**
	 * Factory class
	 * @var string
	 */
	private $class;
	
	/**
	 * Factory method 
	 * @var string
	 */
	private $method;
	
	
	/**
	* Constructor
	*
	* @param string|null $class Factory class
	* @param string|null $method Factory method
	*/
	public function __construct($class = null, $method = null) {
		$this->class = $class;
		$this->method = $method;
	}
	
	
	/**
	* Gets the factory method
	*
	* @return string Factory method
	*/
	public function getMethod() {
		return $this->method;
	}
	
	
	/**
	* Gets the factory class
	*
	* @return string Factory class
	*/
	public function getClass() {
		return $this->class;
	}

	
	/**
	* Sets the factory class
	*
	* @param string $class Factory class
	*/
	public function setClass($class) {
		$this->class = $class;
		
		return $this;
	}
	
	
	/**
	* Sets the factory method
	*
	* @param string $method Factory method
	*/
	public function setMethod($method) {
		$this->method = $method;
		
		return $this;
	}
}