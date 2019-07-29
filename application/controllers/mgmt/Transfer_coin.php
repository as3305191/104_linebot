<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Transfer_coin extends MY_Base_Controller {

	function __construct() {
		parent::__construct();
		$this -> load -> model('Transfer_coin_dao', 'dao');
		$this -> load -> model('Wallet_tx_dao', 'wtx_dao');
		$this -> load -> model('Wallet_tx_btc_dao', 'wtx_btc_dao');
		$this -> load -> model('Wallet_tx_eth_dao', 'wtx_eth_dao');
		$this -> load -> model('Wallet_tx_ntd_dao', 'wtx_ntd_dao');

		$this -> load -> model('Config_dao', 'config_dao');
		$this -> load -> model('Users_dao', 'users_dao');
	}

	public function index()
	{
		$data = array();
		$data = $this -> setup_user_data($data);
		$this->load->view('mgmt/transfer/coin_list', $data);
	}

	public function get_data() {
		$res = array();
		$data = $this -> get_posts(array(
			'length',
			'start',
			'columns',
			'search',
			'order'
		));

		$s_data = $this -> setup_user_data(array());


		$data['is_verify'] = 1;

		$login_user = $this -> users_dao -> find_by_id($s_data['login_user_id']);
		if($login_user -> role_id != 99) {
			$data['corp_id'] = $login_user -> corp_id;
		}

		// $data['corp_id'] = $s_data['corp'] -> id;
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

		$this->load->view('mgmt/transfer/coin_edit', $data);
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

	public function insert_in($status) {
		$res = array();
		$id = $this -> get_post('id');
		$u_data = array();
		$u_data['status'] = $status;

		if($item -> status != 0) {
			$res['success'] = TRUE;
	 		$this -> to_json($res);
			return;
		}

		$s_data = $this -> setup_user_data(array());
		if($status == -1) {
			$this -> dao -> update(array('status' => -1), $id);
		}
		if($status == 1) {
			// send to out user
			$item = $this -> dao -> find_me($id);
			$in_user = $this -> users_dao -> find_by_id($item -> in_user_id);
			$out_user = $this -> users_dao -> find_by_id($item -> out_user_id);
			if(!empty($in_user -> is_valid_mobile)) {
				$this -> dao -> update($u_data, $id); // confirm

				$corp = $s_data['corp'];

				// 新增至錢包
				$tx = array();
				$tx['transfer_coin_id'] = $id;
				$tx['corp_id'] = $corp -> id; // corp id
				$tx['user_id'] = $item -> in_user_id;
				$tx['amt'] = ($item -> amt);
				$tx['type_id'] = 2; // receive

				$tx['brief'] = "接收數量 $item->amt ";

				if($item -> currency == 'btc') {
					// btc
					$this -> wtx_btc_dao -> insert($tx);
				}
				if($item -> currency == 'eth') {
					// eth
					$this -> wtx_eth_dao -> insert($tx);
				}

				// 通知會員
				$mobile = $in_user -> mobile;

				$msg = "親愛的用戶您好：已接收 $item->currency_name 數量 $item->amt 請查收確認。DBC敬上 ";
				if($in_user -> lang == 'chs') {
					if(!empty($corp -> chs_sms_account)) {
						$m_acc = $corp -> chs_sms_account;
								$m_pwd = $corp -> chs_sms_password;
								$msg = urlencode($msg);
								$n_res = $this -> curl -> simple_get("http://api.sms.cn/sms/?ac=send&uid=$m_acc&pwd=$m_pwd&mobile=$mobile&content=$msg");
					}
				} else {
					$msg=iconv("UTF-8","big5",$msg);
					if(!empty($corp -> cht_sms_account)) {
						$m_acc = $corp -> cht_sms_account;
						$m_pwd = $corp -> cht_sms_password;
						$n_res = $this -> curl -> simple_get("http://smexpress.mitake.com.tw/SmSendGet.asp"
							. "?username=$m_acc&password=$m_pwd&dstaddr=$mobile&DestName=SteveYeh&dlvtime=&vldtime=&smbody=$msg");
					}
				}
				// end
			} else {
				$res['error_msg'] = "會員尚未設定手機號碼";
			}
		}
		$res['success'] = TRUE;
 		$this -> to_json($res);
	}

	public function insert_transfer($status) {
		$res = array();
		$id = $this -> get_post('id');
		$item = $this -> dao -> find_me($id);

		if($item -> status != 0) {
			$res['success'] = TRUE;
	 		$this -> to_json($res);
			return;
		}

		$u_data = array();
		$u_data['status'] = $status;

		$s_data = $this -> setup_user_data(array());
		if($status == -1) {
			$out_user = $this -> users_dao -> find_by_id($item -> out_user_id);
			if(!empty($out_user -> is_valid_mobile)) {
				$this -> dao -> update(array('status' => -1), $id);
				// 退款
				$corp = $s_data['corp'];

				// 新增至錢包
				$tx = array();
				$tx['transfer_coin_id'] = $id;
				$tx['corp_id'] = $corp -> id; // corp id
				$tx['user_id'] = $item -> out_user_id;
				$tx['amt'] = ($item -> amt);
				$tx['type_id'] = 4; // refund

				$tx['brief'] = "返還數量 $item->amt ";

				if($item -> currency == 'btc') {
					// btc
					$this -> wtx_btc_dao -> insert($tx);
				}
				if($item -> currency == 'eth') {
					// eth
					$this -> wtx_eth_dao -> insert($tx);
				}

				// 通知會員
				$mobile = $out_user -> mobile;

				$msg = "親愛的用戶您好：已返還 $item->currency_name 數量 $item->amt 請查收確認。 ";
				$msg=iconv("UTF-8","big5",$msg);
				if(!empty($corp -> cht_sms_account)) {
					$m_acc = $corp -> cht_sms_account;
					$m_pwd = $corp -> cht_sms_password;
					$n_res = $this -> curl -> simple_get("http://smexpress.mitake.com.tw/SmSendGet.asp"
						. "?username=$m_acc&password=$m_pwd&dstaddr=$mobile&DestName=SteveYeh&dlvtime=&vldtime=&smbody=$msg");
				}
				// end
			} else {
				$res['error_msg'] = "會員尚未設定手機號碼";
			}
		}

		if($status == 1) {
			// send to out user

			$out_user = $this -> users_dao -> find_by_id($item -> out_user_id);
			if(!empty($out_user -> is_valid_mobile)) {
				$this -> dao -> update($u_data, $id); // confirm

				// 通知會員
				$mobile = $out_user -> mobile;

				$msg = "親愛的用戶您好： $item->currency_name 數量 $item->amt 已確認匯款。 ";
				$msg=iconv("UTF-8","big5",$msg);
				if(!empty($corp -> cht_sms_account)) {
					$m_acc = $corp -> cht_sms_account;
					$m_pwd = $corp -> cht_sms_password;
					$n_res = $this -> curl -> simple_get("http://smexpress.mitake.com.tw/SmSendGet.asp"
						. "?username=$m_acc&password=$m_pwd&dstaddr=$mobile&DestName=SteveYeh&dlvtime=&vldtime=&smbody=$msg");
				}
				// end
			} else {
				$res['error_msg'] = "會員尚未設定手機號碼";
			}
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
