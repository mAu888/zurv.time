<?php
use \Zurv\View\View;
use \Zurv\View\Adapter\Factory as AdapterFactory;

class TracksTable implements \Zurv\View\Helper {
  protected $_view;

  public function __construct() {
    $viewAdapter = AdapterFactory::create(AdapterFactory::FILE, dirname(__FILE__) . '/views/tracksTable.php');
    $this->_view = new View($viewAdapter);
  }

  public function execute($tracks = null) {
    $this->_view->tracks = $tracks;

    return $this->_view;
  }
}