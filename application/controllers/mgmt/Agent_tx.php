<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Agent_tx extends MY_Base_Controller {

	function __construct() {
		parent::__construct();
		$this -> load -> model('Agent_tx_dao', 'dao');
		$this -> load -> model('Wallet_tx_dao', 'wtx_dao');
		$this -> load -> model('Wallet_tx_btc_dao', 'wtx_btc_dao');
		$this -> load -> model('Wallet_tx_eth_dao', 'wtx_eth_dao');
		$this -> load -> model('Wallet_tx_ntd_dao', 'wtx_ntd_dao');

		$this -> load -> model('Config_dao', 'config_dao');
		$this -> load -> model('Users_dao', 'users_dao');
		$this -> load -> model('Corp_dao', 'corp_dao');
	}

	public function index()
	{
		$data = array();
		$data = $this -> setup_user_data($data);
		$data['login_user'] = $this -> users_dao -> find_by_id($data['login_user_id']);

		$data['sum_amt'] = $this -> wtx_dao -> get_sum_amt($data['login_user_id']);

		$data['corp_list'] = $this -> corp_dao -> find_all_company();

		$this->load->view('mgmt/agent_tx/tx_list', $data);
	}

	public function get_data() {
		$res = array();
		$data = $this -> get_posts(array(
			'length',
			'start',
			'columns',
			'search',
			'order',
			'start_date',
			'end_date',
			'search_corp_id',
			'user_id'
		));

		$s_data = $this -> setup_user_data(array());
		$login_user = $this -> users_dao -> find_by_id($s_data['login_user_id']);

		$data['role_id'] = $login_user -> role_id;
		$data['corp_id'] = $login_user -> corp_id;
		// $data['user_id'] = $login_user -> id;

		$res['items'] = $this -> dao -> query_ajax($data);
		$res['recordsFiltered'] = $this -> dao -> count_ajax($data);
		$res['recordsTotal'] = $this -> dao -> count_all_ajax($data);

		$s_amt = $this -> dao -> sum_ajax($data);
		$res['sum_amt_ntd'] = $s_amt;

		// $sum_list = $this -> wtx_ntd_dao -> query_ajax($data, TRUE);
		$res['sum_amt_range'] = 0;

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
		$data['l_user'] = $s_data['l_user'];

		$data['corp_list'] = $this -> corp_dao -> find_all_company();

		$data['sum_amt'] = $this -> wtx_dao -> get_sum_amt($s_data['login_user_id']);
		$data['sum_amt_btc'] = $this -> wtx_btc_dao -> get_sum_amt($s_data['login_user_id']);
		$data['sum_amt_eth'] = $this -> wtx_eth_dao -> get_sum_amt($s_data['login_user_id']);
		$data['sum_amt_ntd'] = $this -> wtx_ntd_dao -> get_sum_amt($s_data['login_user_id']);
		$this->load->view('mgmt/agent_tx/tx_edit', $data);
	}

	public function insert() {
		$res = array();
		$id = $this -> get_post('id');
		$in_account = $this -> get_post('in_account');
		$s_data = $this -> setup_user_data(array());
		$data = $this -> get_posts(array(
			'amt',
			'in_user_id',
			'currency'
		));

		$user = $this -> users_dao -> find_by_id($data['in_user_id']);

		if(empty($id)) {
			// insert
			$data['corp_id'] = $s_data['corp'] -> id;
			$data['sn'] = 'T' . date('YmdHis');

			if($data['currency'] == 'btc') {
				$data['in_code'] = $user -> wallet_code_btc;
			}
			if($data['currency'] == 'eth') {
				$data['in_code'] = $user -> wallet_code_eth;
			}

			$data['type'] = 1; // 匯入

			if(empty($data['in_code'])) {
				$res['error_msg'] = '尚未設定錢包';
			} else {
				$id = $this -> dao -> insert($data);
				$corp = $s_data['corp'];
				$item = $this -> dao -> find_me($id);

				$m_sms_arr = explode(',', $corp -> manager_sms);
				foreach($m_sms_arr as $mobile) {
					$msg = "『接收』貨幣通知，會員 $user->account 接收 $item->currency_name 數量 $item->amt 確認單，請上線確認";
					$msg=iconv("UTF-8","big5",$msg);
					if(!empty($corp -> cht_sms_account)) {
						$m_acc = $corp -> cht_sms_account;
						$m_pwd = $corp -> cht_sms_password;
						$n_res = $this -> curl -> simple_get("http://smexpress.mitake.com.tw/SmSendGet.asp"
							. "?username=$m_acc&password=$m_pwd&dstaddr=$mobile&DestName=SteveYeh&dlvtime=&vldtime=&smbody=$msg");
					}
				}

				// end
			}

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

			$mobile  = (strlen($out_user->account) < 10 ? $out_user->mobile : $out_user->account);

			$corp = $this -> corp_dao -> find_by_id($out_user -> corp_id);
			if($corp -> lang == 'chs') {
				$msg = "轉帳單號 $item->sn 簡訊認證碼為 $reg_code ，此認證碼將於30分鐘後失效。";

				if(!empty($corp -> chs_sms_account)) {
						$m_acc = $corp -> chs_sms_account;
									$m_pwd = $corp -> chs_sms_password;
									$msg = urlencode($msg);
									$n_res = $this -> curl -> simple_get("http://api.sms.cn/sms/?ac=send&uid=$m_acc&pwd=$m_pwd&mobile=$mobile&content=$msg");
				}

			} else {
				// default cht
				$msg=iconv("UTF-8","big5","轉帳單號 $item->sn 簡訊認證碼為 $reg_code ，此認證碼將於30分鐘後失效。");
				if(!empty($corp -> cht_sms_account)) {
					$m_acc = $corp -> cht_sms_account;
					$m_pwd = $corp -> cht_sms_password;
					$n_res = $this -> curl -> simple_get("http://smexpress.mitake.com.tw/SmSendGet.asp"
						. "?username=$m_acc&password=$m_pwd&dstaddr=$mobile&DestName=SteveYeh&dlvtime=&vldtime=&smbody=$msg");
				}
			}
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
