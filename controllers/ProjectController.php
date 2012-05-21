<?php
use \Zurv\View\Adapter\Factory as AdapterFactory;

class ProjectController extends \Zurv\Controller\Base {
  protected $_template = 'index.php';
  protected $_detectViewAdapter = false;
  protected $_projectsMapper = null;

  public function indexAction() {
    $contentView = $this->_loadView('project/index.php');

    $id = $this->getRequest()->getParameter('id');

    $projectMapper = $this->_getProjectsMapper();
    $project = $projectMapper->findByIdWithTracks($id);

    $contentView->project = $project;

    $this->_view->content = $contentView;
    $this->_view->display();
  }

  public function ajaxAction() {
    $this->_view->setAdapter(AdapterFactory::create(AdapterFactory::JSON));
    $this->_view->so = 'COOL!';

    $this->_view->display();
  }

  protected function _getProjectsMapper() {
    if(is_null($this->_projectsMapper)) {
      $dbAdapter = \Zurv\Application::getInstance()->getRegistry()->db;
      $this->_projectsMapper = new ProjectsMapper($dbAdapter);
    }

    return $this->_projectsMapper;
  }
}