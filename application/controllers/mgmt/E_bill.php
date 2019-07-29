<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class E_bill extends MY_Base_Controller {

	function __construct() {
		parent::__construct();
		$this -> load -> model('Pay_orders_dao', 'dao');

		$this -> load -> model('Images_dao', 'img_dao');

		$this -> load -> model('Wallet_tx_dao', 'wtx_dao');
		$this -> load -> model('Wallet_tx_btc_dao', 'wtx_btc_dao');
		$this -> load -> model('Wallet_tx_eth_dao', 'wtx_eth_dao');
		$this -> load -> model('Wallet_tx_ntd_dao', 'wtx_ntd_dao');

		$this -> load -> model('Coins_dao', 'coins_dao');
		$this -> load -> model('Users_dao', 'u_dao');
	}

	public function index()
	{
		$data = array();
		$data = $this -> setup_user_data($data);

		$data['sum_amt'] = $this -> wtx_dao -> get_sum_amt($data['login_user_id']);
		$data['sum_amt_btc'] = $this -> wtx_btc_dao -> get_sum_amt($data['login_user_id']);
		$data['sum_amt_eth'] = $this -> wtx_eth_dao -> get_sum_amt($data['login_user_id']);
		$data['sum_amt_ntd'] = $this -> wtx_ntd_dao -> get_sum_amt($data['login_user_id']);

		$data['cate_main_list'] = $this -> dao -> list_cate_main();
		$this->load->view('mgmt/e_bill/list', $data);
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
			'user_id',

		));

		$items = $this -> dao -> query_ajax($data);
		$res['items'] = $items;

		$res['recordsFiltered'] = $this -> dao -> count_ajax($data);
		$res['recordsTotal'] = $this -> dao -> count_all_ajax($data);

		$res['status_cnt'] = $this -> dao -> count_all_status();

		$this -> to_json($res);
	}

	public function list_cate_sub() {
		$res = array();

		$cate_main_id = $this -> get_post('cate_main_id');
		$res['cate_main_id'] = $cate_main_id;
		$res['list'] = $this -> dao -> list_cate_sub($cate_main_id);
		$this -> to_json($res);

	}

	public function edit($id, $main_id = 0, $sub_id = 0) {
		$data = array();
		$data['id'] = $id;

		$data = $this -> setup_user_data($data);

		// $data['sum_amt'] = $this -> wtx_dao -> get_sum_amt($data['login_user_id']);
		// $data['sum_amt_btc'] = $this -> wtx_btc_dao -> get_sum_amt($data['login_user_id']);
		// $data['sum_amt_eth'] = $this -> wtx_eth_dao -> get_sum_amt($data['login_user_id']);
		// $data['sum_amt_ntd'] = $this -> wtx_ntd_dao -> get_sum_amt($data['login_user_id']);

		$dbc = $this -> coins_dao -> find_by_currency('dbc');
		$data['dbc_coin'] = $dbc;

		$item = NULL;
		if(!empty($id)) {
			$items = $this -> dao -> find_all_me($id);
			$data['item'] = $items[0];

			$main_id = $data['item'] -> pay_order_cate_main_id;
			$sub_id = $data['item'] -> pay_order_cate_sub_id;

			$data['sum_amt'] = $this -> wtx_dao -> get_sum_amt($data['item'] -> user_id);
		}
		$data['main_id'] = $main_id;
		$data['sub_id'] = $sub_id;

		$m_list = $this -> dao -> list_cate_main();
		foreach($m_list as $each) {
			if($each -> id == $main_id) {
				$data['main_obj'] = $each;
			}
		}
		$s_list = $this -> dao -> list_cate_sub($main_id);
		foreach($m_list as $each) {
			if($each -> id == $sub_id) {
				$data['sub_obj'] = $each;
			}
		}



		$this->load->view('mgmt/e_bill/edit', $data);
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

	public function insert() {
		$res = array();
		$id = $this -> get_post('id');
		$data = $this -> get_posts(array(
			// 'amt_ntd',
			// 'amt_dbc',
			'pay_order_cate_main_id',
			'pay_order_cate_sub_id'
		));

		$s_data = $this -> setup_user_data(array());

		$due_y = $this -> get_post('due_y');
		if(!empty($due_y)) {
			$data['due_y'] = $due_y;
		}
		$due_m = $this -> get_post('due_m');
		if(!empty($due_m)) {
			$data['due_m'] = $due_m;
		}
		$due_d = $this -> get_post('due_d');
		if(!empty($due_d)) {
			$data['due_d'] = $due_d;
		}
		$serial = $this -> get_post('serial');
		if(!empty($serial)) {
			$data['serial'] = $serial;
		}
		$check = $this -> get_post('check');
		if(!empty($check)) {
			$data['check'] = $check;
		}
		$plate_no = $this -> get_post('plate_no');
		if(!empty($plate_no)) {
			$data['plate_no'] = $plate_no;
		}
		$uid = $this -> get_post('uid');
		if(!empty($uid)) {
			$data['uid'] = $uid;
		}
		$mobile = $this -> get_post('mobile');
		if(!empty($mobile)) {
			$data['mobile'] = $mobile;
		}

		if(empty($id)) {
			$data['corp_id'] = $s_data['corp'] -> id;
			$data['user_id'] = $s_data['l_user'] -> id;
			$user = $s_data['l_user'];
			$sn = "PO" . $s_data['l_user'] -> id . date('YmdHis');
			$data['sn'] = $sn;
			// insert
			$id = $this -> dao -> insert($data);

			$corp = $s_data['corp'];
			$items = $this -> dao -> find_all_me($id);
			$item = $items[0];

			$m_sms_arr = explode(',', $corp -> manager_sms);
			foreach($m_sms_arr as $mobile) {
				// if($mobile != '0925815921') continue;

				$msg = "民生繳費通知，會員 $user->account 建立確認單 " . $sn . "，請上線確認";
				$msg=iconv("UTF-8","big5",$msg);
				if(!empty($corp -> cht_sms_account)) {
					$m_acc = $corp -> cht_sms_account;
					$m_pwd = $corp -> cht_sms_password;
					$n_res = $this -> curl -> simple_get("http://smexpress.mitake.com.tw/SmSendGet.asp"
						. "?username=$m_acc&password=$m_pwd&dstaddr=$mobile&DestName=SteveYeh&dlvtime=&vldtime=&smbody=$msg");
				}
			}
		} else {
			// update
			//$this -> dao -> update($data, $id);
		}

		$res['success'] = TRUE;
 		$this -> to_json($res);
	}

	public function delete($id) {
		$res['success'] = TRUE;
		$this -> dao -> delete_status($id, $this -> session -> userdata('user_id'));
		$this -> to_json($res);
	}

	public function do_status() {
		$res['success'] = TRUE;
		$id = $this -> get_post('id');
		$items = $this -> dao -> find_all_me($id);
		$item = $items[0];

		$status = $this -> get_post('status');
		$amt_ntd = $this -> get_post('amt_ntd');

		$user = $this -> u_dao -> find_by_id($item -> user_id);

		$u_data = array();
		$u_data['status'] = $status;

		$s_data = $this -> setup_user_data(array());

		$dbc = $this -> coins_dao -> find_by_currency('dbc');

		$has_error = FALSE;
		if($item -> status == 0 && $status == 1) {
			$u_data['amt_ntd'] = $amt_ntd;
			$amt_dbc = floatval($amt_ntd) / $dbc -> sell_price_twd * 1.2;
			$sum_amt = $this -> wtx_dao -> get_sum_amt($item -> user_id);

			if($sum_amt >= $amt_dbc) {
				$u_data['amt_ntd'] = $amt_ntd;
				$u_data['amt_dbc'] = $amt_dbc;

				// commit tx
				$tx = array();
				$tx['pay_order_id'] = $item -> id;
				$tx['user_id'] = $item -> user_id;
				$tx['corp_id'] = $item -> corp_id;

				$p_amt = $amt_dbc;
				$tx['amt'] = -$p_amt;
				$tx['type_id'] = 40; // 退款

				$tx['brief'] = "民生繳費扣款 $item->pay_order_cate_main_name DBC數量 -$p_amt";
				$this -> wtx_dao -> insert($tx);

				$mobile = $user -> mobile;

				$corp = $s_data['corp'];

				$msg = "親愛的用戶您好： 民生繳費單號 $item->sn 已扣款DBC數量 $p_amt 。DBC敬上 ";
				if($user -> lang == 'chs') {
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
			} else {
				$has_error = TRUE;
				$res['error_msg'] = 'DBC不足';
			}
		}

		if($item -> status > 0 && $status == -1) { // 已經叩過款

			// commit tx
			$tx = array();
			$tx['pay_order_id'] = $item -> id;
			$tx['user_id'] = $item -> user_id;
			$tx['corp_id'] = $item -> corp_id;

			$p_amt = $item -> amt_dbc;
			$tx['amt'] = $p_amt;
			$tx['type_id'] = 41; // 退款

			$tx['brief'] = "民生繳費退款 $item->pay_order_cate_main_name DBC數量 $p_amt";
			$this -> wtx_dao -> insert($tx);

			$mobile = $user -> mobile;

			$corp = $s_data['corp'];

			$msg = "親愛的用戶您好： 民生繳費單號 $item->sn 已退款DBC數量 $p_amt 。DBC敬上 ";
			if($user -> lang == 'chs') {
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
		}

		if(!$has_error) {
			$this -> dao -> update($u_data, $id);
		}

		$this -> to_json($res);
	}
}
