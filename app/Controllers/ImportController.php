<?php 
namespace App\Controllers;
use \App\Helper\Data;
use \App\Model\Stores;

class ImportController extends BaseController {
  const MAX_UPLOAD_FILESIZE = 20000000;//20M
  public function index($request, $response) {
    $uri = $request->getUri();
    $data = [
      'base_url' => $uri->getBaseUrl()
    ];
    return $this->view->render($response, 'import.phtml', $data);
  }  
  function upload($request, $response, $args) {
    $resStatus = array(
      'status' => 'error',
      'message' => 'Something went wrong! Can not upload file to server!'
    );
    //Directory of image. Magento will different with Wordpress
    $target_dir = $this->baseDir() . '/resource/upload/';
    $filename = basename($_FILES["filename"]["name"]);
    //Try to upload image without wordpress function
    
    $target_file = $target_dir . $filename;
    $uploadOk = 1;
    $fileExt = pathinfo($target_file,PATHINFO_EXTENSION);
    // Allow certain file formats
    if($fileExt != "xlsx" && $fileExt != "xls") {
        $resStatus['message'] = "Xin lỗi, chỉ cho phép upload file excel có đuôi .xlsx, xls";
        $uploadOk = 0;
    }
    // Check if file is a actual file or fake file
    if(isset($_POST["submit"])) {
        $check = filesize($_FILES["filename"]["tmp_name"]);
        if($check !== false) {
            //echo "File is an image - " . $check["mime"] . ".";
            $uploadOk = 1;
        } else {
            $resStatus['message'] = "File is invalid.";
            $uploadOk = 0;
        }
    }
    // Check if file already exists
    if (file_exists($target_file)) {
        //$resStatus['message'] = "Sorry, file already exists.";
        $filename = time() . '_' . $filename;
        $target_file = $target_dir . $filename;
        //If file already exists then rename the file and allow upload normally
        $uploadOk = 1;
    }
    // Check file size
    if ($_FILES["filename"]["size"] > self::MAX_UPLOAD_FILESIZE) {
        $resStatus['message'] = "Sorry, your file is too large.";
        $uploadOk = 0;
    }
    // Check if $uploadOk is set to 0 by an error
    if ($uploadOk == 1) {
      if (move_uploaded_file($_FILES["filename"]["tmp_name"], $target_file)) {
          //Try to read excel
          try {
            $helper = new \App\Helper\Data();
            $data = $helper->readExcel($target_file);
            if(!empty($data)) {
              //Load district to compare 
              if(!isset($_SESSION['districtList'])) {
                $districtSQL = "SELECT district.code as 'district_id', CONCAT(district.name,'-', provinces.name) as 'title', district.name as 'huyen' FROM district LEFT JOIN provinces ON district.parent_code = provinces.code ORDER BY huyen";
                $districtData = $this->db->query($districtSQL)->fetchAll(\PDO::FETCH_ASSOC);
                $_SESSION['districtList'] = $districtData;
              }
              $districtList = $_SESSION['districtList'];

              $type = $args['type'];
              $importData = [];
              if($type == "tdv") {
                $importData = $this->getTdvData($data, $districtList);
              } else {
                $importData = $this->getOrderData($data, $districtList);
              }
              //Format orders data 
              $resStatus['data'] = $importData;
              $resStatus['status'] = "success";
              $resStatus['message'] = "Upload file thành công!";
              
            }
          }catch(Exception $e) {
            $resStatus['message'] = "Xin lỗi! Server không thể đọc được file excel này: $filename!";
          }
      } else {
          $resStatus['message'] = "Sorry, there was an error uploading your file.";
      }
    }
    $response->getBody()->write(json_encode($resStatus));
    return $response->withHeader('Content-type', 'application/json');
  }
  public function readExcel($path) {
    if(file_exists($path)) {
      $reader = new Xlsx();
      $spreadsheet = $reader->load($path);
      $sheetData = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);
      //Remove title row of excel
      unset($sheetData[1]);
      return $sheetData;
    }
    return [];
  }
  protected function getOrderData($data, $districtList) {
    $orderData = array();
    $compareStoreNameAndAddress = [];
    $helper = new \App\Helper\Data();
    foreach($data as $order) {
      //Find district for this order info 
      $sourceText = isset($order['B']) ? $order['B'] : '';
      //Remove empty row
      if($sourceText == "") continue;
      $district = $helper->findDistrict($sourceText, $districtList);

      $itemArr = array(
        'order_id' => '',
        'store_id' => '',
        'name' => isset($order['A']) ? $order['A'] : '',
        'address' => isset($order['B']) ? $order['B'] : '',
        'product_id' => isset($order['C']) ? $order['C'] : '',
        'delivery_id' => isset($order['D']) ? $order['D'] : '',
        'date' => isset($order['E']) ? $order['E'] : '',
        'qty' => isset($order['F']) ? $order['F'] : '',
        'price' => isset($order['G']) ? $order['G'] : '',
        'unit' => isset($order['H']) ? $order['H'] : '',
        'tdv' => isset($order['I']) ? $order['I'] : '',
        'district_id' => !empty($district) ? $district['district_id'] : '',
        'district_name' => !empty($district) ? $district['huyen'] : '',
        'name_address' => ''
      );

      $nameAndAddress = $itemArr['name'] . $itemArr['address'];
      if($nameAndAddress != "") {
        //mb_strtolower FOR UTF-8 CODE
        $textToLower = mb_strtolower($nameAndAddress, 'UTF-8');
        $textTemp = str_replace(' ', '',$textToLower);
        $compareStoreNameAndAddress[] = "'" . $textTemp . "'";  
        $itemArr['name_address'] = $textTemp;
      }
      $orderData[] = $itemArr;
    }
    //Load all stores exists in database 
    $existsStore = [];
    if(!empty($compareStoreNameAndAddress)) {
      $store = new Stores($this->db);
      $existsStore = $store->checkExistsStores(implode($compareStoreNameAndAddress, ','));
    }
    //If found exists store, then update store_id for that store
    if(!empty($existsStore)) {
      for($i = 0; $i < count($orderData); $i++) {
        foreach($existsStore as $store) {
          if($store['title'] == $orderData[$i]['name_address']) {
            $orderData[$i]['store_id'] = $store['store_id'];
          }
        }
      }
    }
    return $orderData;
  }
  protected function getTdvData($data, $districtList) {
    $tdvData = array();
    $compareStoreNameAndAddress = [];
    $helper = new \App\Helper\Data();
    foreach($data as $order) {
      //Find district for this order info 
      $sourceText = isset($order['C']) ? $order['C'] : '';//Address
      if($sourceText == "") continue;
      $district = $helper->findDistrict($sourceText, $districtList);
      
      $itemArr = array(
        'ma_tdv' => isset($order['A']) ? $order['A'] : '',
        'name' => isset($order['B']) ? $order['B'] : '',
        'address' => isset($order['C']) ? $order['C'] : '',
        'phone' => isset($order['D']) ? $order['D'] : '',
        'company' => isset($order['E']) ? $order['E'] : '',
        'datejoin' => isset($order['F']) ? $order['F'] : '',
        'district_id' => !empty($district) ? $district['district_id'] : '',
        'district_name' => !empty($district) ? $district['huyen'] : '',
        'name_address' => ''
      );
      
      $nameAndAddress = $itemArr['address'];
      if($nameAndAddress != "") {
        //mb_strtolower FOR UTF-8 CODE
        $textToLower = mb_strtolower($nameAndAddress, 'UTF-8');
        $textTemp = str_replace(' ', '',$textToLower);
        $compareStoreNameAndAddress[] = "'" . $textTemp . "'";  
        $itemArr['name_address'] = $textTemp;
      }
      $tdvData[] = $itemArr;
    }
    return $tdvData;
  }
}