<?php
spl_autoload_register(function($class) {
  $class = str_replace('\\', '/', $class);

  $dirs = array('../', '../controllers/');
  foreach($dirs as $dir) {
    if(file_exists($dir . $class . '.php')) {
      require_once $dir . $class . '.php';
      break;
    }
  }
});

require_once '../library/zurv.core/Zurv/Application.php';

$app = \Zurv\Application::getInstance(
  array(
    'applicationPath' => realpath(dirname(__FILE__) . '/..') . '/',
    'bootstrapperClass' => '\Bootstrapper',
    'libraryPath' => realpath(dirname(__FILE__) . '/..') . '/library/zurv.core/'
  )
);

$app->run();