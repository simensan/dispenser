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
 * Representation of a method to be called at object instantiation. 
 *
 */
class Dispenser_Element_Method {
	
	/**
	 * Name of method.
	 * @var string
	 */
	private $method;
	
	/**
	 * Array of arguments to be called with method. 
	 * @var array
	 */
	private $arguments = array();
	
	
	/**
	* Constructor
	*
	* @param string|null $method Name of method
	* @param mixed|array|null $arguments Argument(s) to be called with method
	* @returns Dispenser_Element_Method $this
	*/
	public function __construct($method = null, $arguments = null) {
		$this->setMethod($method);
		$this->setArguments($arguments);
		
		return $this;
	}
	
	
	/**
	* Returns method name
	*
	* @return string
	*/
	public function getMethod() {
		return $this->method;
	}
	
	
	/**
	* Returns arguments called with method
	*
	* @return array
	*/
	public function getArguments() {
		return $this->arguments;
	}
	
	/**
	 * Sets arguments to be called with method
	 * @param mixed|array $arguments
	 * @returns Dispenser_Element_Method $this
	 */
	public function setArguments($arguments) {
		if(is_array($arguments) === false) {
			$arguments = array($arguments);
		}
		
		$this->arguments = $arguments;
		
		return $this;
	}
	
	/**
	 * Sets method name
	 * @param string $method
	 * @returns Dispenser_Element_Method $this
	 */
	public function setMethod($method) {
		$this->method = $method;
		
		return $this;
	}
}