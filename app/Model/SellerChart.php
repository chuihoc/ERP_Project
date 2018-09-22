<?php 
namespace App\Model;
use \App\Model\Base;

class SellerChart extends Base {
  protected $db;
  protected $isSuper;
  protected $userId;
  public function __construct($db, $isSuper = false, $userId = '') {
    $this->db = $db;
    $this->isSuper = $isSuper;
    $this->userId = $userId;
  }
  public function reportByYear($year, $maTdv) {
    $where = "orders.status = 1 AND Year(date) = $year " . self::FILTER_BY_EXCHANGE;
    $targetWhere = " AND thoi_gian_theo = 'month'";
    if($maTdv != "" && $maTdv != "all") {
      $where .= " AND orders.tdv='{$maTdv}'";
      $targetWhere  .= " AND tdv='{$maTdv}'";
    }
    $sql = "SELECT ". $this->getSum() .", Year(date) as <nam> FROM <orders>, exchange WHERE $where GROUP BY <nam> ORDER BY <nam>";
    $data = $this->db->query($sql)->fetchAll(\PDO::FETCH_ASSOC);
    //Sql muc tieu 
    $sqlTarget = "SELECT SUM(doanhso) as doanhso,tdv,so_thoi_gian,Year(create_on) as nam FROM quan_ly_ke_hoach WHERE muc_tieu_theo = 'tdv' AND status = 1 $targetWhere GROUP BY nam ORDER BY nam";
    $targetData = $this->db->query($sqlTarget)->fetchAll(\PDO::FETCH_ASSOC);
    $targetReport = array();
    $targetReportChart = array();
    if(!empty($targetData)) {
      foreach ($targetData as $yearTarget) {
        $targetReport[$yearTarget['nam']] = $yearTarget['doanhso'];
      }
    }
    //End target data
    if(empty($data)) {
      return array(
        'status' => 'error', 
        'message' => "Không có dữ liệu theo điều kiện tìm kiếm"
      );
    }
    $report = array();
    foreach($data as $quy) {
      $xLabel = "Năm " . $quy['nam'];
      $report['labels'][] = $xLabel;
      $report['data'][] = $quy['doanhso'];
      $report['doanhthu'][$quy['doanhso']] = $quy['doanhthu'];
      $targetReportChart[] = isset($targetReport[$quy['nam']]) ? $targetReport[$quy['nam']] : 0;
    }
    $chartData = array(
      'labels' => $report['labels'],
      'doanhthu' => $report['doanhthu'],
      'datasets' => array(
        [
          'label' => 'Mục tiêu',
          'data' => $targetReportChart,
          'backgroundColor' => 'rgba(255, 99, 132, 1)',
        ],
        [
          'label' => 'Doanh số',
          'data' => $report['data'],
          'backgroundColor' => 'rgba(54, 162, 235, 1)',
          'target' => $targetReportChart
        ],
        // [
        //   'label' => 'Doanh số',
        //   'data' => $report['doanhso'],
        //   'backgroundColor' => 'rgba(54, 100, 100, 1)',
        //   'hidden' => true
        // ]
      ),
    );
    $barChart['data'] = $chartData;
    //Chart options 
    $tdvLabel = $maTdv ? $maTdv : 'Tất cả TDV';
    $barChart['options'] = array(
      'maintainAspectRatio' => false,
      'title' => array(
        'display' => true,
		'text' => 'Tổng doanh số theo năm của: ' . $tdvLabel 
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
              'display' => true,
              'labelString' => self::DOANH_SO_LABEL,
              'fontStyle' => 'bold',
              'fontColor' => '#ccc'
            ]
          ]
        ]
      ]
    );
    $barChart['width'] = 300;
    $barChart['height'] = 300;
    return $barChart;
  }
  public function reportByQuarter($year, $maTdv) {
    $where = "orders.status = 1 AND Year(date) = $year " . self::FILTER_BY_EXCHANGE;
    if($maTdv != "" && $maTdv != "all") {
      $where .= " AND orders.tdv='{$maTdv}'";
    }
    $sql = "SELECT ". $this->getSum() .", QUARTER(date) as <quy> FROM <orders>, exchange WHERE $where GROUP BY <quy> ORDER BY <quy>";
    $data = $this->db->query($sql)->fetchAll(\PDO::FETCH_ASSOC);
    if(empty($data)) {
      return array(
        'status' => 'error', 
        'message' => 'Chưa có dữ liệu theo quý!'
      );
    }
    $report = array();
    foreach(self::QUARTER_VALUES as $quater => $value) {
      $report['labels'][$quater] = "Quý $quater";
      $report['data'][$quater] = $value;
    }
    foreach($data as $quy) {
      $xLabel = "Quý " . $quy['quy'];
      $report['labels'][$quy['quy']] = $xLabel;
      $report['data'][$quy['quy']] = $quy['doanhso'];
      $report['doanhthu'][$quy['doanhso']] = $quy['doanhthu'];
    }
    $quaterTarget = $this->getQuarterTarget($year, $maTdv);
    $chartData = array(
      'labels' => array_values($report['labels']),
      'doanhthu' => $report['doanhthu'],
      'datasets' => array(
        [
          'label' => 'Mục tiêu',
          'data' => $quaterTarget,
          'backgroundColor' => 'rgba(255, 99, 132, 1)',
        ],
        [
          'label' => 'Doanh số',
          'data' => array_values($report['data']),
          'backgroundColor' => 'rgba(54, 162, 235, 1)',
          'target' => $quaterTarget
        ]
      ),
    );
    $barChart['data'] = $chartData;
    //Chart options 
    $tdvLabel = $maTdv ? $maTdv : 'Tất cả TDV';
    $barChart['options'] = array(
      'maintainAspectRatio' => false,
      'title' => array(
        'display' => true,
		'text' => 'Doanh số theo quý (năm '. $year .') của: ' . $tdvLabel
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
              'labelString' => "Biểu đồ doanh thu theo quý năm $year",
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
    $barChart['width'] = 300;
    $barChart['height'] = 300;
    return $barChart;
  }
  protected function getDataByUser() {
    $where = self::FILTER_BY_EXCHANGE;
    if(!$this->isSuper) {
      $where .= " AND orders.create_by = '{$this->userId}' ";
    }
    return $where;
  }
  protected function getQuarterTarget($year, $maTdv) {
    $monthTarget = $this->getMonthTarget($year, $maTdv);
    $target = array(
      $monthTarget["01/$year"] + $monthTarget["02/$year"] + $monthTarget["03/$year"],
      $monthTarget["04/$year"] + $monthTarget["05/$year"] + $monthTarget["06/$year"],
      $monthTarget["07/$year"] + $monthTarget["08/$year"] + $monthTarget["09/$year"],
      $monthTarget["10/$year"] + $monthTarget["11/$year"] + $monthTarget["12/$year"],
    );
    return $target;
  }
  protected function getMonthTarget($year, $maTdv) {
    $targetWhere = " AND YEAR(create_on) = $year";
    if($maTdv != "" && $maTdv != "all") {
      $targetWhere = " AND tdv_id='{$maTdv}'";
    }
    $sqlTarget = "SELECT SUM(doanhso) as doanhso,tdv_id,so_thoi_gian FROM quan_ly_ke_hoach WHERE muc_tieu_theo = 'tdv' AND status = 1 $targetWhere GROUP BY so_thoi_gian";
    $targetData = $this->db->query($sqlTarget)->fetchAll(\PDO::FETCH_ASSOC);
    $targetReport = array();
    $targetReportChart = array();
    if(!empty($targetData)) {
      foreach ($targetData as $monthTarget) {
        $targetReport[$monthTarget['so_thoi_gian']] = $monthTarget['doanhso'];
      }
    }
    foreach(self::TARGET_MONTH_VALUES as $month => $value) {
      //Month format MM/YYY
      $monthFormat = "$month/$year";
      $targetReportChart[$monthFormat] = isset($targetReport[$monthFormat]) ? $targetReport[$monthFormat] : 0;
    }
    return $targetReportChart;
  }
  public function reportByMonthOfYear($year, $maTdv) {
    $where = "orders.status = 1 AND Year(date) = $year " . self::FILTER_BY_EXCHANGE;
    if($maTdv != "" && $maTdv != "all") {
      $where .= " AND orders.tdv='{$maTdv}'";
    }
    $sql = "SELECT ". $this->getSum() .", MONTH(date) as <thang> FROM <orders>, exchange WHERE $where GROUP BY <thang> ORDER BY <thang>";
    $data = $this->db->query($sql)->fetchAll(\PDO::FETCH_ASSOC);
    if(empty($data)) {
      return array(
        'status' => 'error', 
        'message' => 'Chưa có dữ liệu theo tháng!'
      );
    }
    $report = array(
      'labels' => self::MONTH_LABELS,
      'data' => self::MONTH_VALUES
    );
    $monthTarget = $this->getMonthTarget($year, $maTdv);
    foreach($data as $quy) {
      $xLabel = $quy['thang'];
      $report['labels'][$quy['thang']] = $xLabel;
      $report['data'][$quy['thang']] = $quy['doanhso'];
      $report['doanhthu'][$quy['doanhso']] = $quy['doanhthu'];
    }
    $chartData = array(
      'labels' => array_values($report['labels']),
      'doanhthu' => $report['doanhthu'],
      'datasets' => array(
        [
          'label' => 'Mục tiêu',
          'type' => 'line',
          'data' => array_values($monthTarget),
          'fill' => false,
          'backgroundColor' => 'rgba(255, 99, 132, 1)',
          'borderColor' => '#ED6D85',
          'pointBorderColor' => '#ED6D85',
          'pointBackgroundColor' => '#ED6D85',
          'pointHoverBackgroundColor' => '#ED6D85',
          'pointHoverBorderColor' => '#ED6D85',
        ],
        [
          'label' => 'Doanh số',
          'data' => array_values($report['data']),
          'backgroundColor' => 'rgba(54, 162, 235, 1)',
          'target' => array_values($monthTarget)
        ]
      ),
    );
    $barChart['data'] = $chartData;
    $tdvLabel = $maTdv ? $maTdv : 'Tất cả TDV';
    //Chart options 
    $barChart['options'] = array(
      'maintainAspectRatio' => false,
      'title' => array(
        'display' => true,
		'text' => 'Doanh số theo tháng (năm '. $year .') của: ' . $tdvLabel
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
              'labelString' => "Biểu đồ doanh thu theo tháng năm $year",
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
    $barChart['width'] = 300;
    $barChart['height'] = 300;
    return $barChart;
  }
  protected function getWeekOfYearTarget($year, $maTdv) {
    $targetWhere = " AND YEAR(create_on) = $year AND thoi_gian_theo = 'week_of_year'";
    if($maTdv != "" && $maTdv != "all") {
      $targetWhere = " AND tdv_id='{$maTdv}'";
    }
    $sqlTarget = "SELECT SUM(doanhso) as doanhso,tdv_id,so_thoi_gian FROM quan_ly_ke_hoach WHERE muc_tieu_theo = 'tdv' AND status = 1 $targetWhere GROUP BY so_thoi_gian";
    $targetData = $this->db->query($sqlTarget)->fetchAll(\PDO::FETCH_ASSOC);
    $targetReport = array();
    $targetReportChart = array();
    if(!empty($targetData)) {
      foreach ($targetData as $monthTarget) {
        $targetReport[$monthTarget['so_thoi_gian']] = $monthTarget['doanhso'];
      }
    }
    $weekOfYear = $this->getWeekOfYearData();
    foreach($weekOfYear as $week => $value) {
      //Month format MM/YYY
      $weekFormat = "$week/$year";
      $targetReportChart[$weekFormat] = isset($targetReport[$weekFormat]) ? $targetReport[$weekFormat] : 0;
    }
    return $targetReportChart;
  }
  public function reportByWeekOfYear($year, $maTdv) {
    $where = "orders.status = 1 AND Year(date) = $year " . self::FILTER_BY_EXCHANGE;
    if($maTdv != "" && $maTdv != "all") {
      $where .= " AND orders.tdv='{$maTdv}'";
    }
    $sql = "SELECT ". $this->getSum() .", WEEKOFYEAR(date) as <tuan> FROM <orders>, exchange WHERE $where GROUP BY <tuan> ORDER BY <tuan>";
    $data = $this->db->query($sql)->fetchAll(\PDO::FETCH_ASSOC);
    if(empty($data)) {
      return array(
        'status' => 'error', 
        'message' => 'Empty data!'
      );
    }
    //Load plan data 
    //$plan = new Plan($this->db);
    //$planAmounts = $plan->getPlanPerWeek($year, $maTdv);
    $weekTarget = $this->getWeekOfYearTarget($year, $maTdv);
//    echo "<pre>";
//    print_r($planAmounts);
//    die();
    $report = array();
    $weekOfYear = $this->getWeekOfYearData();
    foreach ($weekOfYear as $week => $value) {
      $report['labels'][$week] = $week;
      $report['data'][$week] = $value;
    }
    foreach($data as $quy) {
      $xLabel = $quy['tuan'];
      $report['labels'][$quy['tuan']] = $xLabel;
      $report['data'][$quy['tuan']] = $quy['doanhso'];
      $report['doanhthu'][$quy['doanhso']] = $quy['doanhthu'];
    }
    $chartData = array(
      'labels' => array_values($report['labels']),
      'doanhthu' => $report['doanhthu'],
      'datasets' => array(
        [
          'label' => 'Mục tiêu',
          'type' => 'line',
          'data' => array_values($weekTarget),
          'fill' => false,
          'backgroundColor' => 'rgba(255, 99, 132, 1)',
          'borderColor' => '#ED6D85',
          'pointBorderColor' => '#ED6D85',
          'pointBackgroundColor' => '#ED6D85',
          'pointHoverBackgroundColor' => '#ED6D85',
          'pointHoverBorderColor' => '#ED6D85',
        ],
        [
          'label' => 'Doanh số',
          'data' => array_values($report['data']),
          'backgroundColor' => 'rgba(54, 162, 235, 1)',
          'target' => array_values($weekTarget)
        ]
      ),
    );
    $barChart['data'] = $chartData;
    $tdvLabel = $maTdv ? $maTdv : 'Tất cả TDV';
    //Chart options 
    $barChart['options'] = array(
      'maintainAspectRatio' => false,
      'title' => array(
        'display' => true,
		'text' => 'Doanh số theo tuần (năm '. $year .') của: ' . $tdvLabel
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
              'labelString' => "Biểu đồ doanh thu theo tuần năm $year",
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
    $barChart['width'] = 300;
    $barChart['height'] = 300;
    return $barChart;
  }
  public function reportByTopSellers($params) {    
    $title = "Doanh số theo TDV";
    $where = $this->getDataByUser();
    $having = "";
    $limit = "";
    if(isset($params['nam']) && $params['nam'] != "") {
      $where .= " AND nam = '{$params['nam']}'";
    }
    if(isset($params['limit']) && $params['limit'] != "") {
      $title = "Doanh số top " . $params['limit'] . " TDV";
      $limit = "LIMIT {$params['limit']}";
    }
    $sql = "SELECT tdv.name, ". $this->getSum() .", YEAR(date) as 'nam' FROM orders,tdv,exchange WHERE orders.tdv = tdv.ma_tdv AND orders.tdv <> '' AND orders.status = 1 ". $where ." GROUP BY orders.tdv $having ORDER BY doanhso DESC $limit";
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