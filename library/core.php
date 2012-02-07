<?php
namespace Zurv;

class Registry {
	private static $_instance = null;
	
	private $_data = array();
	
	private final function __construct() {}
	private final function __clone() {}
	
	public static function getInstance() {
		if(self::$_instance === null) {
			self::$_instance = new self();
		}
		
		return self::$_instance;
	}
	
	public function __get($key) {
		if(isset($this->_data[$key])) {
			return $this->_data[$key];
		}
		
		return null;
	}
	
	public function __set($key, $value) {
		$this->_data[$key] = $value;
	}
}