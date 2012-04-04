<?php
use \Zurv\Application;

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

  public function initRoutes(Application $application) {
    // Behold! Add most specific rotes first!
    $application->getRouter()->addRoutes(
      array(
        '/reports' => array(
          'controller' => 'ReportsOverview',
          'action' => 'index'
        )
      )
    );
    $application->getRouter()->addRoute('/(:controller(?:/(:action)?)?)?', 'Index', 'index');
  }
}