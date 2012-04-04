<?php

class IsCurrentPage implements \Zurv\View\Helper {
  public function execute($controller= '', $action = '') {
    $request = \Zurv\Application::getInstance()->getRequest();

    $currentController = strtolower($request->getController());
    $currentAction = strtolower($request->getAction());

    return $controller === $currentController && $action === $currentAction;
  }
}