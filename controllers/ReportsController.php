<?php
use \Zurv\Controller\Base as BaseController;

use \Zurv\Request;
use \Zurv\Response;

class ReportsController extends BaseController {
  protected $_template = 'index.php';

  public function indexAction(Request $request, Response $response) {
    $contentView = $this->_loadView('reports/index.php');
    $contentView->title = 'Reports';

    $this->_view->content = $contentView;

    $this->_view->display();
  }

  public function todayAction(Request $request, Response $response) {
    $contentView = $this->_loadView('reports/today.php');
    $contentView->title = 'Heute';

    $this->_view->content = $contentView;

    $this->_view->display();
  }
}