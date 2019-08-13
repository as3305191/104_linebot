<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Set_up_super8 extends MY_Mgmt_Controller {

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
		$this -> load -> model('Config_dao', 'config_dao');

	}

	public function index()
	{
		$data = array();
		$data = $this -> setup_user_data($data);
		$data['config'] = $this -> config_dao -> find_by_id(1);


		$this->load->view('mgmt/set_up_super8/edit', $data);
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

	public function update() {

		$res = array();
		$com_pct = $this -> get_post('com_pct');
		$normal_pct = $this -> get_post('normal_pct');
		$overall_pct = $this -> get_post('overall_pct');
		$normal_winning = $this -> get_post('normal_winning');
		$overall_winning = $this -> get_post('overall_winning');
		$id=1;
		$data['com_pct']=$com_pct;
		$data['normal_pct']=$normal_pct;
		$data['overall_pct']=$overall_pct;
		$data1['normal_winning']=$normal_winning;
		$data1['overall_winning']=floatval($overall_winning)+floatval($normal_winning);
		$sum=floatval($com_pct)+floatval($normal_pct)+floatval($overall_pct);
		$sum1=floatval($overall_winning)+floatval($normal_winning);

		if(!empty($com_pct)||!empty($normal_pct)||!empty($overall_pct)) {
			if(!empty($com_pct)&&!empty($normal_pct)&&!empty($overall_pct)) {
				if($sum==1){
					$this -> config_dao -> update_by($data,'id',$id);
					$res['success'] = "true";
				}else{
					$res['success1'] = "true";
				}
			} else{
				$res['err'] = "true";
			}
		}

		if(!empty($normal_winning)||!empty($overall_winning)) {
			if(!empty($normal_winning)&&!empty($overall_winning)) {
				if ($sum1<=100) {
					$this -> config_dao -> update_by($data1,'id',$id);
					$res['success'] = "true";
				} else {
					$res['success1'] = "true1";
				}
			} else{
				$res['err1'] = "true";
			}
		}


 		$this -> to_json($res);
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
			$error[] = "餘額不足";
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
			$tx['type_id'] = 14; // 升級經理人
			$tx['brief'] = "升級經理人, 花費 $config->upgrade_amt 點";
			$this -> wtx_dao -> insert($tx);

			// intro
			$tx = array();
			$tx['corp_id'] = $s_data['corp'] -> id; // corp id
			$tx['user_id'] = $user -> intro_id;

			$pc = $this -> rs_dao -> get_val(3); // 會員
			$amt = ($config -> upgrade_amt * floatval($pc) / 100.0);
			$tx['amt'] = $amt;
			$tx['type_id'] = 5; // 會員收入
			$tx['brief'] = "會員 $user->account 升級經理人, 利潤 $config->upgrade_amt x $pc% = $amt 點";
			$this -> wtx_dao -> insert($tx);

			// manager
			$tx = array();
			$tx['corp_id'] = $s_data['corp'] -> id; // corp id
			$tx['user_id'] = $user -> manager_id;

			$pc = $this -> rs_dao -> get_val(2); // 經理人
			$amt = ($config -> upgrade_amt * floatval($pc) / 100.0);
			$tx['amt'] = $amt;
			$tx['type_id'] = 6; // 經理人收入
			$tx['brief'] = "會員 $user->account 升級經理人, 利潤 $config->upgrade_amt x $pc% = $amt 點";
			$this -> wtx_dao -> insert($tx);

			// share holder
			$tx = array();
			$tx['corp_id'] = $s_data['corp'] -> id; // corp id
			$tx['user_id'] = $user -> shareholder_id;;

			$pc = $this -> rs_dao -> get_val(11); // 股東
			$amt = ($config->upgrade_amt * floatval($pc) / 100.0);
			$tx['amt'] = $amt;
			$tx['type_id'] = 7; // 股東收入
			$tx['brief'] = "會員 $user->account 升級經理人, 利潤 $config->upgrade_amt x $pc% = $amt 點";
			$this -> wtx_dao -> insert($tx);

			// corp
			$tx = array();
			$tx['corp_id'] = $s_data['corp'] -> id; // corp id

			// corp admin
			$ca = $this -> dao -> find_corp_admin($user -> corp_id);
			$tx['user_id'] = $ca -> id;;

			$pc = $this -> rs_dao -> get_val(1); // 公司
			$amt = ($config->upgrade_amt * floatval($pc) / 100.0);
			$tx['amt'] = $amt;
			$tx['type_id'] = 8; // 公司收入
			$tx['brief'] = "會員 $user->account 升級經理人, 利潤 $config->upgrade_amt x $pc% = $amt 點";
			$this -> wtx_dao -> insert($tx);

			// sys mgmt
			$tx = array();
			$tx['corp_id'] = 1; // sys corp id
			$sa = $this -> dao -> find_sys_admin();
			$tx['user_id'] = $sa -> id;

			$pc = $this -> rs_dao -> get_val(999); // 系統管銷
			$amt = ($config->upgrade_amt * floatval($pc) / 100.0);
			$tx['amt'] = $amt;
			$tx['type_id'] = 9; // 系統管銷
			$tx['brief'] = "會員 $user->account 升級經理人, 利潤 $config->upgrade_amt x $pc% = $amt 點";
			$this -> wtx_dao -> insert($tx);

			// sys margin
			$tx = array();
			$tx['corp_id'] = 1; // sys corp id
			$sa = $this -> dao -> find_sys_admin();
			$tx['user_id'] = $sa -> id;

			$pc = $this -> rs_dao -> get_val(99); // 系統獲利
			$amt = ($config->upgrade_amt * floatval($pc) / 100.0);
			$tx['amt'] = $amt;
			$tx['type_id'] = 10; // 系統獲利
			$tx['brief'] = "會員 $user->account 升級經理人, 利潤 $config->upgrade_amt x $pc% = $amt 點";
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
