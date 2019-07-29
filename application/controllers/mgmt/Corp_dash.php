<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Corp_dash extends MY_Base_Controller {

	function __construct() {
		parent::__construct();
		$this -> load -> model('Images_dao', 'img_dao');

		$this -> load -> model('Params_dao', 'params_dao');
		$this -> load -> model('Wallet_tx_dao', 'wtx_dao');
		$this -> load -> model('Com_tx_dao', 'ctx_dao');
		$this -> load -> model('Users_dao', 'users_dao');

		$this -> load -> model('Cash_deposite_dao', 'cash_deposit_dao');
		$this -> load -> model('Transfer_gift_dao', 'transfer_gift_dao');
	}

	public function index()
	{
		$data = array();
		$data = $this -> setup_user_data($data);
		$l_user = $this -> users_dao -> find_by_id($data['login_user_id']);
		$data['l_user'] = $l_user;

		$corp = $data['corp'];
		$data['param'] = $this -> params_dao -> find_by_corp_id($corp -> id);

		$data['sum_amt_all'] = $this -> ctx_dao -> get_sum_amt_all();
		$data['sum_amt_month'] = $this -> ctx_dao -> get_sum_amt_time(date('Y-m'));
		$data['sum_amt_day'] = $this -> ctx_dao -> get_sum_amt_time(date('Y-m-d'));


		$data['sum_all'] = $this -> wtx_dao -> sum_all($corp -> id);

		$this->load->view('mgmt/corp_dash/list', $data);
	}

	public function find_all_corp_tx() {
		$res = array();

		$s_data = $this -> setup_user_data(array());
		$login_user = $this -> users_dao -> find_by_id($s_data['login_user_id']);
		if($login_user -> role_id != 99) {
			$data['corp_id'] = $s_data['corp'] -> id;
		}

		$s_date = $this -> get_post('s_date');

		$res_list = array();

		/*
		今日充值金幣數量
		今日贈禮數量
		今日手續費
		今日新增會員
		今日總收益金幣(總輸贏
		*/
		// 今日充值金幣數量
		$obj = new stdClass;
		$obj -> type_name = "今日充值金幣數量";
		$obj -> sum_amt = $this -> cash_deposit_dao -> sum_by_date($s_date);
		$res_list[] = $obj;

		// 今日贈禮數量
		$obj = new stdClass;
		$obj -> type_name = "今日贈禮數量";
		$obj -> sum_amt = $this -> transfer_gift_dao -> sum_by_date($s_date);
		$res_list[] = $obj;

		// 今日贈禮手續費
		$obj = new stdClass;
		$obj -> type_name = "今日贈禮手續費";
		$obj -> sum_amt = $this -> transfer_gift_dao -> sum_ope_by_date($s_date);
		$res_list[] = $obj;

		// 今日新增會員
		$obj = new stdClass;
		$obj -> type_name = "今日新增會員";
		$obj -> sum_amt = $this -> users_dao -> count_user($s_date, 1);
		$res_list[] = $obj;

		// 今日總收益金幣(總輸贏)
		$obj = new stdClass;
		$obj -> type_name = "今日總收益金幣(總輸贏)";
		$obj -> sum_amt = $this -> ctx_dao -> sum_win_loose_amt_by_date($s_date);
		$res_list[] = $obj;

		$res['cp_list'] = $res_list;
		$this -> to_json($res);
	}

	public function find_all_p_tx() {
		$res = array();
		$data['s_date'] = $this -> get_post('s_date');
		$data['e_date'] = $this -> get_post('e_date');
		$data['user_id'] = $this -> get_post('user_id');
		$list = $this -> wtx_dao -> find_all_user_tx($data);
		$res['cp_list'] = $list;
		$sum = 0;
		foreach($list as $each) {
			$sum += $each -> amt;
		}
		$res['sum'] = $sum;
		$this -> to_json($res);
	}

	public function find_all_m_user() {
		$res = array();
		$data['s_date'] = $this -> get_post('s_date');
		$data['e_date'] = $this -> get_post('e_date');
		$data['user_id'] = $this -> get_post('user_id');
		$list = $this -> users_dao -> find_all_user_by_me($data);
		$res['cp_list'] = $list;
		$res['sum'] = count($list);
		$this -> to_json($res);
	}
}
