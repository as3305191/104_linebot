<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Withdraw extends MY_Base_Controller {

	function __construct() {
		parent::__construct();
		$this -> load -> model('Withdraw_dao', 'dao');
		$this -> load -> model('Wallet_tx_dao', 'wtx_dao');
		$this -> load -> model('Config_dao', 'config_dao');
		$this -> load -> model('Users_dao', 'u_dao');
	}

	public function index()
	{
		$data = array();
		$data = $this -> setup_user_data($data);
		$data['sum_amt'] = $this -> wtx_dao -> get_sum_amt($data['login_user_id']);
		$this->load->view('mgmt/withdraw/list', $data);
	}

	public function get_data() {
		$res = array();
		$data = $this -> get_posts(array(
			'length',
			'start',
			'columns',
			'search',
			'order',
			'user_id'
		));

		$res['items'] = $this -> dao -> query_ajax($data);
		$res['recordsFiltered'] = $this -> dao -> count_ajax($data);
		$res['recordsTotal'] = $this -> dao -> count_all_ajax($data);

		$s_data = $this -> setup_user_data(array());
		$res['sum_amt'] = $this -> wtx_dao -> get_sum_amt($s_data['login_user_id']);

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
		$this->load->view('mgmt/withdraw/edit', $data);
	}

	public function insert() {
		$res = array();
		$id = $this -> get_post('id');
		$s_data = $this -> setup_user_data(array());
		$data = $this -> get_posts(array(
			'amt',
			'ope_percent',
			'ope_amt',
			'transfer_amt',
			'result_amt'
		));
		$data['user_id'] = $s_data['login_user_id'];
		// find user
		$l_user = $this -> u_dao -> find_by_id($s_data['login_user_id']);

		if(empty($id)) {
			$this -> dao -> db -> trans_start();

			// insert
			$data['corp_id'] = $s_data['corp'] -> id;
			$data['sn'] = 'W' . date('YmdHis');
			$id = $this -> dao -> insert($data);

			// insert tx
			// intro
			$tx = array();
			$tx['withdraw_id'] = $id;
			$tx['corp_id'] = $s_data['corp'] -> id; // corp id
			$tx['user_id'] = $s_data['login_user_id'];

			$amt = $data['amt'];
			$tx['amt'] = -$amt;
			$tx['type_id'] = 18; // 鎖定點數
			$tx['brief'] = "會員 $l_user->account 提款 鎖定點數 -$amt 點";
			$this -> wtx_dao -> insert($tx);

			$this -> dao -> db -> trans_complete();

			if ($this -> dao ->  db -> trans_status() === FALSE)
			{
				$res['error_msg'] = '訊息有誤';
			}
		} else {
			// update
			// $this -> dao -> update($data, $id);
		}

		$res['success'] = TRUE;
		$res['last_id'] = $id;
		$res['sum_amt'] = $this -> wtx_dao -> get_sum_amt($s_data['login_user_id']);
 		$this -> to_json($res);
	}

	public function delete($id) {
		$res['success'] = TRUE;
		$this -> dao -> delete_status($id, $this -> session -> userdata('user_id'));
		$this -> to_json($res);
	}
}
