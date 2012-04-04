<?php

class ProjectList implements \Zurv\View\Helper {
  protected $_projectsMapper;

  public function __construct() {
    $application = \Zurv\Application::getInstance();

    $this->_projectsMapper = new ProjectsMapper($application->getRegistry()->db);
  }

  public function execute() {
    return $this->_projectsMapper->fetchAll();
  }
}