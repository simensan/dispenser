<?php

class MockClass {
	
	private $arg;
	
	public function __construct($arg = "defaultval") {
		$this->arg = $arg;
	}
	
	public function getArgument() {
		return $this->arg;
	}

	
	public function setArgument($arg) {
		$this->arg = $arg;
	}
}

class AnotherMockClass {
	
	
}


class FactoryClass {	
	public static function getFactory($argument = "") {
		return new FactoryReturnedClass($argument);
	}
	
	
}

class FactoryReturnedClass {
	private $argument;
	
	public function __construct($argument) {
		$this->argument = $argument;
	}
	
	public function getArgument() {
		return $this->argument;
	}
}