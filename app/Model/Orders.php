<?php 
namespace App\Model;
use \App\Helper\Data;

class Orders {
  protected $db;
  protected $isSuper;
  protected $userId;
  const STORE_PREFIX = 'NT';
  const STORE_STARTER_NUM = 1000;
  public function __construct($db, $isSuper = false, $userId = '') {
    $this->db = $db;
    $this->isSuper = $isSuper;
    $this->userId = $userId;
  }
  public function getOrders() {
    $where = "status = 1";
    if(!$this->isSuper) {
      $where .= " AND orders.create_by = '{$this->userId}' ";
    }
    $sql = "SELECT (order_id * 1) AS order_id, store_id, product_id, qty, price, date, unit, delivery_id,tdv  FROM orders WHERE $where ORDER BY date DESC";
    $data = $this->db->query($sql)->fetchAll(\PDO::FETCH_ASSOC);
    return $data;
  }
  public function addOrders($data, $userId = '') {
    $response = array(
      'status' => 'error',
      'message' => 'Có lỗi trong quá trình nhập đơn hàng'
    );
    $helper = new Data();
    $isOk = true;
    $doneCreateStore = false;
    $date = new \DateTime();
    $datetime = $date->format('Y-m-d H:i:s');
   
    //Get next store ID number 
    $nextStoreId = $this->getNextStoreId();
    $newStoreIdList = array();
    //New store arr data 
    $newStores = [];
    $newOrders = [];
    for($i = 0; $i < count($data); $i++) {
      if(isset($data[$i]['order_id']) && $data[$i]['order_id'] != '') {
        //Edit order
        //Convert date to MYSQL date format 
        $data[$i]['date'] = $helper->convertStringToDate('d/m/Y', $data[$i]['date']);
        $result = $this->db->update('orders', [
          "store_id" => $data[$i]['store_id'],
          "date" => $data[$i]['date'],
          "product_id" => $data[$i]['product_id'],
          "delivery_id" => $data[$i]['delivery_id'],
          "qty" => $data[$i]['qty'],
          "price" => $data[$i]['price'],
          "unit" => $data[$i]['unit'],
          "tdv" => $data[$i]['tdv'],
          "update_on" => $datetime,
          "update_by" => $userId
        ], ['order_id' => $data[$i]['order_id']]);
        if($result->rowCount()) {
          $response['status'] = 'success';
          $response['message'] = 'Cập nhật đơn hàng thành công!';
        }
      } else {
        //New order mode
        if(!isset($data[$i]['product_id']) || $data[$i]['product_id'] == ''
          || !isset($data[$i]['date']) || $data[$i]['date'] == '') {
          $isOk = false;
          $response['message'] = 'Đơn hàng không có mã sản phẩm hoặc không có ngày đặt hàng!';
          return false;
        }
        if($data[$i]['store_id'] == '') {
          if($nextStoreId == "") {
            $response['message'] = "Không tìm thấy mã nhà thuốc tiếp theo!";
            return $response;
          }
          //Du lieu tu file excel co the bi trung, gop cac ban ghi bi trung va tao chung id
          $nameAddressKey = strtolower($data[$i]['name'] . $data[$i]['address']);
          if(!array_key_exists($nameAddressKey, $newStoreIdList)) {
            $newStoreId = self::STORE_PREFIX . $nextStoreId;
            $newStoreIdList[$nameAddressKey] = $newStoreId;
            $newStores[] = [
              "store_id" => $newStoreId,
              "name" => $data[$i]['name'],
              "address" => $data[$i]['address'],
              "district_id" => $data[$i]['district_id'],
              'create_on' => $datetime
            ];
            $data[$i]['store_id'] = $newStoreId;
            $response['nha_thuoc_moi'][] = $newStoreId;
            $nextStoreId += 1;
          } else {
            $data[$i]['store_id'] = $newStoreIdList[$nameAddressKey];
          }
          //Increment last store number
        }
        //Convert date to MYSQL date format 
        $data[$i]['date'] = $helper->convertStringToDate('d/m/Y', $data[$i]['date']);
        $newOrders[] = [
          "store_id" => $data[$i]['store_id'],
          "date" => $data[$i]['date'],
          "product_id" => $data[$i]['product_id'],
          "delivery_id" => $data[$i]['delivery_id'],
          "qty" => $data[$i]['qty'],
          "price" => $data[$i]['price'],
          "unit" => $data[$i]['unit'],
          "tdv" => $data[$i]['tdv'],
          "create_on" => $datetime,
          "create_by" => $userId
        ];
      }
    }
    if($isOk) {
      //If there are any new store, then create stores first 
      if(!empty($newStores)) {
        $result = $this->db->insert('nha_thuoc', $newStores);
        //update $lastStoreNumber
        if($result->rowCount()) {
          $doneCreateStore = true;
        }
      } else {
        $doneCreateStore = true;
      }
      if(!empty($newOrders) && $doneCreateStore) {
        $result = $this->db->insert('orders', $newOrders);
        $response['message'] = "Đã nhập thành công {$result->rowCount()} đơn hàng!";
        $response['status'] = "success";
        return $response;
      }
    }
    return $response;
  }
  public function getNextStoreId() {
    $where = "";//For test
    $sql = "SELECT (MAX(REPLACE(store_id,'NT','')) + 1) as nextId FROM `nha_thuoc` $where";
    $nextStoreData = $this->db->query($sql)->fetchAll(\PDO::FETCH_ASSOC);
    $nextStoreId = "";
    if(!empty($nextStoreData)) {
      $nextStoreId = isset($nextStoreData[0]['nextId']) ? $nextStoreData[0]['nextId'] : '';
      if($nextStoreId == "") {
        //Check store has data or not 
        $storeDataSQL = "SELECT COUNT(store_id) as storeNum FROM nha_thuoc $where";
        $storeData = $this->db->query($storeDataSQL)->fetchAll(\PDO::FETCH_ASSOC);
        if(isset($storeData[0]['storeNum']) && $storeData[0]['storeNum'] == 0) {
          $nextStoreId = self::STORE_PREFIX . self::STORE_STARTER_NUM;//Start from 1000, for beauty ID purpose 
        }
      }
      //Check store exist 
      $storeExists = $this->db->select('nha_thuoc', ['store_id'], ['store_id' => $nextStoreId]);
      if(empty($storeExists)) {
       return $nextStoreId;
      }
    }
    return $nextStoreId;
  }
  public function deleteOrder($orderId) {
    $result = $this->db->delete('orders', [
      'order_id' => $orderId
    ]);
    return $result->rowCount();
  }
  public function getPreOrderData() {
    $productSQL = "SELECT product_id, name FROM products";
    $storeSQL = "SELECT store_id, CONCAT(store_id, '-', name) as 'title', address, district_id, name FROM nha_thuoc";
    $deliverySQL = "SELECT delivery_id,name FROM delivery";
    //Load district to compare 
    if(!isset($_SESSION['districtList'])) {
      $districtSQL = "SELECT district.code as 'district_id', CONCAT(district.name,'-', provinces.name) as 'title', district.name as 'huyen' FROM district LEFT JOIN provinces ON district.parent_code = provinces.code ORDER BY huyen";
      $districtData = $this->db->query($districtSQL)->fetchAll(\PDO::FETCH_ASSOC);
      $_SESSION['districtList'] = $districtData;
    }
    $districtList = $_SESSION['districtList'];
    $productData = $this->db->query($productSQL)->fetchAll(\PDO::FETCH_ASSOC);
    $storeData = $this->db->query($storeSQL)->fetchAll(\PDO::FETCH_ASSOC);
    $deliveryData = $this->db->query($deliverySQL)->fetchAll(\PDO::FETCH_ASSOC);
    //$districtData = $this->db->query($districtSQL)->fetchAll(\PDO::FETCH_ASSOC);
    return array(
      'products' => $productData,
      'stores' => $storeData,
      'deliveries' => $deliveryData,
      'districts' => $districtList
    );
  }
}