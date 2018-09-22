<?php 
namespace App\Model;

class ProvinceDetailsChart extends Base {
  protected $db;
  protected $isSuper;
  protected $userId;
  public function __construct($db, $isSuper = false, $userId = '') {
    $this->db = $db;
    $this->isSuper = $isSuper;
    $this->userId = $userId;
  }
  protected function getDataByUser() {
    $where = self::FILTER_BY_EXCHANGE;
    if(!$this->isSuper) {
      $where .= " AND orders.create_by = '{$this->userId}' ";
    }
    return $where;
  }
  protected function getLocationSpecific($params) {
    $productId = isset($params['product-id']) ? $params['product-id'] : '';
    $provinceId = isset($params['province-id']) ? $params['province-id'] : '';
    $year = isset($params['year']) ? $params['year'] : '';
    $where = '';
    if($year != 'provinces_all_year') {
      $where .= "AND YEAR(date) = $year";
    }
    if($productId != "" && $productId != "all") {
      $where .= " AND <orders.product_id> = '$productId'";
    }
    if($provinceId != "") {
      $where .= " AND <khuvuc.ma_tinh> = '$provinceId'";
    }
    $location = 'tỉnh';
    if(isset($params['location-specific'])) {
      switch ($params['location-specific']) {
        case 'district':
          if(isset($params['district']) && $params['district'] != "") {
            $where .= " AND <khuvuc.code> = '{$params['district']}'";
          }
          $data = $this->db->select('district', ['name'], ['code' => $params['district']]);
          if(!empty($data)) {
            $location = $data[0]['name'];
          }
          break;
        case 'store':
          if(isset($params['store-id']) && $params['store-id'] != "") {
            $where .= " AND <orders.store_id> = '{$params['store-id']}'";
          }
          $data = $this->db->select('nha_thuoc', ['name'], ['store_id' => $params['store-id']]);
          if(!empty($data)) {
            $location = $data[0]['name'];
          }
          break;
      }
    }
    return [
      'location' => $location,
      'where' => $where
    ];
  }
  public function reportByProvinceYear($params) {
    $title = "Doanh số tỉnh theo các năm";
    $where = $this->getDataByUser();
    $extraQuery = $this->getLocationSpecific($params);
    if($extraQuery['where'] != '') {
      $where .= $extraQuery['where'];
    }
    if($extraQuery['location'] != '') {
      $title = "Doanh số " . $extraQuery['location'] . " theo các năm";
    }
    $sql = "SELECT tinh,mien, ". $this->getSum() .", YEAR(date) as 'nam' from (". self::FULL_LOCATION_SQL .") as khuvuc, orders,nha_thuoc,exchange WHERE orders.store_id = nha_thuoc.store_id AND orders.status = 1 AND nha_thuoc.district_id = khuvuc.code ". $where ." GROUP BY nam ORDER BY doanhso DESC";
      
    $data = $this->db->query($sql)->fetchAll(\PDO::FETCH_ASSOC);
    if(empty($data)) {
      return array(
        'status' => 'error', 
        'message' => 'Không có dữ liệu!'
      );
    }
    $report = array();
    foreach($data as $item) {
      $report['labels'][] = $item['nam'];
      $report['data'][] = $item['doanhso'];
      $report['doanhthu'][$item['doanhso']] = $item['doanhthu'];
    }
    $total_num = count($data);
    $chartData = array(
      'labels' => $report['labels'],
      'doanhthu' => $report["doanhthu"],
      'datasets' => array(
        [
          'label' => 'Doanh số',
          'data' => $report['data'],
          'backgroundColor' => 'rgba(54, 162, 235, 1)',
        ]
      ),
    );
    $barChart['data'] = $chartData;
    //Chart options 
    $barChart['options'] = array(
      'maintainAspectRatio' => false,
      'showTooltip' => false,
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
            'maxBarThickness' => self::MAX_BAR_THICKNESS,
            'scaleLabel' => [
              'display' => false,
              'labelString' => "Biểu đồ doanh số tỉnh theo các năm",
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
              'display' => true,
              'labelString' => self::DOANH_SO_LABEL,
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
    $barChart['height'] = 300;
    return $barChart;
  }
  public function reportByProvinceQuarter($params) {
    $year = isset($params['year']) ? $params['year'] : '';
    $title = "Doanh số tỉnh theo quý trong năm $year";
    $where = $this->getDataByUser();
    $extraQuery = $this->getLocationSpecific($params);
    if($extraQuery['where'] != '') {
      $where .= $extraQuery['where'];
    }
    if($extraQuery['location'] != '') {
      $title = "Doanh số ". $extraQuery['location'] ." theo quý trong năm $year";
    }
    $sql = "SELECT tinh,mien, ". $this->getSum() .", QUARTER(date) as 'quy' from (". self::FULL_LOCATION_SQL .") as khuvuc, orders,nha_thuoc,exchange WHERE orders.store_id = nha_thuoc.store_id AND orders.status = 1 AND nha_thuoc.district_id = khuvuc.code ". $where ." GROUP BY quy ORDER BY doanhso DESC";
    $data = $this->db->query($sql)->fetchAll(\PDO::FETCH_ASSOC);
    if(empty($data)) {
      return array(
        'status' => 'error', 
        'message' => "Không có dữ liệu theo quý.!"
      );
    }
    $total_num = count($data);
    $report = array();
    foreach($data as $item) {
      $report['labels'][] = "Quý " . $item['quy'];
      $report['data'][] = $item['doanhso'];
      $report['doanhthu'][$item['doanhso']] = $item['doanhthu'];
    }
    $chartData = array(
      'labels' => $report['labels'],
      'doanhthu' => $report['doanhthu'],
      'datasets' => array(
        [
          'label' => 'Doanh số',
          'data' => $report['data'],
          'backgroundColor' => 'rgba(54, 162, 235, 1)',
        ]
      ),
    );
    $barChart['data'] = $chartData;
    //Chart options 
    $barChart['options'] = array(
      'maintainAspectRatio' => false,
      'showTooltip' => false,
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
            'maxBarThickness' => self::MAX_BAR_THICKNESS,
            'scaleLabel' => [
              'display' => false,
              'labelString' => "Biểu đồ doanh số tỉnh theo quý trong năm $year",
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
              'display' => true,
              'labelString' => self::DOANH_SO_LABEL,
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
    $barChart['height'] = 300;
    return $barChart;
  }
  public function reportByProvinceMonth($params) {
    $year = isset($params['year']) ? $params['year'] : '';
    $where = $this->getDataByUser();
    $title = "Doanh số tỉnh theo tháng trong năm $year";
    $extraQuery = $this->getLocationSpecific($params);
    if($extraQuery['where'] != '') {
      $where .= $extraQuery['where'];
    }
    if($extraQuery['location'] != '') {
      $title = "Doanh số ". $extraQuery['location'] ." theo tháng trong năm $year";
    }
    $sql = "SELECT tinh,mien, ". $this->getSum() .", MONTH(date) as 'thang' from (". self::FULL_LOCATION_SQL .") as khuvuc, orders,nha_thuoc,exchange WHERE orders.store_id = nha_thuoc.store_id AND orders.status = 1 AND nha_thuoc.district_id = khuvuc.code ". $where ." GROUP BY thang ORDER BY thang,doanhso DESC";
    $data = $this->db->query($sql)->fetchAll(\PDO::FETCH_ASSOC);
    $total_num = count($data);
    if(empty($data)) {
      return array(
        'status' => 'error', 
        'message' => "Không có dữ liệu theo tháng."
      );
    }
    $report = array();
    foreach($data as $item) {
      $report['labels'][] = $item['thang'];
      $report['data'][] = $item['doanhso'];
      $report['doanhthu'][$item['doanhso']] = $item['doanhthu'];
    }
    $chartData = array(
      'labels' => $report['labels'],
      'doanhthu' => $report['doanhthu'],
      'datasets' => array(
        [
          'label' => 'Doanh số',
          'data' => $report['data'],
          'backgroundColor' => 'rgba(54, 162, 235, 1)',
        ]
      ),
    );
    $barChart['data'] = $chartData;
    //Chart options 
    $barChart['options'] = array(
      'maintainAspectRatio' => false,
      'showTooltip' => false,
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
            'maxBarThickness' => self::MAX_BAR_THICKNESS,
            'scaleLabel' => [
              'display' => false,
              'labelString' => "Biểu đồ doanh số tỉnh theo tháng trong năm $year",
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
              'display' => true,
              'labelString' => self::DOANH_SO_LABEL,
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
    $barChart['height'] = 300;
    return $barChart;
  }
  public function reportByProvinceWeek($params) {
    $year = isset($params['year']) ? $params['year'] : '';
    $where = $this->getDataByUser();
    $title = "Doanh số tỉnh theo tuần trong năm $year";
    $extraQuery = $this->getLocationSpecific($params);
    if($extraQuery['where'] != '') {
      $where .= $extraQuery['where'];
    }
    if($extraQuery['location'] != '') {
      $title = "Doanh số ". $extraQuery['location'] ." theo tuần trong năm $year";
    }
    $sql = "SELECT tinh,mien, ". $this->getSum() .", WEEKOFYEAR(date) as 'tuan' from (". self::FULL_LOCATION_SQL .") as khuvuc, orders,nha_thuoc,exchange WHERE orders.store_id = nha_thuoc.store_id AND orders.status = 1 AND nha_thuoc.district_id = khuvuc.code ". $where ." GROUP BY tuan ORDER BY tuan,doanhso DESC";
    $data = $this->db->query($sql)->fetchAll(\PDO::FETCH_ASSOC);
    $total_num = count($data);
    if(empty($data)) {
      return array(
        'status' => 'error', 
        'message' => "Không có dữ liệu theo tuần!"
      );
    }
    $report = array();
    foreach($data as $item) {
      $report['labels'][] = $item['tuan'];
      $report['data'][] = $item['doanhso'];
      $report['doanhthu'][$item['doanhso']] = $item['doanhthu'];
    }
    $chartData = array(
      'labels' => $report['labels'],
      'doanhthu' => $report['doanhthu'],
      'datasets' => array(
        [
          'label' => 'Doanh số',
          'data' => $report['data'],
          'backgroundColor' => 'rgba(54, 162, 235, 1)',
        ]
      ),
    );
    $barChart['data'] = $chartData;
    //Chart options 
    $barChart['options'] = array(
      'maintainAspectRatio' => false,
      'showTooltip' => false,
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
            'maxBarThickness' => self::MAX_BAR_THICKNESS,
            'scaleLabel' => [
              'display' => false,
              'labelString' => "Biểu đồ doanh số tỉnh theo tuần trong năm $year",
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
              'display' => true,
              'labelString' => self::DOANH_SO_LABEL,
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
    $barChart['height'] = 300;
    return $barChart;
  }
  public function reportByProvinceWeekOfMonth($params) {
    $year = isset($params['year']) ? $params['year'] : '';
    $month = isset($params['month']) ? $params['month'] : '';
    $where = $this->getDataByUser();
    $title = "Doanh số tỉnh theo tuần trong tháng $month/$year";
    $extraQuery = $this->getLocationSpecific($params);
    if($extraQuery['where'] != '') {
      $where .= $extraQuery['where'];
    }
    if($extraQuery['location'] != '') {
      $title = "Doanh số ". $extraQuery['location'] ." theo tuần trong tháng $month/$year";
    }
    if($month != "") {
      $where .= " AND MONTH(date) = '$month'";
    }
    $sql = "SELECT tinh,mien, ". $this->getSum() .", WEEKOFYEAR(date) as 'tuan' from (". self::FULL_LOCATION_SQL .") as khuvuc, orders,nha_thuoc,exchange WHERE orders.store_id = nha_thuoc.store_id AND orders.status = 1 AND nha_thuoc.district_id = khuvuc.code ". $where ." GROUP BY tuan ORDER BY tuan,doanhthu DESC";
    $data = $this->db->query($sql)->fetchAll(\PDO::FETCH_ASSOC);
    $total_num = count($data);
    if(empty($data)) {
      return array(
        'status' => 'error', 
        'message' => "Không có dữ liệu theo tuần trong tháng $month!"
      );
    }
    $report = array();
    foreach($data as $key => $item) {
      //$report['labels'][] = $item['tuan'];//Week of year label
      $report['labels'][] = $key + 1;
      $report['data'][] = $item['doanhso'];
      $report['doanhthu'][$item['doanhso']] = $item['doanhthu'];
    }
    $chartData = array(
      'labels' => $report['labels'],
      'doanhthu' => $report['doanhthu'],
      'datasets' => array(
        [
          'label' => 'Doanh số',
          'data' => $report['data'],
          'backgroundColor' => 'rgba(54, 162, 235, 1)',
        ]
      ),
    );
    $barChart['data'] = $chartData;
    //Chart options 
    $barChart['options'] = array(
      'maintainAspectRatio' => true,
      'showTooltip' => false,
      'title' => array(
        'display' => true,
		'text' =>$title
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
              'labelString' => "Biểu đồ doanh số tỉnh theo tuần trong tháng $month/$year",
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
              'display' => true,
              'labelString' => self::DOANH_SO_LABEL,
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
    $barChart['height'] = 300;
    $barChart['dayOfMonthData'] = $this->reportByProvinceDayOfMonth($params);
    return $barChart;
  }
  public function reportByProvinceDayOfMonth($params) {
    $year = isset($params['year']) ? $params['year'] : '';
    $month = isset($params['month']) ? $params['month'] : '';
    $where = $this->getDataByUser();
    $title = "Doanh số tỉnh theo ngày trong tháng $month/$year";
    $extraQuery = $this->getLocationSpecific($params);
    if($extraQuery['where'] != '') {
      $where .= $extraQuery['where'];
    }
    if($extraQuery['location'] != '') {
      $title = "Doanh số ". $extraQuery['location'] ." theo ngày trong tháng $month/$year";
    }
    if($month != "") {
      $where .= " AND MONTH(date) = '$month'";
    }
    $sql = "SELECT tinh,mien, ". $this->getSum() .", DAYOFMONTH(date) as 'ngay' from (". self::FULL_LOCATION_SQL .") as khuvuc, orders,nha_thuoc,exchange WHERE orders.store_id = nha_thuoc.store_id AND orders.status = 1 AND nha_thuoc.district_id = khuvuc.code ". $where ." GROUP BY ngay ORDER BY ngay";
    $data = $this->db->query($sql)->fetchAll(\PDO::FETCH_ASSOC);
    $total_num = count($data);
    if(empty($data)) {
      return array(
        'status' => 'error', 
        'message' => "Không có dữ liệu theo ngày trong tháng $month!"
      );
    }
    $report = array();
    foreach($data as $key => $item) {
      $report['labels'][] = $item['ngay'];
      $report['data'][] = $item['doanhso'];
      $report['doanhthu'][$item['doanhso']] = $item['doanhthu'];
    }
    $chartData = array(
      'labels' => $report['labels'],
      'doanhthu' => $report['doanhthu'],
      'datasets' => array(
        [
          'label' => 'Doanh số',
          'data' => $report['data'],
          'backgroundColor' => 'rgba(54, 162, 235, 1)',
        ]
      ),
    );
    $barChart['data'] = $chartData;
    //Chart options 
    $barChart['options'] = array(
      'maintainAspectRatio' => true,
      'showTooltip' => false,
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
            'maxBarThickness' => self::MAX_BAR_THICKNESS,
            'scaleLabel' => [
              'display' => false,
              'labelString' => "Biểu đồ doanh số tỉnh theo ngày trong tháng $month/$year",
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
              'display' => true,
              'labelString' => self::DOANH_SO_LABEL,
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
    $barChart['height'] = 300;
    return $barChart;
  }
  public function reportByTopStores($params) {
    $productId = isset($params['product-id']) ? $params['product-id'] : '';
    $title = "Doanh số nhà thuốc theo quận/huyện";
    $where = $this->getDataByUser();
    $having = "";
    $limit = "";
    if($productId != "" && $productId != "all") {
      $where .= " AND <orders.product_id> = '$productId'";
    }
    if(isset($params['province-id']) && $params['province-id'] != "all") {
      $where .= " AND <khuvuc.ma_tinh> = '{$params['province-id']}'";
    }
    if(isset($params['district']) && $params['district'] != "") {
      $where .= " AND <khuvuc.code> = '{$params['district']}'";
    }
    if(isset($params['district']) && $params['district'] != "") {
      $where .= " AND <khuvuc.code> = '{$params['district']}'";
    }
    if(isset($params['doanhthu']) && $params['doanhthu'] != "") {
      $having .= " HAVING doanhthu >= {$params['doanhthu']}";
    } else {
      if(isset($params['doanhso']) && $params['doanhso'] != "") {
        $having .= " HAVING doanhso >= {$params['doanhso']}";
      }
    }
    if(isset($params['fromdate']) && $params['fromdate'] != "" 
      && isset($params['todate']) && $params['todate'] != "") {
      $where .= " AND <orders.date> BETWEEN '{$params['fromdate']}' AND '{$params['todate']}'";
    }
    if(isset($params['limit']) && $params['limit'] != "") {
      $limit = "LIMIT {$params['limit']}";
    }
    $sql = "SELECT orders.date, nha_thuoc.name, ". $this->getSum() .", YEAR(date) as 'nam', khuvuc.code from (". self::FULL_LOCATION_SQL .") as khuvuc, orders,nha_thuoc,exchange WHERE orders.store_id = nha_thuoc.store_id AND orders.status = 1 AND nha_thuoc.district_id = khuvuc.code ". $where ." GROUP BY nha_thuoc.store_id $having ORDER BY doanhso DESC $limit";
    //die($sql);
    $data = $this->db->query($sql)->fetchAll(\PDO::FETCH_ASSOC);
    if(empty($data)) {
      return array(
        'status' => 'error', 
        'message' => 'Không có dữ liệu!'
      );
    }
    $report = array();
    foreach($data as $item) {
      $yLabel = $item['name'];
      $report['labels'][] = $yLabel;
      $report['data'][] = $item['doanhso'];
      $report['doanhthu'][$item['doanhso']] = $item['doanhthu'];
    }
    $total_num = count($data);
    $chartData = array(
      'labels' => $report['labels'],
      'doanhthu' => $report['doanhthu'],
      'type' => 'horizontalBar',
      'datasets' => array(
        [
          'label' => 'Doanh số',
          'data' => $report['data'],
          'backgroundColor' => 'rgba(54, 162, 235, 1)',
        ]
      ),
    );
    $barChart['data'] = $chartData;
    //Chart options 
    $barChart['options'] = array(
      'maintainAspectRatio' => false,
      'showTooltip' => false,
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
            'maxBarThickness' => self::MAX_BAR_THICKNESS,
            'scaleLabel' => [
              'display' => true,
              'labelString' => self::DOANH_SO_LABEL,
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
              'labelString' => self::DOANH_SO_LABEL,
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