<?php
require_once 'library/toro.php';
require_once 'library/core.php';

class TracksHandler extends BaseHandler {
	public function get_xhr($projectId = -1) {
		
		if($projectId > 0) {
			$stmt = $this->_db->prepare('SELECT *, `project_id` AS `project` FROM `tracks` WHERE `project_id` = :project ORDER BY `date` DESC');
			$stmt->execute(array(':project' => $projectId));
		}
		else {
			$stmt = $this->_db->query('SELECT *, `project_id` AS `project`, UNIX_TIMESTAMP(`date`) * 1000 AS `date` FROM `tracks` WHERE `deleted` = 0 ORDER BY `date` DESC');
		}
		
		echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
	}
	
	public function post_xhr() {
		$stmt = $this->_db->prepare('INSERT INTO `tracks` (`description`, `date`, `rate`, `minutes`, `project_id`) VALUES(:description, :date, :rate, :minutes, :project)');
		
		$stmt->execute(array(
			':description' => $_POST['description'],
			':date' => date('Y-m-d H:i:s', $_POST['date']/1000),
			':rate' => $_POST['rate'],
			':minutes' => $_POST['minutes'],
			':project' => $_POST['project']
		));
		
		
		echo json_encode(array(
			'id' => $this->_db->lastInsertId(),
			'description' => $_POST['description'],
			'date' => $_POST['date'],
			'rate' => $_POST['rate'],
			'minutes' => $_POST['minutes'],
			'project' => $_POST['project'],
			'paid' => false
			
		));
	}
}

class TrackHandler extends BaseHandler {
	public function put_xhr($id) {
		$stmt = $this->_db->prepare('UPDATE `tracks` SET `description` = :description, `date` = :date, `rate` = :rate, `minutes` = :minutes, `paid` = :paid WHERE `id` = :id');
		
		$stmt->execute(array(
			':description' => $_POST['description'],
			':date' => date('Y-m-d H:i:s', $_POST['date']/1000),
			':rate' => $_POST['rate'],
			':minutes' => $_POST['minutes'],
			':id' => $_POST['id'],
			':paid' => $_POST['paid'] ? 1 : 0
		));
		
		echo json_encode(array(
			'id' => $_POST['id'],
			'description' => $_POST['description'],
			'date' => $_POST['date'],
			'rate' => $_POST['rate'],
			'minutes' => $_POST['minutes'],
			'project' => $_POST['project'],
			'paid' => $_POST['paid']
		));
	}
	
	public function delete_xhr($id) {
		$stmt = $this->_db->prepare('UPDATE `tracks` SET `deleted` = 1 WHERE `id` = :id LIMIT 1');
		
		$stmt->execute(array(':id' => $id));
	}
}