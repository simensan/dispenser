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
 * Representation of a variable reference. Gets injected from @see Dispenser_Builder into component
 * 
 */
class Dispenser_Element_Variable {
	
	/**
	 * Name of variable reference
	 * @var string
	 */
	private $name;
	
	
	/**
	* Constructor
	*
	* @param string $name Name of variable reference
	*/
	public function __construct($name) {
		$this->name = $name;
	}
	
	
	/**
	* Returns variable reference name
	*
	* @return string
	*/
	public function getName() {
		return $this->name;
	}
}

