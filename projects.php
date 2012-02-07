<?php
require_once 'library/core.php';
require_once 'library/toro.php';

class ProjectHandler extends ToroHandler {
	protected $_db = null;
	
	public function __construct() {
		parent::__construct();
		
		$this->_db = Zurv\Registry::getInstance()->db;
	}
	
	public function get_xhr() {
		$result = $this->_db->query('SELECT * FROM `projects` ORDER BY `name`');
		
		echo json_encode($result->fetchAll());
	}
	
	public function post_xhr() {
		$stmt = $this->_db->prepare('INSERT INTO `projects` (`name`) VALUES(:name)');
		
		$stmt->execute(array(':name' => $_POST['name']));
		
		echo json_encode(array(
			'id' => $this->_db->lastInsertId(),
			'name' => $_POST['name']
		));
	}
	
	public function delete_xhr($id) {
		$stmt = $this->_db->prepare('DELETE FROM `projects` WHERE `id` = :id LIMIT 1');
		
		$stmt->execute(array(':id' => $id));
	}
}