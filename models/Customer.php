<?php

class Customer extends \Zurv\Model\Entity\Base {
  protected $_attributes = array(
    'id' => -1,
    'name' => '',
    'projects' => array()
  );

  public function addProject(Project $project) {
    if(! in_array($project, $this->getProjects())) {
      array_push($this->_attributes['projects'], $project);
    }
  }

  public function hasProjects() {
    $projects = $this->getProjects();
    
    return is_array($projects) && count($projects) > 0;
  }

  public function __toString() {
    return $this->getName();
  }
}