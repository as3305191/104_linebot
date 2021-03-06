<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Add_coin extends MY_Mgmt_Controller {

	function __construct() {
		parent::__construct();
		$this -> load -> model('Users_dao', 'dao');
		$this -> load -> model('Images_dao', 'img_dao');
		$this -> load -> model('Wallet_tx_dao', 'wtx_dao');
		$this -> load -> model('Config_dao', 'config_dao');
		$this -> load -> model('Role_share_dao', 'rs_dao');
		$this -> load -> model('Corp_dao', 'corp_dao');
		$this -> load -> model('Banks_dao', 'banks_dao');
		$this -> load -> model('Daily_quotes_dao', 'd_q_dao');

		$this -> load -> model('Add_coin_dao', 'add_dao');
		$this -> load -> model('Quotes_record_dao', 'q_r_dao');
		$this -> load -> model('Game_pool_dao', 'game_pool_dao');

	}

	public function index()
	{
		$data = array();
		$data = $this -> setup_user_data($data);
		$data['role_list'] = $this -> dao -> find_all_roles();

		$add_coin_daily = $this -> q_r_dao -> find_last();
		$data['add_coin_daily'] = $add_coin_daily;
		$this->load->view('mgmt/add_coin/edit', $data);
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
			'role_id'
		));

		$res['items'] = $this -> dao -> query_ajax($data);
		$res['recordsFiltered'] = $this -> dao -> count_ajax($data);
		$res['recordsTotal'] = $this -> dao -> count_all_ajax($data);

		$this -> to_json($res);
	}

	public function edit($id) {
		$data = array();
		$data['id'] = $id;
		$data = $this -> setup_user_data($data);
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
			if(!empty($item -> image_id)) {
				$item -> img = $this -> img_dao -> find_by_id($item -> image_id);
			}

			$data['item'] = $item;
			$data['sum_amt'] = $this -> wtx_dao -> get_sum_amt($item -> id);
			$data['config'] = $this -> config_dao -> get_item_by_corp($data['corp'] -> id);
			$data['corp'] = $this -> corp_dao -> find_by_id($item -> corp_id);
		}

		$data['role_list'] = $this -> dao -> find_all_roles();

		$lang = $this -> session -> userdata('lang');
		$data['lang'] = $lang;
		$country = 0;
		if($lang == 'chs') {
			$country = 1;
		}
		$data['bank_list'] = $this -> banks_dao -> find_all_by_country($country);

		$this->load->view('mgmt/add_coin/edit', $data);
	}

	public function insert() {

		$res = array();
		$point = $this -> get_post('point');
		$ntd = $this -> get_post('ntd');

		$data['point']=$point;
		$data['ntd']=$ntd;

		$Date = date("Y-m-d");

		if(!empty($point)||!empty($ntd)) {
			// insert
			$last_id=$this -> add_dao -> insert($data);
			$add_coin=$this -> add_dao -> find_by_id($last_id);

			// ?????? point
			$atx = array();
			$atx['tx_type'] = "add_coin";
			$atx['tx_id'] = $last_id;
			$atx['corp_id'] = 1; // corp id
			$atx['user_id'] = 528; // ???????????????
			$atx['amt'] = $point;
			$atx['brief'] = "?????????????????? {$point}";
			$this -> wtx_dao -> insert($atx);

			// -- ???????????? --
			// ntd
			$get_current_ntd = $this -> add_dao -> sum_all_ntd();
			// point
			$get_current_point =  $this -> wtx_dao -> get_sum_amt_total();
			// ??????
			$get_all_pool = $this -> game_pool_dao -> get_all_pool_amt();

			$idata['tx_type']="add_coin";
			$idata['tx_id']=$last_id;
			$idata['point_change']=$point;
			$idata['current_point']=floatval($get_current_point)+$get_all_pool;
			$idata['ntd_change']=$ntd;
			$idata['current_ntd']=floatval($get_current_ntd);
			$last_id_insert_q = $this -> q_r_dao -> insert($idata);
			$add_coin_daily=$this -> q_r_dao -> find_by_id($last_id_insert_q);
			$p1 = $this -> d_q_dao -> find_last_d_q($Date);
			$dq =  $this -> d_q_dao -> find_d_q($Date);
			$cp = floatval(intval($add_coin_daily->current_point)); // ?????????0??????
			$p = 0;
			if($cp != 0) {
				$p=floatval($add_coin_daily->current_ntd)/floatval(intval($add_coin_daily->current_point));
			}
			$price1 = round($p,8);
			$dtx['date'] = $Date;
			$dtx['average_price'] = $p1->last_price;
			$dtx['last_price'] = $price1;
			$dtx['now_price'] = $price1;
			if(!empty($dq)){
				$u_data['last_price'] = $price1;
				$u_data['now_price'] = $price1;
				$this -> d_q_dao -> update_by($u_data,'id',$dq->id);
			} else{
				$this -> d_q_dao -> insert($dtx);
			}
		}

		$res['success'] = TRUE;

 		$this -> to_json($add_coin_daily);
	}

	public function upgrade_me() {
		$res = array();
		$id = $this -> get_post('id');
		$user = $this -> dao -> find_me($id);

		// session data
		$s_data = $this -> setup_user_data(array());

		// config
		$config = $this -> config_dao -> get_item_by_corp($s_data['corp'] -> id);

		$error = array();
		$sum_amt = $this -> wtx_dao -> get_sum_amt($user -> id);
		if($sum_amt < $config -> upgrade_amt) {
			$error[] = "????????????";
			$res['error_code'] = 98;
		}

		if(count($error) == 0) {
			$this -> dao -> db -> trans_start();
			// do upgrade
			$u_data['role_id'] = 2; // manager
			$this -> dao -> update($u_data, $id);

			// buy tx
			$tx = array();
			$tx['corp_id'] = $s_data['corp'] -> id; // corp id
			$tx['user_id'] = $id;
			$tx['amt'] = -($config -> upgrade_amt);
			$tx['type_id'] = 14; // ???????????????
			$tx['brief'] = "???????????????, ?????? $config->upgrade_amt ???";
			$this -> wtx_dao -> insert($tx);

			// intro
			$tx = array();
			$tx['corp_id'] = $s_data['corp'] -> id; // corp id
			$tx['user_id'] = $user -> intro_id;

			$pc = $this -> rs_dao -> get_val(3); // ??????
			$amt = ($config -> upgrade_amt * floatval($pc) / 100.0);
			$tx['amt'] = $amt;
			$tx['type_id'] = 5; // ????????????
			$tx['brief'] = "?????? $user->account ???????????????, ?????? $config->upgrade_amt x $pc% = $amt ???";
			$this -> wtx_dao -> insert($tx);

			// manager
			$tx = array();
			$tx['corp_id'] = $s_data['corp'] -> id; // corp id
			$tx['user_id'] = $user -> manager_id;

			$pc = $this -> rs_dao -> get_val(2); // ?????????
			$amt = ($config -> upgrade_amt * floatval($pc) / 100.0);
			$tx['amt'] = $amt;
			$tx['type_id'] = 6; // ???????????????
			$tx['brief'] = "?????? $user->account ???????????????, ?????? $config->upgrade_amt x $pc% = $amt ???";
			$this -> wtx_dao -> insert($tx);

			// share holder
			$tx = array();
			$tx['corp_id'] = $s_data['corp'] -> id; // corp id
			$tx['user_id'] = $user -> shareholder_id;;

			$pc = $this -> rs_dao -> get_val(11); // ??????
			$amt = ($config->upgrade_amt * floatval($pc) / 100.0);
			$tx['amt'] = $amt;
			$tx['type_id'] = 7; // ????????????
			$tx['brief'] = "?????? $user->account ???????????????, ?????? $config->upgrade_amt x $pc% = $amt ???";
			$this -> wtx_dao -> insert($tx);

			// corp
			$tx = array();
			$tx['corp_id'] = $s_data['corp'] -> id; // corp id

			// corp admin
			$ca = $this -> dao -> find_corp_admin($user -> corp_id);
			$tx['user_id'] = $ca -> id;;

			$pc = $this -> rs_dao -> get_val(1); // ??????
			$amt = ($config->upgrade_amt * floatval($pc) / 100.0);
			$tx['amt'] = $amt;
			$tx['type_id'] = 8; // ????????????
			$tx['brief'] = "?????? $user->account ???????????????, ?????? $config->upgrade_amt x $pc% = $amt ???";
			$this -> wtx_dao -> insert($tx);

			// sys mgmt
			$tx = array();
			$tx['corp_id'] = 1; // sys corp id
			$sa = $this -> dao -> find_sys_admin();
			$tx['user_id'] = $sa -> id;

			$pc = $this -> rs_dao -> get_val(999); // ????????????
			$amt = ($config->upgrade_amt * floatval($pc) / 100.0);
			$tx['amt'] = $amt;
			$tx['type_id'] = 9; // ????????????
			$tx['brief'] = "?????? $user->account ???????????????, ?????? $config->upgrade_amt x $pc% = $amt ???";
			$this -> wtx_dao -> insert($tx);

			// sys margin
			$tx = array();
			$tx['corp_id'] = 1; // sys corp id
			$sa = $this -> dao -> find_sys_admin();
			$tx['user_id'] = $sa -> id;

			$pc = $this -> rs_dao -> get_val(99); // ????????????
			$amt = ($config->upgrade_amt * floatval($pc) / 100.0);
			$tx['amt'] = $amt;
			$tx['type_id'] = 10; // ????????????
			$tx['brief'] = "?????? $user->account ???????????????, ?????? $config->upgrade_amt x $pc% = $amt ???";
			$this -> wtx_dao -> insert($tx);
			$this -> dao -> db -> trans_complete();
		} else {
			$res['error_msg'] = join(',', $error);
		}

		$res['success'] = TRUE;
 		$this -> to_json($res);
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
