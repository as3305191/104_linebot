<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Orders extends MY_Base_Controller {

	function __construct() {
		parent::__construct();
		$this -> load -> model('Orders_dao', 'dao');
		$this -> load -> model('Wallet_tx_dao', 'wtx_dao');

		$this -> load -> model('Images_dao', 'img_dao');
	}

	public function index()
	{
		$data = array();
		$data = $this -> setup_user_data($data);
		$this->load->view('mgmt/orders/list', $data);
	}

	public function get_data() {
		$res = array();
		$data = $this -> get_posts(array(
			'length',
			'start',
			'columns',
			'search',
			'order',
			'company_id',
			'status_filter',
			'pay_status_filter',

		));

		$items = $this -> dao -> query_ajax($data);
		$res['items'] = $items;

		$res['recordsFiltered'] = $this -> dao -> count_ajax($data);
		$res['recordsTotal'] = $this -> dao -> count_all_ajax($data);

		$res['status_cnt'] = $this -> dao -> count_all_status();

		$this -> to_json($res);
	}

	public function edit($id) {
		$data = array();
		$data['id'] = $id;

		$item = NULL;
		if(!empty($id)) {
			$items = $this -> dao -> find_all_me($id);
			$data['item'] = $items[0];
		}

		$this->load->view('mgmt/orders/edit', $data);
	}

	public function get_product_list() {
		$res = array();

		$order_id = $this -> get_post('order_id');
		$items = $this -> od_dao -> find_all_by_order_id($order_id);
		foreach($items as $item) {
			if(!empty($item -> image_id)) {
				// $item -> img_url = get_img_url($item -> image_id);
			}
		}
		$res['items'] = $items;
		$this -> to_json($res);
	}

	public function get_status_log() {
		$res = array();

		$order_id = $this -> get_post('order_id');
		$items = $this -> status_log_dao -> find_all_by_order_id($order_id);
		$res['items'] = $items;
		$this -> to_json($res);
	}

	public function do_cancel() {
		$res = array();

		$id = $this -> get_post('id');
		$items = $this -> dao -> find_all_me($id);

		$o = array();
		$s_data = $this -> setup_user_data($o);

		foreach($items as $each) {
			$this -> dao -> update(array('status' => -1), $each -> id);

			if($each -> pay_status == 1) {
				// commit tx
				$tx = array();
				$tx['order_id'] = $each -> id;
				$tx['user_id'] = $each -> user_id;
				$tx['corp_id'] = $each -> corp_id;

				$p_amt = $each -> product_amt;
				$tx['amt'] = $p_amt;
				$tx['type_id'] = 39; // 退款

				$tx['brief'] = "商品退款 $each->product_name DBC數量 $p_amt";
				$this -> wtx_dao -> insert($tx);

				// update pay status
				$this -> dao -> update(array('pay_status' => -1), $each -> id);
			}
			$res['id'] = $each -> id;
		}
		$this -> to_json($res);
	}

	public function do_shipping() {
		$res = array();

		$id = $this -> get_post('id');
		$status = $this -> get_post('status');
		$items = $this -> dao -> find_all_me($id);
		foreach($items as $each) {
			$this -> dao -> update(array('shipping_status' => $status), $each -> id);
			$res['id'] = $each -> id;
		}
		$this -> to_json($res);
	}

	public function insert() {
		$res = array();
		$id = $this -> get_post('id');
		$data = $this -> get_posts(array(
			'receive_name',
			'receive_phone',
			'receive_zip',
			'receive_address'
		));


		if(empty($id)) {
			// insert
			$id = $this -> dao -> insert($data);
		} else {
			// update
			$this -> dao -> update($data, $id);
		}

		$res['success'] = TRUE;
 		$this -> to_json($res);
	}

	public function delete($id) {
		$res['success'] = TRUE;
		$this -> dao -> delete_status($id, $this -> session -> userdata('user_id'));
		$this -> to_json($res);
	}
}
