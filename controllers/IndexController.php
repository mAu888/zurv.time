<?php
use \Zurv\Controller\Base as BaseController;

use \Zurv\Request;
use \Zurv\Response;
use \Zurv\View\Adapter\Factory as AdapterFactory;

class IndexController extends BaseController {
  protected $_template = 'index.php';
  public function indexAction(Request $request, Response $response) {
    $contentView = $this->_loadView('index/index.php');
    $contentView->title = 'Oh baby mach dich naggisch!';

    $this->_view->content = $contentView;

    $this->_view->display();
  }
}