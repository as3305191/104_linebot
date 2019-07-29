<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Transaction_record extends MY_Base_Controller {

	function __construct() {
		parent::__construct();
		$this -> load -> model('Transaction_record_props_dao', 'transaction_record_props_dao');


	}

	public function index()
	{
		$data = array();
		$this -> setup_user_data($data);

		// $this -> to_json($data);

		$this->load->view('mgmt/transaction_record/list', $data);
	}

	public function get_data() {
		$res = array();
		// $data = $this -> get_posts(array(
		// 	'length',
		// 	'start',
		// 	'columns',
		// 	'search',
		// 	'order',
		// 	'dt',
		// 	'e_dt',
		// 	'station_id',
		// 	'multiple',
		// 	'bypass_101',
		// ));
		//
		// $data['no_limit'] = TRUE;

		// $this -> session -> set_userdata('Production_daily_product_station_report_data_v2', $data);
		$items = $this -> transaction_record_props_dao -> transaction_record();

		$res['items'] = $items;
		$this -> to_json($res);
	}

	public function edit($id) {
		$data = array();
		$data['id'] = $id;

		$item = NULL;
		if(!empty($id)) {
			$q['id'] = $id;
			$items = $this -> dao -> query_ajax($q);
			$item = $items[0];
			$data['member'] = $this -> m_dao -> find_by_id($item -> member_id);
			$data['item'] = $item;

			$data['s_log_list'] = $this -> status_log_dao -> find_all_by_order_id($id);
			$data['p_log_list'] = $this -> ps_log_dao -> find_all_by_order_id($id);
		}

		$this->load->view('mgmt/production_daily_product_station_2/edit', $data);
	}

	function export_all() {
			$this->load->dbutil();
      $this->load->helper('file');
      $this->load->helper('download');
      $delimiter = ",";
      $newline = "\r\n";
			$date = date('YmdHis');
      $filename = $date."-pruduction-daily-product.csv";

			$data = $this -> session -> userdata("Production_daily_product_station_report_data_v2");

			//create a file pointer
    	$f = fopen('php://memory', 'w');
			$fields = array(
				iconv("UTF-8","Big5//IGNORE",'開始時間'),
				!empty($data['dt'])? $data['dt']: '',
				iconv("UTF-8","Big5//IGNORE",'結束時間'),
				!empty($data['e_dt'])? $data['e_dt']: '',
			);
			fputcsv($f, $fields, $delimiter);

			$fields = array(
				iconv("UTF-8","Big5//IGNORE",'站別'),
				iconv("UTF-8","Big5//IGNORE",'料號'),
				iconv("UTF-8","Big5//IGNORE",'品名'),
				iconv("UTF-8","Big5//IGNORE",'工單'),
				iconv("UTF-8","Big5//IGNORE",'件數'),
				iconv("UTF-8","Big5//IGNORE",'重量(KG)'),
				iconv("UTF-8","Big5//IGNORE",'日期'),
				iconv("UTF-8","Big5//IGNORE",'barcode製造日期'),

			);
			fputcsv($f, $fields, $delimiter);

      $result = $items = $this -> production_service_v2 -> sum_order_station_record_group_by_station($data);
			$sum_weight = 0;
			$sum_number = 0;
			foreach($result as $each) {
				$lineData = array(
					iconv("UTF-8","Big5//IGNORE",$each -> station_name),
					$each -> lot_number,
					iconv("UTF-8","Big5//IGNORE",$each -> product_name),
					iconv("UTF-8","Big5//IGNORE",$each -> sn),
					$each -> sum_number,
					$each -> sum_weight,
					$each -> create_date,
					$each -> psn,
				);
				$sum_number += floatval($each -> sum_number);
				$sum_weight += floatval($each -> sum_weight);
				fputcsv($f, $lineData, $delimiter);
			}
			// print sum
			$lineData = array(
				"",
				iconv("UTF-8","Big5//IGNORE","總計"),
				$sum_number,
				$sum_weight,
			);
			fputcsv($f, $lineData, $delimiter);

			//move back to beginning of file
    	fseek($f, 0);

			//set headers to download file rather than displayed
		  header('Content-Type: text/csv');
		  header('Content-Disposition: attachment; filename="' . $filename . '";');

			//output all remaining data on a file pointer
			fpassthru($f);
	}
}
