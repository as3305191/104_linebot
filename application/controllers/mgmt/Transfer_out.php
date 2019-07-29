<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Transfer_out extends MY_Base_Controller {

	function __construct() {
		parent::__construct();
		$this -> load -> model('Transfer_dao', 'dao');
		$this -> load -> model('Wallet_tx_dao', 'wtx_dao');
		$this -> load -> model('Config_dao', 'config_dao');
		$this -> load -> model('Users_dao', 'users_dao');
	}

	public function index()
	{
		$data = array();
		$data = $this -> setup_user_data($data);
		$data['sum_amt'] = $this -> wtx_dao -> get_sum_amt($data['login_user_id']);
		$this->load->view('mgmt/transfer/out_list', $data);
	}

	public function get_data() {
		$res = array();
		$data = $this -> get_posts(array(
			'length',
			'start',
			'columns',
			'search',
			'order',
			'out_user_id'
		));

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

		$s_data = $this -> setup_user_data(array());
		$data['config'] = $this -> config_dao -> get_item_by_corp($s_data['corp'] -> id);

		$data['sum_amt'] = $this -> wtx_dao -> get_sum_amt($s_data['login_user_id']);
		$this->load->view('mgmt/transfer/out_edit', $data);
	}

	public function insert() {
		$res = array();
		$id = $this -> get_post('id');
		$in_account = $this -> get_post('in_account');
		$s_data = $this -> setup_user_data(array());
		$data = $this -> get_posts(array(
			'amt',
			'ope_percent',
			'ope_amt',
			'sms_amt',
			'result_amt'
		));

		$user = $this -> users_dao -> find_by_group_and_account($s_data['corp'] -> id, $in_account);
		$data['in_user_id'] = $user -> id;
		$data['out_user_id'] = $s_data['login_user_id'];

		if(empty($id)) {
			// insert
			$data['corp_id'] = $s_data['corp'] -> id;
			$data['sn'] = 'T' . date('YmdHis');
			$id = $this -> dao -> insert($data);
		} else {
			// update
			// $this -> dao -> update($data, $id);
		}

		$res['success'] = TRUE;
		$res['last_id'] = $id;
 		$this -> to_json($res);
	}

	public function delete($id) {
		$res['success'] = TRUE;
		$this -> dao -> delete_status($id, $this -> session -> userdata('user_id'));
		$this -> to_json($res);
	}


	public function confirm_code() {
		$res = array();
		$id = $this -> get_post('id');
		$code = $this -> get_post('code');
		$u_data = array();

		// send to out user
		$item = $this -> dao -> find_me($id);
		$out_user = $this -> users_dao -> find_by_id($item -> out_user_id);

		$error = array();
		if($item -> code != $code) {
			$error[] = "驗證碼錯誤";
		}

		$sum_amt = $this -> wtx_dao -> get_sum_amt($item -> out_user_id);
		if($sum_amt < $item -> amt) {
			$error[] = "餘額不足";
		}

		if(count($error) == 0) {
			// no error
			$u_data['status'] = 2;
			$this -> dao -> update($u_data, $id);

			// out tx
			$tx = array();
			$tx['transfer_id'] = $id;
			$tx['corp_id'] = $item -> corp_id; // corp id
			$tx['user_id'] = $item -> out_user_id;
			$tx['amt'] = -($item -> amt);

			$tx['type_id'] = 12; // 轉帳匯出
			$tx['brief'] = "轉帳至 $item->in_account 共 $item->result_amt 點, 花費點數 $item->amt 點";
			$this -> wtx_dao -> insert($tx);

			// in tx
			$tx = array();
			$tx['transfer_id'] = $id;
			$tx['corp_id'] = $item -> corp_id; // corp id
			$tx['user_id'] = $item -> in_user_id;
			$tx['amt'] = ($item -> result_amt);

			$tx['type_id'] = 13; // 轉帳匯入
			$tx['brief'] = "$item->out_account 轉帳 $item->result_amt 點, 花費點數 $item->amt 點";
			$this -> wtx_dao -> insert($tx);
 		} else {
			$res['error_msg'] = join(',', $error);
		}

		$res['success'] = TRUE;
 		$this -> to_json($res);
	}

	public function check_account() {
		$res = array();
		$account = $this -> get_post('in_account');
		$list = $this -> users_dao -> find_all_by('account', $account);

		$corp = $this -> session -> userdata('corp');
		$contain = FALSE;
		foreach($list as $each) {
			if($each -> corp_id == $corp -> id) {
				$contain = TRUE;
			}
		}
		$res['valid'] = $contain;
		$res['list'] = $list;
		$res['account'] = $account;
		$this -> to_json($res);
	}
}
