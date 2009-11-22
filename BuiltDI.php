<?php 

class BuiltDI extends Dispenser_Builder { 
	private $RefClass2;
	private $RefClass;
	private $Test;

	/**
	* @return FactoryReturnedClass
	*/ 
	public function getRenderer() { 
		$instance = FactoryClass::getFactory('viewRenderer');
		return $instance;
	}


	/**
	* @return PassByRefClass
	*/ 
	public function getRefClass2() { 
		if($this->RefClass2 == null) { 
			$instance = new PassByRefClass();
			$this->RefClass2 = $instance;
		} 

		return $this->RefClass2;
	}


	/**
	* @return PassByRefClass
	*/ 
	public function getRefClass() { 
		if($this->RefClass == null) { 
			$instance = new PassByRefClass();
			$this->RefClass = $instance;
		} 

		return $this->RefClass;
	}


	/**
	* @return Test
	*/ 
	public function getTest() { 
		if($this->Test == null) { 
			$instance = new Test('blah');
			$instance->setSomeShit($this->getRefClass()); 
			$instance->setSomeOtherShit($this->getVariable("test.config")); 
			$this->Test = $instance;
		} 

		return $this->Test;
	}
}