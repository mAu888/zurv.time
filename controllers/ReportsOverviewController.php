<?php
use \Zurv\Controller\Base as BaseController;

use \Zurv\Request;
use \Zurv\Response;

class ReportsOverviewController extends BaseController {
  protected $_template = 'index.php';

  public function indexAction(Request $request, Response $response) {
    $contentView = $this->_loadView('reports/index.php');
    $contentView->title = 'Reports';

    $this->_view->content = $contentView;

    $this->_view->display();
  }
}