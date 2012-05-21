<?php

class Project extends \Zurv\Model\Entity\Base {
  const TRACKS_ALL = 1;
  const TRACKS_CURRENTMONTH = 2;

  protected $_attributes = array(
    'id' => -1,
    'name' => '',
    'tracks' => array()
  );

  public function addTrack(Track $track) {
    if(! in_array($track, $this->getTracks())) {
      array_push($this->_attributes['tracks'], $track);
    }
  }

  public function hasTracks() {
    $tracks = $this->getTracks();

    return is_array($tracks) && count($tracks) > 0;
  }

  public function getTracks($filter = null) {
    $tracks = $this->_attributes['tracks'];

    if(! is_null($filter)) {
      switch($filter) {
        case self::TRACKS_CURRENTMONTH:
          $startDate = new DateTime();
          $startDate->modify('first day of next month')->modify('last month')->setTime(0, 0, 10); // 10 second midnight offset
          $endDate = new DateTime();
          $endDate->modify('first day of next month')->setTime(0, 0, 10);
          
          for($i = count($tracks) - 1; $i > 0; $i--) {
            if(! $tracks[$i]->isBetweenDates($startDate, $endDate)) {
              unset($tracks[$i]);
            }
          }
          break;
        case self::TRACKS_ALL:
        default:
          break;
      }
    }

    return $tracks;
  }

  public function __toString() {
    return $this->getName();
  }
}