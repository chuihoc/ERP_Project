<?php 
namespace App\Model;

class Plan {
  protected $db;
  public function __construct($db) {
    $this->db = $db;
  }
  public function getPlan() {
    $sql = "SELECT *  FROM plan_of_weeks";
    $data = $this->db->query($sql)->fetchAll(\PDO::FETCH_ASSOC);
    //get product list also 
    $productSQL = "SELECT product_id, name FROM products";
    $productData = $this->db->query($productSQL)->fetchAll(\PDO::FETCH_ASSOC);
    //get info TDV
    $tdvSQL = "SELECT id, name FROM tdv";
    $tdvSQL = $this->db->query($tdvSQL)->fetchAll(\PDO::FETCH_ASSOC);
    return array(
      'plans' => $data,
      'products' => $productData,
      'tdv' => $tdvSQL
    );
    
  }
  public function updatePlan($data) {
    $newPlans = [];
    $updatedCount = [];
    $newPlanCount = [];
    foreach($data as $plan) {
      if(isset($plan['plan_no']) && $plan['plan_no'] != '') {
        //Update mode
        $result = $this->db->update('plan_of_weeks', $plan, ['plan_no' => $plan['plan_no']]);
        if($result->rowCount()) {
          $updatedCount[] = $plan['plan_no'];
        }
      } else {
        //Insert mode
        $newPlans[] = $plan;
      }
    }
    if(!empty($newPlans)) {
      $result = $this->db->insert('quan_ly_ke_hoach', $newPlans);
      $newPlanCount = $result->rowCount();
    }
    return true;
  }
  public function getPlanPerWeek($year, $productId) {
    $where = "WHERE year = $year";
    if($productId != '' && $productId != 'all') {
      $where .= " AND product_id = '{$productId}'";
    }
    $weekSelect = '';
    for($i = 1; $i < 52; $i++) {
      $weekSelect .= "sum(w$i) as 'w{$i}',";
    }
    $sql = "SELECT product_id, 
          year, 
          ". $weekSelect ."
          sum(w52) as 'w52'
          FROM plan_of_weeks ". $where ." GROUP BY year";
    
    $planData = $this->db->query($sql)->fetchAll(\PDO::FETCH_ASSOC);
    $planDataItem = [];
    if(count($planData) == 1) {
      $planDataItem = $planData[0];
    }
    $targetAmount = [];
    for($i = 1; $i <= 52; $i++) {
      $targetAmount[] = isset($planDataItem['w' . $i]) ? $planDataItem['w' . $i] : 0;
    }
    return $targetAmount;
  }
}