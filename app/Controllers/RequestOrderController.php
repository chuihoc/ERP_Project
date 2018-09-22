<?php
namespace App\Controllers;
use \Medoo\Medoo;
use \Monolog\Logger;
use \Ramsey\Uuid\Uuid;
use App\Helper\Roles;

class RequestOrderController extends BaseController
{
	private $tableName = 'lotus_request_orders';

	private function getColumns() {
		$columns = [
			'id',
			'ma_order',
			'date_delivery',
			'ma_kh',
			'note',
			'tinh_trang',
			'filename',
			'create_on' => Medoo::raw("DATE_FORMAT( create_on, '%d/%m/%Y' )")
		];
		return $columns;
	}
 
	public function fetch($request){
		//$this->logger->addInfo('Request Npp path');
		$rsData = array(
			'status' => self::ERROR_STATUS,
			'message' => 'Chưa có dữ liệu từ hệ thống!'
		);
		// Columns to select.
		$columns = $this->getColumns();
		$collection = $this->db->select($this->tableName, $columns, [
			"status" => 1,
			"ORDER" => ["id" => "DESC"],
		]);
		if(!empty($collection)) {
			$rsData['status'] = self::SUCCESS_STATUS;
			$rsData['message'] = 'Dữ liệu đã được load!';
			$rsData['data'] = $collection;
		}
		header("Content-Type: application/json");
    echo json_encode($rsData, JSON_UNESCAPED_UNICODE);
    exit;
	}
	public function fetchSelectedProduct($request, $response, $args) {
		//$this->logger->addInfo('Request Npp path');
		$rsData = array(
			'status' => self::ERROR_STATUS,
			'message' => 'Chưa load được sản phẩm của phiếu!'
		);
		$ma_order = isset(	$args['ma_order']) ? $args['ma_order'] : '';
		// Columns to select.
		$columns = [
			'lotus_request_order_product.id',
			'lotus_request_order_product.id(key)',//For unique react item
			'lotus_request_order_product.ma_order',
			'lotus_request_order_product.product_id',
			'lotus_request_order_product.qty',
			'lotus_request_order_product.status',
			'lotus_request_order_product.create_on',
			'lotus_vattu.name'
		];
		$collection = $this->db->select('lotus_request_order_product', 
			[
				"[>]lotus_vattu" => ["product_id" => "product_id"],
			], 
			$columns, [
			"lotus_request_order_product.status" => 1,
			"lotus_request_order_product.ma_order" => $ma_order
		]);
		if(!empty($collection)) {
			$rsData['status'] = self::SUCCESS_STATUS;
			$rsData['message'] = 'Dữ liệu sản phẩm đã được load!';
			$rsData['data'] = $collection;
		}
		header("Content-Type: application/json");
    echo json_encode($rsData, JSON_UNESCAPED_UNICODE);
    exit;
	}
	private function saveProductOfBill($products, $maOrder, $createOn, $isEdit = false) {		
		$validProducts = [];
		if($isEdit) {
			//Delete old product belong to bill 
			$oldProducts = $this->db->select('lotus_request_order_product', ['id','ma_order'], ['ma_order' => $maOrder]);
			if(!empty($oldProducts)) {
				$oldIds = [];
				foreach ($oldProducts as $key => $item) {
					$oldIds[] = $item['id'];
				}
				$this->db->update('lotus_request_order_product', ['status' => 2], ['id' => $oldIds]);
			}
		}
		$userId = isset($this->jwt->id) ? $this->jwt->id : '';
		foreach($products as $product) {
			$validProducts[] = array(
				'ma_order' => $maOrder,
				'product_id' => $product['product_id'],
				'qty' => $product['qty'],
				'create_on' => $createOn
			);
		}
		$result = $this->db->insert('lotus_request_order_product', $validProducts);
		return $result;
	}
	public function update($request, $response)
	{
		$rsData = array(
			'status' => self::ERROR_STATUS,
			'message' => 'Xin lỗi! Dữ liệu chưa được cập nhật thành công!'
		);
		// Get params and validate them here.
		$id = $request->getParam('id');
		$params = $request->getParams();
		$ma_order = isset($params['ma_order']) ? $params['ma_order'] : '';
		$date_delivery = isset($params['date_delivery']) ? $params['date_delivery'] : '';
		$ma_kh = isset($params['ma_kh']) ? $params['ma_kh'] : '';
		$note = isset($params['note']) ? $params['note'] : '';
		$filename = isset($params['filename']) ? $params['filename'] : '';
		$products = (isset($params['products']) && !empty($params['products'])) ? $params['products'] : [];
		//Some validation 
		if(empty($products)) {
			$rsData['message'] = 'Không có sản phẩm nào trong đơn hàng!';
				echo json_encode($rsData);
				die;
		}
		if(!$ma_order) {
			$rsData['message'] = 'Mã đơn hàng không được để trống!';
				echo json_encode($rsData);
				die;
		}
		$userId = isset($this->jwt->id) ? $this->jwt->id : '';
		$date = new \DateTime();
		$createOn = $date->format('Y-m-d H:i:s');
		$duLieuPhieu = array(
			'ma_order' => $ma_order,
			'ma_kh' => $ma_kh,
			'date_delivery' => isset($params['date_delivery']) ? $params['date_delivery'] : '',
			'note' => isset($params['note']) ? $params['note'] : '',
			'tinh_trang' => isset($params['tinh_trang']) ? $params['tinh_trang'] : '',
			'filename' => $filename,
			'status' => 1
		);
		if(!$id) {
			$uuid1 = Uuid::uuid1();
			//$ma_order = $uuid1->toString();

			//Tao phieu 
			$duLieuPhieu['create_on'] = $createOn;
			$duLieuPhieu['create_by'] = $userId;
			$selectColumns = ['id', 'ma_order'];
			$where = ['ma_order' => $duLieuPhieu['ma_order']];
			$data = $this->db->select($this->tableName, $selectColumns, $where);
			if(!empty($data)) {
				$rsData['message'] = "Mã đơn hàng [". $duLieuPhieu['ma_order'] ."] đã tồn tại: ";
				echo json_encode($rsData);exit;
			}
			$result = $this->db->insert($this->tableName, $duLieuPhieu);
			if($result->rowCount()) {
				$productsNum = $this->saveProductOfBill($products, $ma_order, $createOn, false);
				if($productsNum->rowCount()) {
					$rsData['status'] = 'success';
					$columns = $this->getColumns();
					$data = $this->db->select($this->tableName, $columns, ['ma_order' => $ma_order]);
					$rsData['data'] = $data[0];
					$rsData['message'] = 'Đã thêm đơn hàng thành công!';
				} else {
					$rsData['message'] = 'Dữ liệu chưa được cập nhật vào cơ sở dữ liệu!';
				}
			} else {
				// echo "<pre>";
				// print_r($result->errorInfo());
				$rsData['message'] = 'Không chèn được lệnh vào CSDL!';
			}
		} else {
			//update data base on $id
			$duLieuPhieu['update_on'] = $createOn;
			$duLieuPhieu['update_by'] = $userId;
			//Check user có cập nhật ma_order vào mã order đã tồn tại hay chưa 
			$selectColumns = ['id', 'ma_order'];
			$where = ['ma_order' => $duLieuPhieu['ma_order'], 'id[!]' => $id];
			$data = $this->db->select($this->tableName, $selectColumns, $where);
			if(!empty($data)) {
				$rsData['message'] = "Mã đơn hàng [". $duLieuPhieu['ma_order'] ."] đã tồn tại: ";
				echo json_encode($rsData);exit;
			}
			$result = $this->db->update($this->tableName, $duLieuPhieu, ['id' => $id]); 
			$this->superLog('Cập nhật đơn hàng', $duLieuPhieu);
			$productsNum = $this->saveProductOfBill($products, $ma_order, $createOn, true);
			if($productsNum->rowCount()) {
				$this->superLog('Cập nhật sản phẩm vào đơn hàng', $products);
				$rsData['status'] = self::SUCCESS_STATUS;
				$rsData['message'] = 'Dữ liệu đã được cập nhật vào hệ thống!';
			} else {
				$rsData['message'] = 'Chưa cập nhật được sản phẩm theo phiếu xuất!';
			}
		}
		echo json_encode($rsData);
	}
	public function updateProduct($request, $response)
	{
		$rsData = array(
			'status' => self::ERROR_STATUS,
			'message' => 'Xin lỗi! Dữ liệu chưa được cập nhật thành công!'
		);
		// Get params and validate them here.
		$id = $request->getParam('id');
		$params = $request->getParams();
		$maSp = isset($params['product_id']) ? $params['product_id'] : '';
		$ma_order = isset($params['ma_order']) ? $params['ma_order'] : '';	
		$date = new \DateTime();
		$createOn = isset($params['create_on']) ? $params['create_on'] : $date->format('Y-m-d H:i:s');
		$updateOn = $date->format('Y-m-d H:i:s');
		//Some validation 
		if(!$ma_order) {
			$rsData['message'] = 'Mã sản xuất không được để trống!';
				echo json_encode($rsData);
				die;
		}
		if(!$maSp) {
			$rsData['message'] = 'Mã VT không được để trống!';
				echo json_encode($rsData);
				die;
		}
		$userId = isset($this->jwt->id) ? $this->jwt->id : '';
		if(!$id) {
			$itemData = array(
				'ma_order' => $ma_order,
				'ma_maquet' => isset($params['ma_maquet']) ? $params['ma_maquet'] : '',
				'product_id' => $maSp,
				'cong_doan' => isset($params['cong_doan']) ? $params['cong_doan'] : '',
				'sl_1000' => isset($params['sl_1000']) ? $params['sl_1000'] : '',
				'sl_nvl' => isset($params['sl_nvl']) ? $params['sl_nvl'] : '',
				'hu_hao' => isset($params['hu_hao']) ? $params['hu_hao'] : '',
				'create_on' => $createOn
			);
			$result = $this->db->insert('lotus_request_order_product', $itemData);
			if($result->rowCount()) {
				$rsData['status'] = 'success';
				$id = $this->db->id();
				$rsData['data'] = array('id' => $id);
				$rsData['message'] = 'Đã thêm sản phẩm vào sản xuất thành công!';
			} else {
				$rsData['message'] = 'Dữ liệu chưa được cập nhật vào cơ sở dữ liệu!';
			}
		} else {
			//update data base on $id
			$itemData = [
				'ma_order' => $ma_order,
				'ma_maquet' => isset($params['ma_maquet']) ? $params['ma_maquet'] : '',
				'product_id' => isset($params['product_id']) ? $params['product_id'] : '',
				'sl_1000' => isset($params['sl_1000']) ? $params['sl_1000'] : '',
				'cong_doan' => isset($params['cong_doan']) ? $params['cong_doan'] : '',
				'sl_nvl' => isset($params['sl_nvl']) ? $params['sl_nvl'] : '',
				'hu_hao' => isset($params['hu_hao']) ? $params['hu_hao'] : '',
				'create_on' => $createOn,
				'update_on' => $updateOn
			];
			$result = $this->db->update('lotus_request_order_product', $itemData, ['id' => $id]);
			if($result->rowCount()) {
				$this->superLog('Update SP theo đơn hàng ', $itemData);
				$rsData['status'] = self::SUCCESS_STATUS;
				$rsData['message'] = 'Dữ liệu đã được cập nhật vào hệ thống!';
			} else {
				$rsData['message'] = 'Dữ liệu chưa được cập nhật vào hệ thống!';
			}
		}
		echo json_encode($rsData);
	}

