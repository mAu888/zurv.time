<?php

class Track extends \Zurv\Model\Entity\Base {
  protected $_attributes = array(
    'id' => -1,
    'description' => '',
    'date' => null,
    'rate' => 0,
    'minutes' => 0,
    'paid' => false,
    'deleted' => false,
    'project' => null
  );

  public function setProject(Project $project) {
    $this->_attributes['project'] = $project;
  }

  public function setDate($date) {
    if(! $date instanceof DateTime) {
      $date = new DateTime("@{$date}");
    }

    $this->_attributes['date'] = $date;
  }

  public function getDate($format = '') {
    if(! empty($format)) {
      return $this->_attributes['date']->format($format);
    }

    return $this->_attributes['date'];
  }

  public function getDateFormatted() {
    $timestamp = $this->getDate()->getTimestamp();

    return strftime('%a., %e.%b', $timestamp);
  }

  public function isCurrentMonth() {
    $startDate = new DateTime();
    $startDate->modify('first day of next month')->modify('last month')->setTime(0, 0, 10); // 10 second midnight offset
    $endDate = new DateTime();
    $endDate->modify('first day of next month')->setTime(0, 0, 10);

    return $this->getDate() >= $startDate && $this->getDate() < $endDate;
  }

  public function isBetweenDates(DateTime $startDate, DateTime $endDate) {
    return $this->getDate() > $startDate && $this->getDate() < $endDate; 
  }

  public function isSameDay($track) {
    if(is_null($track) || ! $track instanceof Track) {
      return false;
    }

    return $track->getDate('d.m.Y') === $this->getDate('d.m.Y');
  }

  public function __toString() {
    return $this->getName();
  }
}