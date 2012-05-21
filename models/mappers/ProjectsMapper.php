<?php

class ProjectsMapper extends \Zurv\Model\Mapper\Base {
  protected $_tracksMapper;

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

  public function findById($id) {
    $stmt = $this->_db->prepare("
      SELECT
        `id`, `name`
      FROM
        `projects`
      WHERE
        `id` = :id
    ");
    $stmt->bindValue(':id', $id, PDO::PARAM_INT);
    $stmt->execute();

    $project = $stmt->fetch(PDO::FETCH_ASSOC);
    
    return $this->create($project);
  }

  public function findByIdWithTracks($id) {
    $project = $this->findById($id);

    $tracksMapper = $this->_getTracksMapper();
    $tracks = $tracksMapper->fetchAllByProject($project);

    $project->setTracks($tracks);

    return $project;
  }

  public function create(array $seed = array()) {
    return new Project($seed);
  }

  protected function _getTracksMapper() {
    if(is_null($this->_tracksMapper)) {
      $this->_tracksMapper= new TracksMapper($this->_db);
    }

    return $this->_tracksMapper;
  }
}