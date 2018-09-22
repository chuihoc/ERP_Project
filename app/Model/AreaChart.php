<?php 
namespace App\Model;

class AreaChart extends Base {
  protected $db;
  protected $isSuper;
  protected $userId;
  public function __construct($db, $isSuper = false, $userId = '') {
    $this->db = $db;
    $this->isSuper = $isSuper;
    $this->userId = $userId;
  }
  protected function getDataByUser($year, $productId, $area) {
    $where = "orders.status = 1 AND YEAR(date) = $year AND orders.product_id = exchange.product_id";
    if(!$this->isSuper) {
      $where .= " AND orders.create_by = '{$this->userId}' ";
    }
    return $where;
  }
  public function reportByAreas($year, $productId, $area) {
    $where = $this->getDataByUser($year, $productId, $area);
    $codWhere = $where;
    //Get rid of COD store
    $where .= self::WITHOUT_COD_SQL;
    if($productId != "" && $productId != "all") {
      $where .= " AND <orders.product_id> = '$productId'";
      $codWhere .= " AND <orders.product_id> = '$productId'";
    }
    if($area != "" && $area != "all") {
      $where .= " AND <khuvuc.ma_mien> = '$area'";
    }
    $sql = "SELECT code, mien,". $this->getSum() ." from (
select district.code, areas.area_code as 'ma_mien', areas.name as 'mien' from district,provinces,areas WHERE district.parent_code = provinces.code AND provinces.area_code = areas.area_code
) as khuvuc, orders,nha_thuoc,exchange WHERE orders.store_id = nha_thuoc.store_id AND nha_thuoc.district_id = khuvuc.code AND ". $where ." GROUP BY mien ORDER BY doanhthu DESC";
    $data = $this->db->query($sql)->fetchAll(\PDO::FETCH_ASSOC);
    $codSQL = $this->getCODSQL($codWhere);
    $codData = $this->db->query($codSQL)->fetchAll(\PDO::FETCH_ASSOC);
    if(empty($data)) {
      return array(
        'status' => 'error', 
        'message' => 'Empty data!'
      );
    }
    $report = array();
    foreach($data as $quy) {
      $xLabel = "Miền " . $quy['mien'];
      $report['labels'][] = $xLabel;
      $report['data'][] = $quy['doanhso'];
      $report['doanhthu'][$quy['doanhso']] = $quy['doanhthu'];
    }
    //Column COD 
    $report['labels'][] = "COD";
    $doanhSo = isset($codData[0]['doanhso']) ? $codData[0]['doanhso']  : 0;
    $doanhThu = isset($codData[0]['doanhthu']) ? $codData[0]['doanhthu'] : 0;
    $report['data'][] = $doanhSo;
    $report['doanhthu'][$doanhSo] = $doanhThu;
    $chartData = array(
      'labels' => $report['labels'],
      'doanhthu' => $report['doanhthu'],
      'datasets' => array(
        // [
        //   'label' => 'Mục tiêu',
        //   'data' => [0.5,0.7,1],
        //   'backgroundColor' => 'rgba(255, 99, 132, 1)',
        // ],
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
		'text' => 'Tổng doanh số các miền năm ' . $year
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
  public function reportByAreasQuarter($year, $productId, $area) {
    $where = $where = $this->getDataByUser($year, $productId, $area);
    $codWhere = $where;
    $where .= self::WITHOUT_COD_SQL;
    if($productId != "" && $productId != "all") {
      $where .= " AND <orders.product_id> = '$productId'";
      $codWhere .=  " AND <orders.product_id> = '$productId'";
    }
    if($area != "" && $area != "all") {
      $where .= " AND <khuvuc.ma_mien> = '$area'";
    }
    $sql = "SELECT code, khuvuc.ma_mien, QUARTER(orders.date) as 'quy',". $this->getSum() ." from (
select district.code, areas.area_code as 'ma_mien', areas.name as 'mien' from district,provinces,areas WHERE district.parent_code = provinces.code AND provinces.area_code = areas.area_code
) as khuvuc, orders,nha_thuoc,exchange WHERE orders.store_id = nha_thuoc.store_id AND nha_thuoc.district_id = khuvuc.code AND ". $where ." GROUP BY ma_mien, quy ORDER BY ma_mien,quy";
    $data = $this->db->query($sql)->fetchAll(\PDO::FETCH_ASSOC);
    $codSQL = $this->getCODSQL($codWhere, "GROUP BY quy", ",QUARTER(orders.date) as 'quy'");
    $codData = $this->db->query($codSQL)->fetchAll(\PDO::FETCH_ASSOC);
    if(empty($data)) {
      return array(
        'status' => 'error', 
        'message' => 'Empty data!'
      );
    }    
    $report = array(
      'b' => [
        1 => 0,
        2 => 0,
        3 => 0,
        4=> 0
      ],
      't' => [
        1 => 0,
        2 => 0,
        3 => 0,
        4=> 0
      ],
      'n' => [
        1 => 0,
        2 => 0,
        3 => 0,
        4=> 0
      ],
      'cod' => [
        1 => 0,
        2 => 0,
        3 => 0,
        4=> 0
      ]
    );
    $doanhthu = array();
    foreach($codData as $item) {
      $report['cod'][$item['quy']] = $item['doanhso'];
      $doanhthu[$item['doanhso']] = $item['doanhthu'];
    }
    //COD column data 
    foreach($data as $item) {
      switch($item['ma_mien']) {
        case 'b':
          $report['b'][$item['quy']] = $item['doanhso'];
          $doanhthu[$item['doanhso']] = $item['doanhthu'];
          break;
        case 't':
          $report['t'][$item['quy']] = $item['doanhso'];
          $doanhthu[$item['doanhso']] = $item['doanhthu'];
          break;
        case 'n':
          $report['n'][$item['quy']] = $item['doanhso'];
          $doanhthu[$item['doanhso']] = $item['doanhthu'];
          break;
      }
    }
    $chartData = array(
      'labels' => ["Quý 1", "Quý 2", "Quý 3", "Quý 4"],
      'doanhthu' => $doanhthu,
      'doanhthuType' => 'quarter',
      'datasets' => array(
        [
          'label' => 'Bắc',
          'data' => array_values($report['b']),
          'backgroundColor' => 'rgba(54, 162, 235, 1)',
        ],
        [
          'label' => 'Trung',
          'data' => array_values($report['t']),
          'backgroundColor' => 'rgba(255, 99, 132, 1)',
        ],
        [
          'label' => 'Nam',
          'data' => array_values($report['n']),
          'backgroundColor' => '#6CBEBF',
        ],
       [
         'label' => 'COD',
         'data' => array_values($report['cod']),
         'backgroundColor' => '#F7CF6B',
       ],
      ),
    );
    $barChart['data'] = $chartData;
    //Chart options 
    $barChart['options'] = array(
      'maintainAspectRatio' => false,
      'showTooltip' => false,
      'title' => array(
        'display' => true,
		'text' => 'Doanh số các miền theo quý năm ' . $year
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
      
    );
    $barChart['width'] = 300;
    $barChart['height'] = 300;
    return $barChart;
  }
  public function reportByAreasMonth($year, $productId, $area) {
    $where = $this->getDataByUser($year, $productId, $area);
    $codWhere = $where;
    $where .= self::WITHOUT_COD_SQL;
    if($productId != "" && $productId != "all") {
      $where .= " AND <orders.product_id> = '$productId'";
      $codWhere .= " AND <orders.product_id> = '$productId'";
    }
    if($area != "" && $area != "all") {
      $where .= " AND <khuvuc.ma_mien> = '$area'";
    }
    $sql = "SELECT code, khuvuc.ma_mien, MONTH(orders.date) as 'thang',". $this->getSum() ." from (
select district.code, areas.area_code as 'ma_mien', areas.name as 'mien' from district,provinces,areas WHERE district.parent_code = provinces.code AND provinces.area_code = areas.area_code
) as khuvuc, orders,nha_thuoc,exchange WHERE orders.store_id = nha_thuoc.store_id AND nha_thuoc.district_id = khuvuc.code AND ". $where ." GROUP BY ma_mien, thang ORDER BY ma_mien,thang";
    $data = $this->db->query($sql)->fetchAll(\PDO::FETCH_ASSOC);
    $codSQL = $this->getCODSQL($codWhere, "GROUP BY thang", ",MONTH(orders.date) as 'thang'");
    $codData = $this->db->query($codSQL)->fetchAll(\PDO::FETCH_ASSOC);
    if(empty($data)) {
      return array(
        'status' => 'error', 
        'message' => 'Empty data!'
      );
    }    
    $report = array(
      'b' => self::MONTH_VALUES,
      't' => self::MONTH_VALUES,
      'n' => self::MONTH_VALUES,
      'cod' => self::MONTH_VALUES,
    );
    $doanhthu = array();
    foreach($data as $item) {
      switch($item['ma_mien']) {
        case 'b':
          $report['b'][$item['thang']] = $item['doanhso'];
          $doanhthu[$item['doanhso']] = $item['doanhthu'];
          break;
        case 't':
          $report['t'][$item['thang']] = $item['doanhso'];
          $doanhthu[$item['doanhso']] = $item['doanhthu'];
          break;
        case 'n':
          $report['n'][$item['thang']] = $item['doanhso'];
          $doanhthu[$item['doanhso']] = $item['doanhthu'];
          break;
      }
    }
    //COD column
    foreach($codData as $item) {
      $report['cod'][$item['thang']] = $item['doanhso'];
      $doanhthu[$item['doanhso']] = $item['doanhthu'];
    }
    $chartData = array(
      'labels' => [1, 2, 3, 4, 5,6,7,8,9,10,11,12],
      'doanhthu' => $doanhthu,
      'datasets' => array(
        [
          'label' => 'Bắc',
          'data' => array_values($report['b']),
          'backgroundColor' => 'rgba(54, 162, 235, 1)',
        ],
        [
          'label' => 'Trung',
          'data' => array_values($report['t']),
          'backgroundColor' => 'rgba(255, 99, 132, 1)',
        ],
        [
          'label' => 'Nam',
          'data' => array_values($report['n']),
          'backgroundColor' => '#6CBEBF',
        ],
       [
         'label' => 'COD',
         'data' => array_values($report['cod']),
         'backgroundColor' => '#F7CF6B',
       ],
      ),
    );
    $barChart['data'] = $chartData;
    //Chart options 
    $barChart['options'] = array(
      'maintainAspectRatio' => false,
      'showTooltip' => false,
      'title' => array(
        'display' => true,
		'text' => 'Doanh số các miền theo tháng năm ' . $year
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
      
    );
    $barChart['width'] = 300;
    $barChart['height'] = 300;
    return $barChart;
  }
  public function reportByAreasWeek($year, $productId, $area) {
    $where = $this->getDataByUser($year, $productId, $area);
    //$where .= self::WITHOUT_COD_SQL;
    if($productId != "" && $productId != "all") {
      $where .= " AND <orders.product_id> = '$productId'";
    }
    if($area != "" && $area != "all") {
      $where .= " AND <khuvuc.ma_mien> = '$area'";
    }
    $sql = "SELECT code, khuvuc.ma_mien, WEEKOFYEAR(orders.date) as 'tuan',". $this->getSum() ." from (
select district.code, areas.area_code as 'ma_mien', areas.name as 'mien' from district,provinces,areas WHERE district.parent_code = provinces.code AND provinces.area_code = areas.area_code
) as khuvuc, orders,nha_thuoc, exchange WHERE orders.store_id = nha_thuoc.store_id AND nha_thuoc.district_id = khuvuc.code AND ". $where ." GROUP BY ma_mien, tuan ORDER BY ma_mien,tuan";
    $data = $this->db->query($sql)->fetchAll(\PDO::FETCH_ASSOC);
    if(empty($data)) {
      return array(
        'status' => 'error', 
        'message' => 'Empty data!'
      );
    }    
//    echo "<pre>";
//    print_r($data);
//    die;
    $weekOfYearData = $this->getWeekOfYearData();
    $report = array(
      'b' => $weekOfYearData,
      't' => $weekOfYearData,
      'n' => $weekOfYearData,
    );
    $doanhthu = array();
    foreach($data as $item) {
      switch($item['ma_mien']) {
        case 'b':
          $report['b'][$item['tuan']] = $item['doanhso'];
          $doanhthu[$item['doanhso']] = $item['doanhthu'];
          break;
        case 't':
          $report['t'][$item['tuan']] = $item['doanhso'];
          $doanhthu[$item['doanhso']] = $item['doanhthu'];
          break;
        case 'n':
          $report['n'][$item['tuan']] = $item['doanhso'];
          $doanhthu[$item['doanhso']] = $item['doanhthu'];
          break;
      }
    }
    $weekLabels = [];
    for($i = 1; $i <= 52; $i++) {
      $weekLabels[] = $i;
    }
    $chartData = array(
      'labels' => $weekLabels,
      'doanhthu' => $doanhthu,
      'datasets' => array(
        [
          'label' => 'Bắc',
          'data' => array_values($report['b']),
          'backgroundColor' => 'rgba(54, 162, 235, 1)',
        ],
        [
          'label' => 'Trung',
          'data' => array_values($report['t']),
          'backgroundColor' => 'rgba(255, 99, 132, 1)',
        ],
        [
          'label' => 'Nam',
          'data' => array_values($report['n']),
          'backgroundColor' => '#6CBEBF',
        ],
//        [
//          'label' => 'COD',
//          'data' => [0.5,0.7,1],
//          'backgroundColor' => '#F7CF6B',
//        ],
      ),
    );
    $barChart['data'] = $chartData;
    //Chart options 
    $barChart['options'] = array(
      'maintainAspectRatio' => false,
      'showTooltip' => false,
      'title' => array(
        'display' => true,
		'text' => 'Doanh số các miền theo tuần năm ' . $year
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
      
    );
    $barChart['width'] = 300;
    $barChart['height'] = 300;
    return $barChart;
  }
}