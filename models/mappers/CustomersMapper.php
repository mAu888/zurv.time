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

  public function fetchAllWithProjects() {
    $query = $this->_db->query("
      SELECT
        `c`.`id` AS `customer.id`, `c`.`name` AS `customer.name`,
        `p`.`id` AS `project.id`, `p`.`name` AS `project.name`
      FROM
        `projects` `p`
      LEFT JOIN
        `customers` `c` ON `c`.`id` = `p`.`customer_id`
      ORDER BY
        `c`.`name` ASC,
        `p`.`name` ASC
    ");

    $customers = array();
    foreach($query->fetchAll(PDO::FETCH_ASSOC) as $customer) {
      $customerId = $customer['customer.id'];
      $customerName = $customer['customer.name'];

      $projectId = $customer['project.id'];
      $projectName = $customer['project.name'];

      // Create customer object if not existent
      if(! array_key_exists($customerId, $customers)) {
        $customers[$customerId] = $this->create(
          array('id' => $customerId, 'name' => $customerName)
        );
      }

      // Add project if existent
      if(! empty($projectId) && ! empty($projectName)) {
        $customers[$customerId]->addProject(
          new Project(
            array('id' => $projectId, 'name' => $projectName)
          )
        );
      }
    }

    return array_values($customers);
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