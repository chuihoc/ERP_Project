<?php
namespace App\Controllers;
use \Medoo\Medoo;
use \Monolog\Logger;
//use \Ramsey\Uuid\Uuid;

class OrderController extends BaseController
{
	private $tableName = 'ans_orders';
	const ERROR_STATUS = 'error';
	const SUCCESS_STATUS = 'success';
 
	public function fetchDh($request){ 
		$rsData = array(
			'status' => self::ERROR_STATUS,
			'message' => 'Chưa có dữ liệu từ hệ thống!'
		);
		// Columns to select.
		$columns = [
				'ans_orders.id',
				'ans_orders.ma_order',
				'ans_orders.ma_kh',
				'ans_orders.product_id',
				'ans_orders.qty',
				'ans_orders.price',
				'ans_orders.note',
				'ans_orders.date_delive',
				'ans_orders.status',
				'ans_orders.create_on',
				'ans_khachhang.name(kh_name)',
				'ans_products.name(product_name)',
		];
		$collection = $this->db->select($this->tableName,[
			"[>]ans_khachhang" => ["ma_kh" => "ma_kh"],
			"[>]ans_products" => ["product_id" => "product_id"],
		] ,$columns, [
			//"ans_orders.status" => 1,
			"ORDER" => ["id" => "DESC"],
		]);
		if(!empty($collection)) {
			$rsData['status'] = self::SUCCESS_STATUS;
			$rsData['message'] = 'Dữ liệu đã được load!';
			$rsData['data'] = $collection;
		}
		echo json_encode($rsData);
	}
	public function updateDh($request, $response)
	{
		$rsData = array(
			'status' => self::ERROR_STATUS,
			'message' => 'Xin lỗi! Dữ liệu chưa được cập nhật thành công!'
		);
		
		// Get params and validate them here.
		//$params = $request->getParams();
		$id = $request->getParam('id');
		//die($id);
		$maDh = $request->getParam('ma_order');
		$maKh = $request->getParam('ma_kh');
		$pid = $request->getParam('product_id');
		$qty = $request->getParam('qty');
		$note = $request->getParam('note');
		$price = $request->getParam('price');
		$date_delive = $request->getParam('date_delive');
		if(!$id) {
			//Insert new data to db
			if(!$maDh) {
				$rsData['message'] = 'Mã đơn hàng không được để trống!';
				echo json_encode($rsData);
				die;
			}
			if(!$maKh) {
				$rsData['message'] = 'Mã khách hàng không được để trống!';
				echo json_encode($rsData);
				die;
			}
			$date = new \DateTime();
			$itemData = [
				'ma_order' => $maDh,
				'product_id' => $pid,
				'qty' => $qty,
				'note' => $note,
				'price' => $price,
				'date_delive' => $date_delive,
				'status' => 1,
				'ma_kh' => $maKh,
				'create_on' => $date->format('Y-m-d H:i:s'),
			];
			$selectColumns = ['id', 'ma_order'];
			$where = ['ma_order' => $itemData['ma_order']];
			$data = $this->db->select($this->tableName, $selectColumns, $where);
			if(!empty($data)) {
				$rsData['message'] = "Mã đơn hàng [". $itemData['ma_order'] ."] đã tồn tại: ";
				echo json_encode($rsData);exit;
			}
			$result = $this->db->insert($this->tableName, $itemData); 
			if($result->rowCount()) {
				$rsData['status'] = 'success';
				$rsData['message'] = 'Đã thêm đơn hàng mới thành công!';
				$data = $this->db->select($this->tableName, $selectColumns, $where);
				$rsData['data'] = $data[0];
			} else {
				$rsData['message'] = 'Dữ liệu chưa được cập nhật vào cơ sở dữ liệu! Có thể do bạn cập nhật trùng mã DH: ' . $maDh;
			}
		} else {
			//update data base on $id
			$date = new \DateTime();
			$itemData = [
				'ma_order' => $maDh,
				'product_id' => $pid,
				'qty' => $qty,
				'price' => $price,
				'date_delive' => $date_delive,
				'status' => 1,
				'note' => $note,
				'ma_kh' => $maKh,
				'create_on' => $date->format('Y-m-d H:i:s'),
			];
			$result = $this->db->update($this->tableName, $itemData, ['id' => $id]);
			if($result->rowCount()) {
				$this->superLog('Update DH', $itemData);
				$rsData['status'] = self::SUCCESS_STATUS;
				$rsData['message'] = 'Dữ liệu đã được cập nhật vào hệ thống!';
			} else {
				$rsData['message'] = 'Dữ liệu chưa được cập nhật vào cơ sở dữ liệu! Có thể do bạn cập nhật trùng mã DH: ' . $maDh;
			}
			
		}
		echo json_encode($rsData);
	}

	public function deleteDh($request, $response, $args){
		$rsData = array(
			'status' => self::ERROR_STATUS,
			'message' => 'Dữ liệu chưa được xoá thành công!'
		);
		// Get params and validate them here.
		$id = isset(	$args['id']) ? $args['id'] : '';
		if($id != "") {
			$result = $this->db->update($this->tableName,['status' => 0], ['id' => $id]);
			if($result->rowCount()) {
				$this->superLog('Delete KH', $id);
				$rsData['status'] = self::SUCCESS_STATUS;
				$rsData['message'] = 'Đã xoá đơn hàng khỏi hệ thống!';
				$rsData['data'] = $id;
			}
		} else {
			$rsData['message'] = 'ID trống, nên không xoá được dữ liệu!';
		}
		echo json_encode($rsData);
	}
}
