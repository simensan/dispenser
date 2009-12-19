<?php 

class BuiltYadif extends Dispenser_Builder { 
	private $Config;

	/**
	* @return Zend_Config_Ini
	*/ 
	public function getConfig() { 
		if($this->Config == null) { 
			$instance = new Zend_Config_Ini('/config/config.ini', true);
			$this->Config = $instance;
		} 

		return $this->Config;
	}


	/**
	* @return Zend_Controller_Request_Http
	*/ 
	public function getRequest() { 
		$instance = new Zend_Controller_Request_Http();
		return $instance;
	}


	/**
	* @return Zend_Controller_Response_Http
	*/ 
	public function getResponse() { 
		$instance = new Zend_Controller_Response_Http();
		return $instance;
	}


	/**
	* @return Zend_Controller_Router_Rewrite
	*/ 
	public function getRouter() { 
		$instance = new Zend_Controller_Router_Rewrite();
		$instance->addRoute('username', $this->getChainRoute()); 
		return $instance;
	}


	/**
	* @return Zend_Controller_Router_Route_Hostname
	*/ 
	public function getRouteHostname() { 
		$instance = new Zend_Controller_Router_Route_Hostname(':_profileUsername.:domain.:tdl');
		return $instance;
	}


	/**
	* @return Zend_Controller_Router_Route
	*/ 
	public function getRouterRoute() { 
		$instance = new Zend_Controller_Router_Route(':controller/:action', array (
  'module' => 'default',
  'controller' => 'profile',
  'action' => 'index',
));
		return $instance;
	}


	/**
	* @return Zend_Controller_Router_Route_Chain
	*/ 
	public function getChainRoute() { 
		$instance = new Zend_Controller_Router_Route_Chain();
		$instance->chain($this->getRouterRoute()); 
		return $instance;
	}
}