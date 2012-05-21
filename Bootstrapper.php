<?php
use \Zurv\Application;
use \Zurv\Router\Route;

class Bootstrapper extends \Zurv\Bootstrapper\Base {
  public function initDatabase(Application $application) {
    $registry = $application->getRegistry();

    $registry->db = new PDO('mysql:host=127.0.0.1;port=8889;dbname=timetracker', 'root', 'root');
    $registry->db->query('SET NAMES "utf8"');
  }

  public function initAutoloaders(Application $application) {
    $applicationPath = $application->getPath();

    $paths = array(
      $applicationPath . 'models/',
      $applicationPath . 'models/mappers/',
      $applicationPath . 'views/_helpers/'
    );
    spl_autoload_register(function($class) use ($paths) {
      $class = strtoupper($class);
      foreach($paths as $path) {
        if(file_exists($path . $class . '.php')) {
          require_once $path . $class . '.php';
          break;
        }
      }
    });
  }

  public function initLanguage(Application $application) {
    if(! setlocale(LC_ALL, 'de_DE.utf8')) {
      setlocale(LC_ALL, 'de_DE');
    }
  }

  public function initRoutes(Application $application) {
    $router = $application->getRouter();

    // Behold! Add most specific rotes first!
    // As ajax routes are more specific, because they
    // match only on xmlhttprequest header, add them first
    $router->addRoutes(
      array(
        '/project' => array(
          'controller' => 'ProjectManage',
          'action' => 'addProjectAjax',
          'isAjax' => true,
          'requestTypes' => array(Route::POST)
        ),
        '/track' => array(
          'controller' => 'ProjectManage',
          'action' => 'addTrackAjax',
          'isAjax' => true,
          'requestTypes' => array(Route::POST)
        ),
        '/reports(?:/(:action)?)?' => array(
          'controller' => 'Reports',
          'action' => 'index'
        ),
        '/project(?:/(?P<id>[1-9]+[0-9]*))?' => array(
          'controller' => 'Project',
          'action' => 'index',
          'id' => 99
        ),
        '/(:controller(?:/(:action)?)?)?' => array(
          'controller' => 'Index',
          'action' => 'index'
        )
      )
    );
  }
}