	public function delete($request, $response, $args){
		$rsData = array(
			'status' => self::ERROR_STATUS,
			'message' => 'Dữ liệu chưa được xoá thành công!'
		);
		// Get params and validate them here.
		$id = isset(	$args['id']) ? $args['id'] : '';
		if($id != "") {
			$result = $this->db->update($this->tableName,[
				'status' => 2,
			], ['id' => $id]);
			if($result->rowCount()) {
				$this->superLog('Delete Đơn hàng', $id);
				$rsData['status'] = self::SUCCESS_STATUS;
				$rsData['message'] = 'Đã xoá đơn hàng khỏi hệ thống!';
				$rsData['data'] = $id;
			}
		} else {
			$rsData['message'] = 'ID trống, nên không xoá được dữ liệu!';
		}
		echo json_encode($rsData);
	}
	public function deleteProduct($request, $response, $args){
		$rsData = array(
			'status' => self::ERROR_STATUS,
			'message' => 'Dữ liệu chưa được xoá thành công!'
		);
		// Get params and validate them here.
		$id = isset(	$args['id']) ? $args['id'] : '';
		if($id != "") {
			$result = $this->db->update('lotus_request_order_product',[
				'status' => 2,
			], ['id' => $id]);
			if($result->rowCount()) {
				$this->superLog('Delete Sản Phẩm của đơn hàng', $id);
				$rsData['status'] = self::SUCCESS_STATUS;
				$rsData['message'] = 'Đã xoá sản phẩm khỏi phiếu nhập!';
				$rsData['data'] = $id;
			}
		} else {
			$rsData['message'] = 'ID trống, nên không xoá được dữ liệu!';
		}
		echo json_encode($rsData);
	}
	public function fetchProductDetailsList($request){
		$rsData = array(
			'status' => self::ERROR_STATUS,
			'message' => 'Chưa có dữ liệu từ hệ thống!'
		);
		// Columns to select.
		$columns = [
				'lotus_vattu.id',
				'lotus_vattu.product_id',
				'lotus_vattu.category_id',
				'lotus_vattu.name',
				//'price',
				'lotus_vattu.unit',
				'lotus_vattu.min',
				'lotus_vattu.max',
				'lotus_cats.name(category_name)'
		];
		$collection = $this->db->select('lotus_vattu', [
			"[>]lotus_cats" => ["category_id" => "id"],
		], $columns, [
			"lotus_vattu.status" => 1
		]);
		if(!empty($collection)) {
			$rsData['status'] = self::SUCCESS_STATUS;
			$rsData['message'] = 'Dữ liệu đã được load!';
			$rsData['data'] = $collection;
		}
		header("Content-Type: application/json");
        echo json_encode($rsData, JSON_UNESCAPED_UNICODE);
        exit;
	}
	public function fetchProductByCate($request, $response, $args){
		$rsData = array(
			'status' => self::ERROR_STATUS,
			'message' => 'Chưa có dữ liệu từ hệ thống!'
		);
		$cate_id = isset(	$args['cate_id']) ? $args['cate_id'] : '';
		// Columns to select.
		$columns = [
				'products.id',
				'products.product_id',
				'products.category_id',
				'products.name',
				//'price',
				'products.unit',
				'products.min',
				'products.max',
				'lotus_cats.name(category_name)'
		];
		$collection = $this->db->select('products', [
			"[>]lotus_cats" => ["category_id" => "id"],
		], $columns, [
			"products.status" => 1,
			"products.category_id" => $cate_id
		]);
		if(!empty($collection)) {
			$rsData['status'] = self::SUCCESS_STATUS;
			$rsData['message'] = 'Dữ liệu đã được load!';
			$rsData['data'] = $collection;
		}
		header("Content-Type: application/json");
        echo json_encode($rsData, JSON_UNESCAPED_UNICODE);
        exit;
	}

}
