<?php 
namespace App\Model;

class StoreChart extends Base {
  protected $db;
  public function __construct($db) {
    $this->db = $db;
  }
  public function reportTotalStoreByProduct($params) {
    $deliveryId = isset($params['delivery_id']) ? $params['delivery_id'] : '';
    $area = isset($params['area']) ? $params['area'] : '';
    $where = "";
    if($deliveryId != "") {
      $where .= "AND <orders.delivery_id> = '$deliveryId'";
    }
    if($area != "" && $area != "all") {
      $where .= "AND <khuvuc.ma_mien> = '$area'";
    }
    $sql = "SELECT orders.order_id,orders.store_id,count(orders.store_id) as 'tong_nt', orders.delivery_id, orders.product_id,khuvuc.ma_mien FROM orders, products, nha_thuoc, (". self::FULL_LOCATION_SQL .") as khuvuc WHERE orders.product_id = products.product_id AND orders.store_id = nha_thuoc.store_id AND orders.status = 1 AND nha_thuoc.district_id = khuvuc.code $where GROUP BY product_id,delivery_id ORDER BY tong_nt DESC";
    $data = $this->db->query($sql)->fetchAll(\PDO::FETCH_ASSOC);
    if(empty($data)) {
      return array(
        'status' => 'error', 
        'message' => 'Chưa có dữ liệu tổng số nhà thuốc theo sản phẩm'
      );
    }
    $report = array();
    foreach($data as $item) {
      $report['labels'][] = $item['product_id'];
      $report['data'][] = $item['tong_nt'];
    }
    $chartData = array(
      'labels' => $report['labels'],
      'datasets' => array(
        [
          'label' => 'Tổng nhà thuốc',
          'data' => $report['data'],
          'backgroundColor' => 'rgba(54, 162, 235, 1)',
        ]
      ),
    );
    $barChart['data'] = $chartData;
    //Chart options 
    $barChart['options'] = array(
      'maintainAspectRatio' => false,
      'showTooltip' => true,
      'title' => array(
        'display' => true,
		'text' => 'Tổng số nhà thuốc theo sản phẩm '
      ),
      'scales' => [
        'xAxes' => [
          [
            'ticks' => [
              'beginAtZero' => true
            ],
            'maxBarThickness' => 50,
            'scaleLabel' => [
              'display' => false,
              'labelString' => "Biểu đồ doanh thu các năm",
              'fontStyle' => 'bold',
              'fontColor' => '#ccc'
            ]
          ]
        ],
        'yAxes' => [
          [
            'ticks' => [
              'beginAtZero' => true
            ],
            'scaleLabel' => [
              'display' => false,
              'labelString' => 'Sản phẩm',
              'fontStyle' => 'bold',
              'fontColor' => '#ccc'
            ]
          ]
        ]
      ]
    );
    $barChart['legend'] = array(
      'display' => true,
      'usePointStyle' => true
      
    );
    $barChart['width'] = 300;
    //$barChart['height'] = 300;
    return $barChart;
  }
  public function reportTotalStoreByArea($params) {
    $deliveryId = isset($params['delivery_id']) ? $params['delivery_id'] : '';
    $productId = isset($params['product-id']) ? $params['product-id'] : '';
    $where = "";
    if($deliveryId != "") {
      $where .= "AND orders.delivery_id = '$deliveryId'";
    }
    if($productId != "" && $productId != "all") {
      $where .= " AND orders.product_id = '$productId'";
    }
    $sql = "SELECT orders.order_id,orders.store_id,count(orders.store_id) as 'tong_nt', orders.delivery_id, orders.product_id,khuvuc.ma_mien,khuvuc.mien FROM orders, products, nha_thuoc, (". self::FULL_LOCATION_SQL .") as khuvuc WHERE orders.product_id = products.product_id AND orders.store_id = nha_thuoc.store_id AND orders.status = 1 AND nha_thuoc.district_id = khuvuc.code $where GROUP BY ma_mien ORDER BY tong_nt DESC";
    $data = $this->db->query($sql)->fetchAll(\PDO::FETCH_ASSOC);
    if(empty($data)) {
      return array(
        'status' => 'error', 
        'message' => 'Chưa có dữ liệu tổng số nhà thuốc theo miền'
      );
    }
    $report = array();
    foreach($data as $item) {
      $report['labels'][] = $item['mien'];
      $report['data'][] = $item['tong_nt'];
    }
    $chartData = array(
      'labels' => $report['labels'],
      'datasets' => array(
        [
          'label' => 'Tổng nhà thuốc',
          'data' => $report['data'],
          'backgroundColor' => 'rgba(54, 162, 235, 1)',
        ]
      ),
    );
    $barChart['data'] = $chartData;
    //Chart options 
    $barChart['options'] = array(
      'maintainAspectRatio' => false,
      'showTooltip' => true,
      'title' => array(
        'display' => true,
		'text' => 'Tổng số nhà thuốc theo miền'
      ),
      'scales' => [
        'xAxes' => [
          [
            'ticks' => [
              'beginAtZero' => true
            ],
            'maxBarThickness' => 50,
            'scaleLabel' => [
              'display' => false,
              'labelString' => "Biểu đồ doanh thu các năm",
              'fontStyle' => 'bold',
              'fontColor' => '#ccc'
            ]
          ]
        ],
        'yAxes' => [
          [
            'ticks' => [
              'beginAtZero' => true
            ],
            'scaleLabel' => [
              'display' => false,
              'labelString' => 'Sản phẩm',
              'fontStyle' => 'bold',
              'fontColor' => '#ccc'
            ]
          ]
        ]
      ]
    );
    $barChart['legend'] = array(
      'display' => true,
      'usePointStyle' => true
      
    );
    $barChart['width'] = 300;
    //$barChart['height'] = 300;
    return $barChart;
  }
  public function reportTotalStoreByDelivery($params) {
    $sql = "SELECT orders.order_id,orders.store_id,count(orders.store_id) as 'tong_nt', orders.delivery_id, orders.product_id,khuvuc.ma_mien,khuvuc.mien FROM orders, products, nha_thuoc, (". self::FULL_LOCATION_SQL .") as khuvuc WHERE orders.product_id = products.product_id AND orders.store_id = nha_thuoc.store_id AND orders.status = 1 AND nha_thuoc.district_id = khuvuc.code GROUP BY orders.delivery_id ORDER BY tong_nt DESC";
    $data = $this->db->query($sql)->fetchAll(\PDO::FETCH_ASSOC);
    if(empty($data)) {
      return array(
        'status' => 'error', 
        'message' => 'Chưa có dữ liệu tổng số nhà thuốc theo nhà phân phối'
      );
    }
    $report = array();
    foreach($data as $item) {
      $report['labels'][] = $item['delivery_id'];
      $report['data'][] = $item['tong_nt'];
    }
    $chartData = array(
      'labels' => $report['labels'],
      'datasets' => array(
        [
          'label' => 'Tổng nhà thuốc',
          'data' => $report['data'],
          'backgroundColor' => 'rgba(54, 162, 235, 1)',
        ]
      ),
    );
    $barChart['data'] = $chartData;
    //Chart options 
    $barChart['options'] = array(
      'maintainAspectRatio' => false,
      'showTooltip' => true,
      'title' => array(
        'display' => true,
		'text' => 'Tổng số nhà thuốc theo nhà phân phối'
      ),
      'scales' => [
        'xAxes' => [
          [
            'ticks' => [
              'beginAtZero' => true
            ],
            'maxBarThickness' => self::MAX_BAR_THICKNESS,
            'scaleLabel' => [
              'display' => false,
              'labelString' => "Biểu đồ doanh thu các năm",
              'fontStyle' => 'bold',
              'fontColor' => '#ccc'
            ]
          ]
        ],
        'yAxes' => [
          [
            'ticks' => [
              'beginAtZero' => true
            ],
            'scaleLabel' => [
              'display' => false,
              'labelString' => 'Sản phẩm',
              'fontStyle' => 'bold',
              'fontColor' => '#ccc'
            ]
          ]
        ]
      ]
    );
    $barChart['legend'] = array(
      'display' => true,
      'usePointStyle' => true
      
    );
    $barChart['width'] = 300;
    //$barChart['height'] = 300;
    return $barChart;
  }
  public function reportTotalStoreByProvince($params) {
    $area = isset($params['area']) ? $params['area'] : '';
    $title = "Tổng số nhà thuốc các tỉnh miền";
    switch ($area) {
      case 'b':
        $title = "Tổng số nhà thuốc các tỉnh miền bắc";
        break;
      case 't':
        $title = "Tổng số nhà thuốc các tỉnh miền trung";
        break;
      case 'n':
        $title = "Tổng số nhà thuốc các tỉnh miền nam";
        break;
    }
    $where = "AND khuvuc.ma_mien = '$area'";
    $sql = "SELECT orders.store_id,count(orders.store_id) as 'tong_nt', khuvuc.tinh,khuvuc.ma_mien FROM orders, products, nha_thuoc, (". self::FULL_LOCATION_SQL .") as khuvuc WHERE orders.product_id = products.product_id AND orders.store_id = nha_thuoc.store_id AND orders.status = 1 AND nha_thuoc.district_id = khuvuc.code $where GROUP BY tinh ORDER BY tong_nt DESC";
    $data = $this->db->query($sql)->fetchAll(\PDO::FETCH_ASSOC);
    $total_num = count($data);
    if(empty($data)) {
      return array(
        'status' => 'error', 
        'message' => 'Chưa có dữ liệu tổng số nhà thuốc theo miền'
      );
    }
    $report = array();
    foreach($data as $item) {
      $report['labels'][] = $item['tinh'];
      $report['data'][] = $item['tong_nt'];
    }
    $chartData = array(
      'labels' => $report['labels'],
      'type' => 'horizontalBar',
      'datasets' => array(
        [
          'label' => 'Tổng nhà thuốc',
          'data' => $report['data'],
          'backgroundColor' => 'rgba(54, 162, 235, 1)',
        ]
      ),
    );
    $barChart['data'] = $chartData;
    //Chart options 
    $barChart['options'] = array(
      'maintainAspectRatio' => false,
      'showTooltip' => true,
      'title' => array(
        'display' => true,
		'text' => $title
      ),
      'scales' => [
        'xAxes' => [
          [
            'ticks' => [
              'beginAtZero' => true
            ],
            'scaleLabel' => [
              'display' => false,
              'labelString' => "Biểu đồ doanh thu các năm",
              'fontStyle' => 'bold',
              'fontColor' => '#ccc'
            ]
          ]
        ],
        'yAxes' => [
          [
            'ticks' => [
              'beginAtZero' => true
            ],
            'scaleLabel' => [
              'display' => false,
              'labelString' => 'Sản phẩm',
              'fontStyle' => 'bold',
              'fontColor' => '#ccc'
            ]
          ]
        ]
      ]
    );
    $barChart['legend'] = array(
      'display' => true,
      'usePointStyle' => true
      
    );
    //$barChart['width'] = 300;
    $barChart['height'] = 50*$total_num + 120;
    return $barChart;
  }
  public function reportTotalStoreByDistrict($params) {
    $provinceId = isset($params['province-id']) ? $params['province-id'] : '';
    $title = "Tổng số nhà thuốc các huyện trong tỉnh";
    $where = "AND khuvuc.ma_tinh = '$provinceId'";
    $sql = "SELECT orders.store_id,count(orders.store_id) as 'tong_nt', khuvuc.ma_tinh,khuvuc.ma_huyen,khuvuc.huyen FROM orders, products, nha_thuoc, (select district.code, provinces.code as 'ma_tinh', district.code as 'ma_huyen', district.name as 'huyen' from district,provinces,areas WHERE district.parent_code = provinces.code AND areas.area_code = provinces.area_code) as khuvuc WHERE orders.product_id = products.product_id AND orders.store_id = nha_thuoc.store_id AND orders.status = 1 AND nha_thuoc.district_id = khuvuc.code $where GROUP BY khuvuc.code ORDER BY tong_nt DESC";
    $data = $this->db->query($sql)->fetchAll(\PDO::FETCH_ASSOC);
    $total_num = count($data);
    if(empty($data)) {
      return array(
        'status' => 'error', 
        'message' => 'Chưa có dữ liệu tổng số nhà thuốc các huyện trong tỉnh'
      );
    }
    $report = array();
    foreach($data as $item) {
      $report['labels'][] = $item['huyen'];
      $report['data'][] = $item['tong_nt'];
    }
    $chartData = array(
      'labels' => $report['labels'],
      'type' => 'horizontalBar',
      'datasets' => array(
        [
          'label' => 'Tổng nhà thuốc',
          'data' => $report['data'],
          'backgroundColor' => 'rgba(54, 162, 235, 1)',
        ]
      ),
    );
    $barChart['data'] = $chartData;
    //Chart options 
    $barChart['options'] = array(
      'maintainAspectRatio' => false,
      'showTooltip' => true,
      'title' => array(
        'display' => true,
		'text' => $title
      ),
      'scales' => [
        'xAxes' => [
          [
            'ticks' => [
              'beginAtZero' => true
            ],
            'scaleLabel' => [
              'display' => false,
              'labelString' => "Biểu đồ doanh thu các năm",
              'fontStyle' => 'bold',
              'fontColor' => '#ccc'
            ]
          ]
        ],
        'yAxes' => [
          [
            'ticks' => [
              'beginAtZero' => true
            ],
            'scaleLabel' => [
              'display' => false,
              'labelString' => 'Sản phẩm',
              'fontStyle' => 'bold',
              'fontColor' => '#ccc'
            ]
          ]
        ]
      ]
    );
    $barChart['legend'] = array(
      'display' => true,
      'usePointStyle' => true
      
    );
    //$barChart['width'] = 300;
    $barChart['height'] = 50*$total_num + 120;
    return $barChart;
  }
  
}