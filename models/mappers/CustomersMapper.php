<?php

class CustomersMapper extends \Zurv\Model\Mapper\Base {
  public function fetchAll() {
    $query = $this->_db->query("
      SELECT
        `id`, `name`
      FROM
        `customers`
      ORDER BY
        `name` ASC
    ");

    $customers = array();
    foreach($query->fetchAll(PDO::FETCH_ASSOC) as $customer) {
      array_push($customers, $this->create($customer));
    }

    return $customers;
  } 

  public function findById($id) {
    $stmt = $this->_db->prepare("
      SELECT
        `id`, `name`
      FROM
        `customers`
      WHERE
        `id` = :id
    ");
    $stmt->bindValue(':id', $id, PDO::PARAM_INT);
    $stmt->execute();

    $customer = $stmt->fetch(PDO::FETCH_ASSOC);
    
    return $this->create($customer);
  }

  public function create(array $seed = array()) {
    return new Customer($seed);
  }
}