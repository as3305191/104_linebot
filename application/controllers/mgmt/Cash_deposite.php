<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Cash_deposite extends MY_Base_Controller {

	function __construct() {
		parent::__construct();
		$this -> load -> model('Cash_deposite_dao', 'dao');
		$this -> load -> model('Wallet_tx_dao', 'wtx_dao');
		$this -> load -> model('Config_dao', 'config_dao');
		$this -> load -> model('Users_dao', 'u_dao');
		$this -> load -> model('Corp_dao', 'c_dao');
		$this -> load -> model('Banks_dao', 'banks_dao');
	}

	public function index()
	{
		$data = array();
		$data = $this -> setup_user_data($data);
		$data['status_list'] = $this -> dao -> find_all_status();

		$login_user = $this -> u_dao -> find_by_id($data['login_user_id']);
		$data['login_user'] = $login_user;
		$data['corp_list'] = $this -> c_dao -> find_all();
		$this->load->view('mgmt/cash_deposite/check_list', $data);
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

		$data['bank_list'] = $this -> banks_dao -> find_all_by_country(0);

		$data['sum_amt'] = $this -> wtx_dao -> get_sum_amt($item -> user_id);
		$this->load->view('mgmt/cash_deposite/check_edit', $data);
	}

	public function insert() {
		$res = array();
		$id = $this -> get_post('id');
		$s_data = $this -> setup_user_data(array());
		$status = $this -> get_post('status');

		$w_item = $this -> dao -> find_by_id($id);

		$error = array();

		if(count($error) == 0 && $status == 2) {
			$this -> dao -> db -> trans_begin();

			$u_data['status'] = $status;
			$this -> dao -> update($u_data, $id);

			// withdraw tx
			$tx = array();
			$tx['cash_deposite_id'] = $id;
			$tx['corp_id'] = $w_item -> corp_id; // corp id
			$tx['user_id'] = $w_item -> user_id;
			$tx['amt'] = ($w_item -> amt);

			$tx['type_id'] = 7; // 請款
			$tx['brief'] = "已存入 $w_item->amt";
			$this -> wtx_dao -> insert($tx);

			$cd_list = $this -> wtx_dao -> find_all_by('cash_deposite_id', $id);
			if (count($cd_list) > 1)
				{
				  $this->dao->db->trans_rollback();
				}
				else
				{
				  $this->dao->db->trans_commit();
				}
		} else {
			$this -> dao -> db -> trans_start();

			// update status
			$u_data['status'] = $status;
			$this -> dao -> update($u_data, $id);

			if($status == 2) {
				$res['error_msg'] = join(',', $error);
			}

			if($status == -1) {
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
				// 	$tx['brief'] = "取消提款, 返回鎖定點數 $w_item->amt 點";
				// 	$this -> wtx_dao -> insert($tx);
				// }
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

	public function test() {
		echo "test....123 <br/>";
		$this -> dao -> db -> trans_begin();

		$tx = array();
		$tx['cash_deposite_id'] = -111;
		$tx['corp_id'] = 0; // corp id
		$tx['user_id'] = 0;
		$tx['amt'] = 0;

		$tx['type_id'] = 7; // 請款
		$tx['brief'] = "已存入 0";
		$this -> wtx_dao -> insert($tx);

		$list = $this -> wtx_dao -> find_all_by('cash_deposite_id', -111);

		if (count($list) > 1)
			{
				echo "trans_rollback  <br/>";
			        $this->dao->db->trans_rollback();
			}
			else
			{
				echo "trans_commit  <br/>";
			        $this->dao->db->trans_commit();
			}
	}
}
