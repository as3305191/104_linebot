<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Buy_records extends MY_Base_Controller {

	function __construct() {
		parent::__construct();
		$this -> load -> model('Buy_records_dao', 'dao');
		$this -> load -> model('Buy_records_status_dao', 'brs_dao');
		$this -> load -> model('Products_dao', 'p_dao');
		$this -> load -> model('Pay_types_dao', 'pt_dao');

		$this -> load -> model('Bonus_tx_dao', 'tx_dao');
		$this -> load -> model('Users_dao', 'u_dao');
	}

	public function index()
	{
		$data = array();
		$data = $this -> setup_user_data($data);
		$data['status_list'] = $this -> brs_dao -> find_all();
		$this->load->view('mgmt/buy_records/list', $data);
	}

	public function get_data() {
		$res = array();
		$data = $this -> get_posts(array(
			'length',
			'start',
			'columns',
			'search',
			'order',
			'user_id',
			'status'
		));

		$s_data = $this -> setup_user_data(array());
		$data['corp_id'] = $s_data['corp'] -> id;

		$res['items'] = $this -> dao -> query_ajax($data);
		$res['recordsFiltered'] = $this -> dao -> count_ajax($data);
		$res['recordsTotal'] = $this -> dao -> count_all_ajax($data);

		$this -> to_json($res);
	}

	public function edit($id) {
		$data = array();
		$data['id'] = $id;
		if(!empty($id)) {
			$q_data = $this -> get_posts(array(
				'length',
				'start',
				'columns',
				'search',
				'order'
			));
			$q_data['id'] = $id;
			$list = $this -> dao -> query_ajax($q_data);
			$item = $list[0];

			$data['item'] = $item;
		}

		$data['product_list'] = $this -> p_dao -> find_all();
		$data['pay_type_list'] = $this -> pt_dao -> find_all();

		$this->load->view('mgmt/buy_records/edit', $data);
	}

	public function insert() {
		$res = array();
		$id = $this -> get_post('id');
		$s_data = $this -> setup_user_data(array());
		$data = $this -> get_posts(array(
			'product_id',
			'pay_type_id'
		));
		$data['user_id'] = $s_data['login_user_id'];

		if(empty($id)) {
			// insert
			$product = $this -> p_dao -> find_by_id($data['product_id']);
			$data['total_price'] =  $product -> price;
			$data['sn'] = 'B' . date('YmdHis');
			$this -> dao -> insert($data);
		} else {
			// update
			$u_data = array();
			$u_data['status'] = "1";
			$this -> dao -> update($u_data, $id);

			// update user date
			$br = $this -> dao -> find_by_id($id);
			$product = $this -> p_dao -> find_by_id($br -> product_id);
			$hours = $product -> hours;

			$uu_data = array();
			$new_time = date("Y-m-d H:i:s", strtotime("+$hours hours"));
			if(empty($br -> end_time)) {
				$uu_data['end_time'] = $new_time;
			} else {
				if(strtotime($br -> end_time) < time()) {
					// deprecated
					$uu_data['end_time'] = $new_time;
				} else {
					$new_date= date("Y-m-d H:i:s", strtotime($br -> end_time . " +$hours hours"));
					$uu_data['end_time'] = $new_time;
				}
			}
			$this -> u_dao -> update($uu_data, $br -> user_id);

			// commit tx
			$b_user = $this -> u_dao -> find_by_id($br -> user_id); // buy user

			// intro
			$tx = array();
			$tx['buy_record_id'] = $id;
			$tx['user_id'] = $b_user -> intro_id;
			$tx['total_price'] = $br -> total_price;
			$tx['percent'] = 20;
			$tx['amt'] = $br -> total_price * 20.0 / 100.0;
			$this -> tx_dao -> insert($tx);

			// manager
			$tx = array();
			$tx['buy_record_id'] = $id;
			$tx['user_id'] = $b_user -> manager_id;
			$tx['total_price'] = $br -> total_price;
			$tx['percent'] = 10;
			$tx['amt'] = $br -> total_price * 10.0 / 100.0;
			$this -> tx_dao -> insert($tx);
		}

		$res['success'] = TRUE;
 		$this -> to_json($res);
	}

	public function delete($id) {
		$res['success'] = TRUE;
		$this -> dao -> delete_status($id, $this -> session -> userdata('user_id'));
		$this -> to_json($res);
	}

	public function check_account($id) {
		$account = $this -> get_post('account');
		$list = $this -> dao -> find_all_by('account', $account);
		$res = array();
		if(!empty($id)) {
			if (count($list) > 0) {
				$item = $list[0];
				if($item -> id == $id) {
					$res['valid'] = TRUE;
				} else {
					$res['valid'] = FALSE;
				}

				$res['item'] = $item;
			} else {
				$res['valid'] = TRUE;
			}
		} else {
			if (count($list) > 0) {
				$res['valid'] = FALSE;
			} else {
				$res['valid'] = TRUE;
			}
		}

		$this -> to_json($res);
	}

	public function chg_user() {
		$user_id = $this -> get_post('user_id');
		$this -> session -> set_userdata('user_id', $user_id);
		$res = array();

		$this -> to_json($res);
	}
}
