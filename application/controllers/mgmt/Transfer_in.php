<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Transfer_in extends MY_Base_Controller {

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
		$this->load->view('mgmt/transfer/in_list', $data);
	}

	public function get_data() {
		$res = array();
		$data = $this -> get_posts(array(
			'length',
			'start',
			'columns',
			'search',
			'order',
			'in_user_id'
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
		$this->load->view('mgmt/transfer/in_edit', $data);
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

	public function insert_in() {
		$res = array();
		$id = $this -> get_post('id');
		$u_data = array();
		$u_data['status'] = 1;

		// send to out user
		$item = $this -> dao -> find_by_id($id);
		$out_user = $this -> users_dao -> find_by_id($item -> out_user_id);
		if(!empty($out_user -> mobile)) {
			$reg_code = $this -> get_reg_code();
			$u_data['code'] = $reg_code;
			$this -> dao -> update($u_data, $id); // confirm

			$mobile = $out_user -> mobile;
			$msg=iconv("UTF-8","big5","轉帳單號 $item->sn 簡訊認證碼為 $reg_code ，此認證碼將於30分鐘後失效。");
			$n_res = $this -> curl -> simple_get("http://smexpress.mitake.com.tw/SmSendGet.asp"
				. "?username=0970632144&password=aaa123&dstaddr=$mobile&DestName=SteveYeh&dlvtime=&vldtime=&smbody=$msg");
		} else {
			$res['error_msg'] = "轉帳會員尚未設定手機號碼";
		}

		$res['success'] = TRUE;
 		$this -> to_json($res);
	}

	private function get_reg_code() {
		$digits = 4;
		return rand(pow(10, $digits - 1), pow(10, $digits) - 1);
	}

	public function delete($id) {
		$res['success'] = TRUE;
		$this -> dao -> delete_status($id, $this -> session -> userdata('user_id'));
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
