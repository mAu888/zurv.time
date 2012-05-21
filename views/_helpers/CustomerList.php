<?php

class CustomerList implements \Zurv\View\Helper {
  protected $_customersMapper;

  public function __construct() {
    $application = \Zurv\Application::getInstance();

    $this->_customersMapper = new CustomersMapper($application->getRegistry()->db);
  }

  public function execute() {
    $a = $this->_customersMapper->fetchAllWithProjects();
    
    return $a;
  }
}