<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Mining_buy extends MY_Mgmt_Controller {

	function __construct() {
		parent::__construct();
		$this -> load -> model('Mining_machine_buy_records_dao', 'dao');
		$this -> load -> model('Mining_machines_dao', 'mm_dao');
		$this -> load -> model('Products_dao', 'p_dao');
		$this -> load -> model('Pay_types_dao', 'pt_dao');

		$this -> load -> model('Wallet_tx_dao', 'wtx_dao');
		$this -> load -> model('Users_dao', 'u_dao');

		$this -> load -> model('Coins_dao', 'coins_dao');

		//載入SDK(路徑可依系統規劃自行調整)
		include APPPATH . 'third_party/ECPay.Payment.Integration.php';
	}

	public function index()
	{
		$data = array();
		$data = $this -> setup_user_data($data);
		$data['sum_amt'] = $this -> wtx_dao -> get_sum_amt($data['login_user_id']);

		$this->load->view('mgmt/mining_buy/list', $data);
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
		$res['sum_amt'] = $this -> wtx_dao -> get_sum_amt($s_data['login_user_id']);

		$login_user = $this -> users_dao -> find_by_id($s_data['login_user_id']);
		if($login_user -> role_id != 99) {
			$data['corp_id'] = $login_user -> corp_id;
		}

		$res['items'] = $this -> dao -> query_ajax($data);
		$res['recordsFiltered'] = $this -> dao -> count_ajax($data);
		$res['recordsTotal'] = $this -> dao -> count_all_ajax($data);

		$this -> to_json($res);
	}

	public function get_machine() {
		$res = array();
		$data = array();
		$data = $this -> setup_user_data($data);


		$m_id = $this -> get_post('machine_id');

		$mm = $this -> mm_dao -> find_by_id($m_id);
		$res['item'] = $mm;

		$dbc = $this -> coins_dao -> find_by_currency($data['corp'] -> corp_code);

		$res['dbc_avg'] = $dbc -> sell_price_twd;
		$res['dbc_amt'] = $mm -> ntd_price / floatval($dbc -> sell_price_twd);

		$res['sum_amt'] = $this -> wtx_dao -> get_sum_amt($data['login_user_id']);
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
		$login_user = $this -> u_dao -> find_by_id($s_data['login_user_id']);
		$is_foreign = FALSE;

		$data['l_user'] = $s_data['l_user'];
		$data['corp'] = $s_data['corp'];
		$data['mm_list'] = $this -> mm_dao -> find_all_valid_by_corp($data['corp'] -> id);
		$data['sum_amt'] = $this -> wtx_dao -> get_sum_amt($s_data['login_user_id']);
		$this->load->view('mgmt/mining_buy/edit', $data);
	}

	public function insert() {
		$res = array();
		$id = $this -> get_post('id');
		$s_data = $this -> setup_user_data(array());
		$data = array();
		$data['mining_machine_id'] = $this -> get_post('mining_machine_id');;
		$data['user_id'] = $s_data['login_user_id'];

		$mm = $this -> mm_dao -> find_by_id($data['mining_machine_id'] );

		$dbc = $this -> coins_dao -> find_by_currency($s_data['corp'] -> corp_code);

		$data['buy_ntd_price'] = $mm -> ntd_price;
		$data['buy_dbc_avg'] = $dbc -> sell_price_twd;
		$data['buy_dbc_amt'] = $mm -> ntd_price / floatval($dbc -> sell_price_twd);

		$sum_amt = $this -> wtx_dao -> get_sum_amt($s_data['login_user_id']);

		if(empty($id)) {
			// insert
			$data['corp_id'] = $s_data['corp'] -> id;
			$data['sn'] = 'MB' . date('YmdHis');

			if($sum_amt >= $data['buy_dbc_amt'] ) {
				$id = $this -> dao -> insert($data);
				$item = $this -> dao -> find_by_id($id);

				$this -> dao -> update(array('last_process_time' => $item -> create_time), $item -> id);

				$user = $this -> u_dao -> find_by_id($data['user_id']);

				// wtx
				$buy_bdc_amt = $data['buy_dbc_amt'];

				// commit tx
				$tx = array();
				$tx['corp_id'] = $s_data['corp'] -> id;
				$tx['mining_machine_buy_record_id'] = $id;
				$tx['user_id'] = $s_data['login_user_id'];
				$tx['amt'] = -$buy_bdc_amt;
				$tx['type_id'] = 35; // 購買挖礦機
				$tx['brief'] = "購買挖礦機 -$buy_bdc_amt 點";
				$this -> wtx_dao -> insert($tx);

				// intro
				$tx = array();
				$tx['corp_id'] = $s_data['corp'] -> id;
				$tx['mining_machine_buy_record_id'] = $id;
				$tx['user_id'] = $user -> intro_id;
				$buy_bdc_amt_bonus = $buy_bdc_amt * 0.1;
				$tx['amt'] = $buy_bdc_amt_bonus;
				$tx['type_id'] = 37; // 購買挖礦機獎勵
				$tx['brief'] = "購買挖礦機獎勵 $buy_bdc_amt_bonus 點";
				$this -> wtx_dao -> insert($tx);

			} else {
				$res['error_msg'] = "餘額不足";
			}

		} else {
			// update
			// $this -> dao -> update($data, $id);
		}

		$res['success'] = TRUE;
		$res['last_id'] = $id;
 		$this -> to_json($res);
	}

	public function intro() {
		// $list = $this -> wtx_dao -> find_all_by('type_id', 35);
		// foreach($list as $each) {
		// 	echo $each -> user_id . "<br/>";
    //
		// 	$tx = array();
		// 	$tx['mining_machine_buy_record_id'] = $each -> mining_machine_buy_record_id;
    //
		// 	$user = $this -> u_dao -> find_by_id($each -> user_id);
		// 	$tx['user_id'] = $user -> intro_id;
		// 	$buy_bdc_amt_bonus = -$each -> amt * 0.1;
		// 	$tx['amt'] = $buy_bdc_amt_bonus;
		// 	$tx['type_id'] = 37; // 購買挖礦機獎勵
		// 	$tx['brief'] = "購買挖礦機獎勵 $buy_bdc_amt_bonus 點";
		// 	$this -> wtx_dao -> insert($tx);
		// }
	}

	public function sys_insert() {
		$res = array();
		$id = $this -> get_post('id');
		$s_data = $this -> setup_user_data(array());
		$data = $this -> get_posts(array(
			'amt',
			'user_id'
		));
		$data['pay_type_id'] = 4; // sys
		$data['status'] = 1; // 已繳款
		$data['payment_time'] = date('Y-m-d H:i:s'); // 繳款時間
		$user = $this -> u_dao -> find_me($data['user_id']);
		if(empty($id)) {
			// insert
			$data['corp_id'] = $user -> corp_id;
			$data['sn'] = 'P' . date('YmdHis');
			$id = $this -> dao -> insert($data);

			$pr = $this -> dao -> find_by_id($id);
			// commit tx
			$tx = array();
			$tx['pay_record_id'] = $pr -> id;
			$tx['user_id'] = $pr -> user_id;
			$tx['amt'] = $pr -> amt;
			$tx['type_id'] = 17; // buy by sys
			$tx['brief'] = "系統購買點數 $pr->amt 點";
			$this -> wtx_dao -> insert($tx);
		} else {
			// update
			//$this -> dao -> update($data, $id);
		}

		$res['success'] = TRUE;
		$res['last_id'] = $id;
 		$this -> to_json($res);
	}

	public function insert_and_pay() {
		$res = array();
		$id = $this -> get_post('id');
		$s_data = $this -> setup_user_data(array());
		$data = $this -> get_posts(array(
			'amt',
			'pay_type_id'
		));
		$data['user_id'] = $s_data['login_user_id'];

		// insert
		$data['sn'] = 'P' . date('YmdHis');
		$id = $this -> dao -> insert($data);

		redirect(base_url("mgmt/pay_records/pay/$id"));
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
