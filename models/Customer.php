<?php

class Customer extends \Zurv\Model\Entity\Base {
  protected $_attributes = array(
    'id' => -1,
    'name' => ''
  );

  public function __toString() {
    return $this->getName();
  }
}