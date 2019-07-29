<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Withdraw_check extends MY_Base_Controller {

	function __construct() {
		parent::__construct();
		$this -> load -> model('Withdraw_dao', 'dao');
		$this -> load -> model('Wallet_tx_dao', 'wtx_dao');
		$this -> load -> model('Wallet_tx_ntd_dao', 'wtx_ntd_dao');
		$this -> load -> model('Config_dao', 'config_dao');
		$this -> load -> model('Users_dao', 'u_dao');
		$this -> load -> model('Corp_dao', 'c_dao');
	}

	public function index()
	{
		$data = array();
		$data = $this -> setup_user_data($data);
		$data['status_list'] = $this -> dao -> find_all_status();

		$login_user = $this -> u_dao -> find_by_id($data['login_user_id']);
		$data['login_user'] = $login_user;
		$data['corp_list'] = $this -> c_dao -> find_all();
		$this->load->view('mgmt/withdraw/check_list', $data);
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

		$login_user = $this -> u_dao -> find_by_id($s_data['login_user_id']);

		if($login_user -> role_id != 99) {
			$data['corp_id'] = $login_user -> corp_id;
		}

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
		
		$data['user'] = $this -> u_dao -> find_by_id($data['item'] -> user_id);

		$s_data = $this -> setup_user_data(array());

		$data['config'] = $this -> config_dao -> get_item_by_corp($s_data['corp'] -> id);

		$data['sum_amt'] = $this -> wtx_ntd_dao -> get_sum_amt($item -> user_id);
		$this->load->view('mgmt/withdraw/check_edit', $data);
	}

	public function insert() {
		$res = array();
		$id = $this -> get_post('id');
		$s_data = $this -> setup_user_data(array());
		$status = $this -> get_post('status');

		$w_item = $this -> dao -> find_by_id($id);

		$error = array();
		$sum_amt = $this -> wtx_dao -> get_sum_amt($w_item -> user_id);
		if($sum_amt < $w_item -> amt) {
			$error[] = "餘額不足";
		}

		if(count($error) == 0 && $status == 2) {
			$this -> dao -> db -> trans_start();

			$u_data['status'] = $status;
			$this -> dao -> update($u_data, $id);

			// withdraw tx
			// $tx = array();
			// $tx['withdraw_id'] = $id;
			// $tx['corp_id'] = $w_item -> corp_id; // corp id
			// $tx['user_id'] = $w_item -> user_id;
			// $tx['amt'] = -($w_item -> amt);
			//
			// $tx['type_id'] = 3; // 提款
			// $tx['brief'] = "提款金額 $w_item->result_amt, 消耗點數 $w_item->amt 點";
			// $this -> wtx_dao -> insert($tx);

			// withdraw tx 解除鎖定
			// $lock = $this -> wtx_dao -> find_withdraw_lock($id);
			// if(!empty($lock)) { // unlock
			// 	$tx = array();
			// 	$tx['withdraw_id'] = $id;
			// 	$tx['corp_id'] = $w_item -> corp_id; // corp id
			// 	$tx['user_id'] = $w_item -> user_id;
			// 	$tx['amt'] = ($w_item -> amt);
			//
			// 	$tx['type_id'] = 18; // 提款
			// 	$tx['brief'] = "確認提款, 返回鎖定點數 $w_item->amt 點";
			// 	$this -> wtx_dao -> insert($tx);
			// }

			$this -> dao -> db -> trans_complete();
		} else {
			$this -> dao -> db -> trans_start();

			// update status
			$u_data['status'] = $status;
			$this -> dao -> update($u_data, $id);

			if($status == 2) {
				$res['error_msg'] = join(',', $error);
			}

			if($status == -1) {
				$tx = array();
				$tx['withdraw_id'] = $id;
				$tx['corp_id'] = $w_item -> corp_id; // corp id
				$tx['user_id'] = $w_item -> user_id;
				$tx['amt'] = ($w_item -> amt);

				$tx['type_id'] = 4; // 返回預扣
				$tx['brief'] = "取消提款, 返回鎖定金額 $w_item->amt ";
				$this -> wtx_ntd_dao -> insert($tx);
			}

			$this -> dao -> db -> trans_complete();
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
