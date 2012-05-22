<?php

class TracksMapper extends \Zurv\Model\Mapper\Base {
  public function fetchAll() {
    $query = $this->_db->query("
      SELECT
        `id`, `description`, UNIX_TIMESTAMP(`date`) AS `date`, `rate`, `minutes`, `paid`, `deleted`
      FROM
        `tracks`
      ORDER BY
        `date` DESC
    ");

    $tracks = array();
    foreach($query->fetchAll(PDO::FETCH_ASSOC) as $track) {
      array_push($tracks, $this->create($track));
    }

    return $tracks;
  } 

  public function fetchAllByProject(Project $project) {
    $stmt = $this->_db->prepare("
      SELECT
        `id`, `description`, UNIX_TIMESTAMP(`date`) AS `date`, `rate`, `minutes`, `paid`, `deleted`
      FROM
        `tracks`
      WHERE
        `project_id` = :projectId
      ORDER BY
        `date` DESC
    ");

    $stmt->bindValue(':projectId', $project->getId(), PDO::PARAM_INT);
    $stmt->execute();

    $tracks = array();
    foreach($stmt->fetchAll(PDO::FETCH_ASSOC) as $track) {
      array_push($tracks, $this->create($track));
    }

    return $tracks;
  }

  public function save(Track $track) {
    $id = $track->getId();

    if($id === -1) {
      return $this->insert($track);
    }
    else {
      return $this->update($track);
    }
  }

  public function insert(Track $track) {
    $stmt = $this->_db->prepare("
      INSERT INTO
        `tracks` (
          `description`, `date`, `rate`, `minutes`, `paid`, `project_id`
        ) VALUES (
          :description, :date, :rate, :minutes, :paid, :project_id
        )
    ");

    $stmt->bindValue(':description', $track->getDescription());
    $stmt->bindValue(':date', $track->getDate()->format(self::DATE_TIME_FORMAT));
    $stmt->bindValue(':rate', $track->getRate());
    $stmt->bindValue(':minutes', $track->getMinutes());
    $stmt->bindValue(':paid', $track->getPaid(), PDO::PARAM_BOOL);
    $stmt->bindValue(':project_id', $track->getProject()->getId(), PDO::PARAM_INT);

    $status = $stmt->execute();
    if($status === true) {
      $track->setId($this->_db->lastInsertId());
    }
    
    return $status;
  }

  public function update(Track $track) {
    $stmt = $this->_db->prepare("
      UPDATE
        `tracks`
      SET
        `description` = :description,
        `date` = :date,
        `rate` = :rate,
        `minutes` = :minutes,
        `paid` = :paid,
        `project_id` = :project_id
      WHERE
        `id` = :id
      LIMIT 1
    ");

    $stmt->bindValue(':description', $track->getDescription());
    $stmt->bindValue(':date', $track->getDate()->format(self::DATE_TIME_FORMAT));
    $stmt->bindValue(':rate', $track->getRate());
    $stmt->bindValue(':minutes', $track->getMinutes());
    $stmt->bindValue(':paid', $track->getPaid(), PDO::PARAM_BOOL);
    $stmt->bindValue(':project', $track->getProject()->getId(), PDO::PARAM_INT);
    $stmt->bindValue(':id', $track->getId(), PDO::PARAM_INT);

    return $stmt->execute();
  }

  public function create(array $seed = array()) {
    return new Track($seed);
  }
}