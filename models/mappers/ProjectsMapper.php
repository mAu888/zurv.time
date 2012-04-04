<?php

class ProjectsMapper extends \Zurv\Model\Mapper\Base {
  public function fetchAll() {
    $query = $this->_db->query("
      SELECT
        `id`, `name`
      FROM
        `projects`
      ORDER BY
        `name` ASC
    ");

    $projects = array();
    foreach($query->fetchAll(PDO::FETCH_ASSOC) as $project) {
      array_push($projects, $this->create($project));
    }

    return $projects;
  } 

  public function create(array $seed = array()) {
    return new Project($seed);
  }
}