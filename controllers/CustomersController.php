<?php
use \Zurv\Controller\Base as BaseController;

use \Zurv\Request;
use \Zurv\Response;
use \Zurv\View\Adapter\Factory as AdapterFactory;

class CustomersController extends BaseController {
  protected $_template = 'index.php';

  protected $_customersMapper = null;

  public function indexAction(Request $request, Response $response) {
    $contentView = $this->_loadView('customers/index.php');
    
    $customersMapper = $this->_getCustomersMapper();
    $customers = $customersMapper->fetchAll();

    $contentView->customers = $customers;

    $this->_view->content = $contentView;

    $this->_view->display();
  }

  protected function _getCustomersMapper() {
    if(is_null($this->_customersMapper)) {
      $dbAdapter = \Zurv\Application::getInstance()->getRegistry()->db;
      $this->_customersMapper = new CustomersMapper($dbAdapter);
    }

    return $this->_customersMapper;
  }
}