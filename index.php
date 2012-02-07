<?php
require_once 'library/core.php';
require_once 'library/toro.php';

require_once 'config.php';

class BaseHandler extends ToroHandler {
	protected $_db = null;

	public function __construct() {
		parent::__construct();

		$this->_db = Zurv\Registry::getInstance()->db;
	}
}

require_once 'projects.php';
require_once 'tracks.php';

class AppHandler extends ToroHandler {
	public function get() {
		echo file_get_contents('index.html');
	}
}

ToroHook::add('before_request', function() {
	$input = json_decode(file_get_contents('php://input'), true);
	switch(strtolower($_SERVER['REQUEST_METHOD'])) {
		case 'get': $_GET = $input; break;
		case 'post': $_POST = $input; break;
		case 'put': $_POST = $input; break;
		case 'delete': $_POST = $input; break;
		default: throw new Exception('Invalid request type'); break;
	}
	
});

ToroHook::add('after_request', function() {
	Zurv\Registry::getInstance()->db = null;
});

$site = new ToroApplication(array(
	array('/', 'AppHandler'),
	array('/projects/([1-9][0-9]*)/tracks', 'TracksHandler'),
	array('/projects/([1-9][0-9]*)', 'ProjectHandler'),
	array('/projects', 'ProjectHandler'),
	array('/tracks/([1-9][0-9]*)', 'TrackHandler'),
	array('/tracks', 'TracksHandler')
));

$site->serve();