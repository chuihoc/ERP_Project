<?php 
namespace App\Controllers;
use \Slim\Views\PhpRenderer;
use \App\Helper\Data;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class HomeController extends BaseController {
  public function index($request, $response) {
    echo "Hello World!";
  }
  public function test($request, $response) {
    die('test');
    // Create new Spreadsheet object
    $ans_products = $this->db->select("ans_products", "*");
    $this->exportExcel($products, "My TItle", "My_filename.xlsx");
  }
}