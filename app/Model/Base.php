<?php 
namespace App\Model;

class Base {
  const MONTH_LABELS = [
    1 => 1,
    2 => 2,
    3 => 3,
    4 => 4,
    5 => 5,
    6 => 6,
    7 => 7,
    8 => 8,
    9 => 9,
    10 => 10,
    11 => 11,
    12 => 12,
  ];
  const MONTH_VALUES = [
    1 => 0,
    2 => 0,
    3 => 0,
    4 => 0,
    5 => 0,
    6 => 0,
    7 => 0,
    8 => 0,
    9 => 0,
    10 => 0,
    11 => 0,
    12 => 0,
  ];
  const TARGET_MONTH_VALUES = [
    '01' => 0,
    '02' => 0,
    '03' => 0,
    '04' => 0,
    '05' => 0,
    '06' => 0,
    '07' => 0,
    '08' => 0,
    '09' => 0,
    '10' => 0,
    '11' => 0,
    '12' => 0,
  ];
  const QUARTER_VALUES = [
    1 => 0,
    2 => 0,
    3 => 0,
    4 => 0,
  ];
  const MAX_BAR_THICKNESS = 35;
  const DOANH_SO_LABEL = 'Doanh số (số hộp)';
  const FULL_LOCATION_SQL = "SELECT district.code, areas.area_code as 'ma_mien', areas.name as 'mien',provinces.code as 'ma_tinh', provinces.name as 'tinh' from district,provinces,areas WHERE district.parent_code = provinces.code AND areas.area_code = provinces.area_code";
  public function getSum() {
    return "SUM(CASE
    WHEN lower(orders.unit) = 'lọ' THEN (orders.qty * exchange.lo)
    WHEN lower(orders.unit) = 'vỉ' THEN (orders.qty * exchange.vi)
    ELSE qty
END) as doanhso,REPLACE(FORMAT(SUM(orders.qty * orders.price /1000000),2),',','') as 'doanhthu'";
  }
  const WITHOUT_COD_SQL = " AND nha_thuoc.name NOT LIKE 'COD%'";
  const WITH_COD_SQL = " AND nha_thuoc.name LIKE 'COD%'";
  const FILTER_BY_EXCHANGE = " AND orders.product_id = exchange.product_id ";
  public function getWeekOfYearData () {
    $weekOfYear = [];
    for($i = 1; $i <= 52; $i++) {
      $weekOfYear[$i] = 0;
    }
    return $weekOfYear;
  }
  protected function getCODSQL($codWhere, $groupBy = "", $extraColumn = "") {
    $codSQL = "SELECT SUM(orders.qty) as 'doanhso', REPLACE(FORMAT(SUM(orders.qty * orders.price /1000000),2),',','') as 'doanhthu' $extraColumn FROM orders,nha_thuoc,exchange WHERE orders.store_id = nha_thuoc.store_id AND nha_thuoc.name LIKE 'COD%' AND $codWhere $groupBy ORDER BY doanhthu DESC";
    return $codSQL;
  }
}