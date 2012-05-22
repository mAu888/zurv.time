<?php
use \Zurv\View\Adapter\Factory as AdapterFactory;

use \Zurv\Request;
use \Zurv\Response;

class ProjectManageController extends \Zurv\Controller\Base {
  public function addProjectAjaxAction() {
    $this->_view->status = 'success';
    $this->_view->display();
  }

  public function addTrackAjaxAction(Request $request, Response $response) {
    $description = $request->getParameter('description');
    $rate        = (float)$request->getParameter('rate');
    $minutes     = (int)$request->getParameter('minutes');
    $paid        = (bool)$request->getParameter('paid');
    $date        = (string)$request->getParameter('date');
    $projectId   = (int)$request->getParameter('projectId');

    // Convert the date
    $date = DateTime::createFromFormat('d.m.Y', $date);

    $projectsMapper = new ProjectsMapper($this->getApplication()->getRegistry()->db);
    $project = $projectsMapper->findById($projectId);

    $tracksMapper = new TracksMapper($this->getApplication()->getRegistry()->db);
    $track = $tracksMapper->create(array(
      'description' => $description,
      'rate'        => $rate,
      'minutes'     => $minutes,
      'paid'        => $paid,
      'date'        => $date,
      'project'     => $project
    ));

    if($tracksMapper->save($track)) {
      $id = $track->getId();

      $this->_view->status = 'success';
      $this->_view->id = $id;
    }
    else {
      $this->_view->status = 'error';
    }

    $this->_view->display();
  }
}