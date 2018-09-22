<?php 
namespace App\Model;

class ProvinceChart extends Base {
  protected $db;
  protected $isSuper;
  protected $userId;
  public function __construct($db, $isSuper = false, $userId = '') {
    $this->db = $db;
    $this->isSuper = $isSuper;
    $this->userId = $userId;
  }
  protected function getDataByUser($year, $productId, $area) {
    $where = "orders.status = 1 AND YEAR(date) = $year" . self::FILTER_BY_EXCHANGE;
    if(!$this->isSuper) {
      $where .= " AND orders.create_by = '{$this->userId}' ";
    }
    return $where;
  }
  public function reportByProvinceYear($year, $productId, $area) {
    $where = $this->getDataByUser($year, $productId, $area);
    if($productId != "" && $productId != "all") {
      $where .= " AND <orders.product_id> = '$productId'";
    }
    if($area != "" && $area != "all") {
      $where .= " AND <khuvuc.ma_mien> = '$area'";
    }
    $sql = "SELECT tinh,mien, ". $this->getSum() ." from (". self::FULL_LOCATION_SQL .") as khuvuc, orders,nha_thuoc,exchange WHERE orders.store_id = nha_thuoc.store_id AND nha_thuoc.district_id = khuvuc.code AND ". $where ." GROUP BY mien,tinh ORDER BY doanhso DESC";
    $data = $this->db->query($sql)->fetchAll(\PDO::FETCH_ASSOC);
    if(empty($data)) {
      return array(
        'status' => 'error', 
        'message' => 'Không có dữ liệu!'
      );
    }
    $report = array();
    foreach($data as $item) {
      $report['labels'][] = $item['tinh'];
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
      'showTooltip' => true,
      'title' => array(
        'display' => true,
		    'text' => 'Doanh số các tỉnh năm ' . $year
      ),
      'scales' => [
        'xAxes' => [
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
        ],
        'yAxes' => [
          [
            'ticks' => [
              'beginAtZero' => true
            ],
            'scaleLabel' => [
              'display' => true,
              'labelString' => 'Danh sách các tỉnh',
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
    $barChart['height'] = 50*$total_num + 120;
    return $barChart;
  }
  public function reportByProvinceQuarter($year, $productId, $area, $quarter) {
    $where = $this->getDataByUser($year, $productId, $area);
    $where .= " AND QUARTER(date) = $quarter";
    if($productId != "" && $productId != "all") {
      $where .= " AND <orders.product_id> = '$productId'";
    }
    if($area != "" && $area != "all") {
      $where .= " AND <khuvuc.ma_mien> = '$area'";
    }
    $sql = "SELECT tinh,mien, ". $this->getSum() ." from (". self::FULL_LOCATION_SQL .") as khuvuc, orders,nha_thuoc,exchange WHERE orders.store_id = nha_thuoc.store_id AND nha_thuoc.district_id = khuvuc.code AND ". $where ." GROUP BY mien,tinh ORDER BY doanhso DESC";
    $data = $this->db->query($sql)->fetchAll(\PDO::FETCH_ASSOC);
    if(empty($data)) {
      return array(
        'status' => 'error', 
        'message' => "Không có dữ liệu trong quý $quarter. Hãy chọn quý khác từ bộ lọc!"
      );
    }
    $total_num = count($data);
    $report = array();
    foreach($data as $item) {
      $report['labels'][] = $item['tinh'];
      $report['data'][] = $item['doanhso'];
      $report['doanhthu'][$item['doanhso']] = $item['doanhthu'];
    }
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
      'showTooltip' => true,
      'title' => array(
        'display' => true,
		'text' => "Tổng doanh số các tỉnh quý $quarter năm $year"
      ),
      'scales' => [
        'xAxes' => [
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
        ],
        'yAxes' => [
          [
            'ticks' => [
              'beginAtZero' => true
            ],
            'scaleLabel' => [
              'display' => true,
              'labelString' => 'Danh sách các tỉnh',
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
    $barChart['height'] = 50*$total_num + 120;
    return $barChart;
  }
  public function reportByProvinceMonth($year, $productId, $area, $month) {
    $where = $this->getDataByUser($year, $productId, $area);
    $where .= " AND MONTH(date) = $month";
    if($productId != "" && $productId != "all") {
      $where .= " AND <orders.product_id> = '$productId'";
    }
    if($area != "" && $area != "all") {
      $where .= " AND <khuvuc.ma_mien> = '$area'";
    }
    $sql = "SELECT tinh,mien, ". $this->getSum() ." from (". self::FULL_LOCATION_SQL .") as khuvuc, orders,nha_thuoc,exchange WHERE orders.store_id = nha_thuoc.store_id AND nha_thuoc.district_id = khuvuc.code AND ". $where ." GROUP BY mien,tinh ORDER BY doanhso DESC";
    $data = $this->db->query($sql)->fetchAll(\PDO::FETCH_ASSOC);
    $total_num = count($data);
    if(empty($data)) {
      return array(
        'status' => 'error', 
        'message' => "Không có dữ liệu trong tháng $month. Hãy chọn tháng khác từ bộ lọc"
      );
    }
    $report = array();
    foreach($data as $item) {
      $report['labels'][] = $item['tinh'];
      $report['data'][] = $item['doanhso'];
      $report['doanhthu'][$item['doanhso']] = $item['doanhthu'];
    }
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
      'showTooltip' => true,
      'title' => array(
        'display' => true,
		    'text' => "Tổng doanh số các tỉnh tháng $month năm $year"
      ),
      'scales' => [
        'xAxes' => [
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
        ],
        'yAxes' => [
          [
            'ticks' => [
              'beginAtZero' => true
            ],
            'scaleLabel' => [
              'display' => true,
              'labelString' => 'Danh sách các tỉnh',
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
    $barChart['height'] = 50*$total_num + 120;
    return $barChart;
  }
  public function reportByProvinceWeek($year, $productId, $area, $week) {
    $where = $this->getDataByUser($year, $productId, $area);
    $where .= " AND WEEKOFYEAR(date) = $week";
    if($productId != "" && $productId != "all") {
      $where .= " AND <orders.product_id> = '$productId'";
    }
    if($area != "" && $area != "all") {
      $where .= " AND <khuvuc.ma_mien> = '$area'";
    }
    $sql = "SELECT tinh,mien, ". $this->getSum() ." from (". self::FULL_LOCATION_SQL .") as khuvuc, orders,nha_thuoc, exchange WHERE orders.store_id = nha_thuoc.store_id AND nha_thuoc.district_id = khuvuc.code AND ". $where ." GROUP BY mien,tinh ORDER BY doanhso DESC";
    $data = $this->db->query($sql)->fetchAll(\PDO::FETCH_ASSOC);
    $total_num = count($data);
    if(empty($data)) {
      return array(
        'status' => 'error', 
        'message' => "Không có dữ liệu trong tuần $week! Hãy chọn tuần khác từ bộ lọc"
      );
    }
    $report = array();
    foreach($data as $item) {
      $report['labels'][] = $item['tinh'];
      $report['data'][] = $item['doanhso'];
      $report['doanhthu'][$item['doanhso']] = $item['doanhthu'];
    }
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
      'showTooltip' => true,
      'title' => array(
        'display' => true,
		'text' => "Tổng doanh số các tỉnh tuần $week năm $year"
      ),
      'scales' => [
        'xAxes' => [
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
        ],
        'yAxes' => [
          [
            'ticks' => [
              'beginAtZero' => true
            ],
            'scaleLabel' => [
              'display' => true,
              'labelString' => 'Danh sách các tỉnh',
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
    $barChart['height'] = 50*$total_num + 120;
    return $barChart;
  }
}