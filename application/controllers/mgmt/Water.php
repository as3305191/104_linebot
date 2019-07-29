<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Water extends MY_Base_Controller {

	function __construct() {
		parent::__construct();
		$this -> load -> model('Guide_dao', 'dao');
		$this -> load -> model('Guide_tx_dao', 'gtx_dao');
		$this -> load -> model('Images_dao', 'img_dao');

		$this -> load -> model('Company_dao', 'c_dao');
		$this -> load -> model('Users_dao', 'u_dao');
		$this -> load -> model('Marquee_dao', 'm_dao');
		$this -> load -> model('Com_tab_dao', 'ct_dao');
		$this -> load -> model('Com_tab_user_dao', 'ctu_dao');

		$this -> load -> model('Params_dao', 'params_dao');
	}

	public function index()
	{
		$data = array();
		$data = $this -> setup_user_data($data);
		$corp = $data['corp'];
		$data['param'] = $this -> params_dao -> find_by_corp_id($corp -> id);

		$user = $this -> u_dao -> find_by_id($data['login_user_id']);
		if(($user -> role_id != 1 && $user -> role_id != 99) && (empty($user -> end_time) || strtotime($user -> end_time) < time())) {
			$this->load->view('mgmt/water/buy_info', $data);
			return;
		}
		$data['marquee_list'] = $this -> m_dao -> find_all_order();
		$data['l_user'] = $user;
		$this->load->view('mgmt/water/list', $data);
	}

	public function com_select()
	{
		$data = array();
		$data = $this -> setup_user_data($data);
		$data['company_list'] = $this -> c_dao -> find_all_not_store();
		$data['guide_list'] = $this -> dao -> list_all_unfinished($data['login_user_id']);

		$user = $this -> u_dao -> find_by_id($data['login_user_id']);
		if(($user -> role_id != 1 && $user -> role_id != 99) && (empty($user -> end_time) || strtotime($user -> end_time) < time())) {
			redirect('mgmt/water');
			return;
		}

		$this->load->view('mgmt/water/com_select', $data);
	}

	public function table_select()
	{
		$com_id = $this -> get_get('com_id');

		$data = array();
		$data = $this -> setup_user_data($data);

		$user = $this -> u_dao -> find_by_id($data['login_user_id']);
		if(($user -> role_id != 1 && $user -> role_id != 99) && (empty($user -> end_time) || strtotime($user -> end_time) < time())) {
			redirect('mgmt/water');
			return;
		}

		$this -> tick($user, $com_id);

		$data['company'] = $this -> c_dao -> find_by_id($com_id);
		$data['tab_list'] = $this -> ctu_dao -> find_all_com_tab_by_com_id($user -> id, $com_id);
		$this->load->view('mgmt/water/table_select', $data);
	}


	public function tick($user, $com_id) {
		$min_sec = 35;
		$now = time();
		if($user -> last_tick < $now && ($now - $user -> last_tick) > $min_sec) {
			// do tick
			$list = $this -> ct_dao -> find_all_by_com_id($com_id);
			foreach($list as $each) {
				// find
				$one = $this -> ctu_dao -> find_by_com_and_tab($user -> id, $each -> com_id, $each -> tab_id);
				$cval = rand(926, 3273);
				if(!empty($one)) {
					// update
					$this -> ctu_dao -> update(array(
						'current_percent' => $cval
					), $one -> id);
				} else {
					// create
					$this -> ctu_dao -> create_by_com_and_tab($user -> id, $each -> com_id, $each -> tab_id, $cval);
				}
			}
			// update user tick
			$this -> u_dao -> update(array(
				'last_tick' => $now
			), $user -> id);
		}

	}

	public function do_sync() {
		$res = array();
		$res['success'] = TRUE;
		$min_sec = 15;

		$com_id = $this -> get_get('com_id');

		$data = array();
		$data = $this -> setup_user_data($data);

		// find user
		$user = $this -> u_dao -> find_by_id($data['login_user_id']);


		$now = time();
		if(($now - $user -> last_tick) > $min_sec) {
			$res['tick'] = true;
			// do tick
			$list = $this -> ct_dao -> find_all_by_com_id($com_id);
			$res['com_id'] = $com_id;
			foreach($list as $each) {

				// find
				$one = $this -> ctu_dao -> find_by_com_and_tab($user -> id, $each -> com_id, $each -> tab_id);
				$cval = rand(926, 3273);
				if(!empty($one)) {
					$res['update'] = true;
					// update
					$this -> ctu_dao -> update(array(
						'current_percent' => $cval
					), $one -> id);
				} else {
					// create
					$this -> ctu_dao -> create_by_com_and_tab($user -> id, $each -> com_id, $each -> tab_id, $cval);
				}
			}
			// update user tick
			$this -> u_dao -> update(array(
				'last_tick' => $now
			), $user -> id);
		} else {
			$rem = $min_sec - ($now - $user -> last_tick) + 1;
			$res['error_msg'] = "至少須經過 $min_sec 秒後才可再同步";
		}
		$this -> to_json($res);
	}

	public function set_yn() {
		$com_id = $this -> get_get('com_id');
		$tab_id = $this -> get_get('tab_id');
		$this -> session -> set_userdata('s_yn', 'yes');
		redirect("mgmt/water/main?com_id=$com_id&tab_id=$tab_id");
	}

	public function set_yn_session() {
		$res = array();
		$this -> session -> set_userdata('s_yn', 'yes');
		$res['success'] = TRUE;
 		$this -> to_json($res);
	}

	public function main()
	{
		$com_id = $this -> get_get('com_id');
		$tab_id = $this -> get_get('tab_id');
		$clear_yn = $this -> get_get('clear_yn');
		$set_yn = $this -> get_get('set_yn');

		if(!empty($clear_yn)) {
			$this -> session -> set_userdata('s_yn', '');
		}

		if(!empty($set_yn)) {
			$this -> session -> set_userdata('s_yn', 'yes');
		}

		$data = array();
		$data = $this -> setup_user_data($data);

		$user = $this -> u_dao -> find_by_id($data['login_user_id']);
		if(($user -> role_id != 1 && $user -> role_id != 99) && (empty($user -> end_time) || strtotime($user -> end_time) < time())) {
			redirect('mgmt/water');
			return;
		}

		$data['company'] = $this -> c_dao -> find_by_id($com_id);
		$data['tab_id'] = $tab_id;

		$com_tab = $this -> ct_dao -> find_by_com_and_tab($com_id, $tab_id);
		$data['tab_name'] = $com_tab -> tab_name;
		$data['com_id'] = $com_id;

		$item = $this -> dao -> find_by_com_and_tab($data['login_user_id'], $com_id, $tab_id);
		$data['item'] = $item;

		// suggest amt
		$s_amt = 0;
		if(!empty($item)) {

			$last_tx = $this -> gtx_dao -> find_last_tx($item -> id);

			if($last_tx -> bet_type == 0) { // 初始
				$s_amt = intval($last_tx -> result_amt / 100);
			} else {
				if($last_tx -> is_win == 0 || $last_tx -> is_win == 99) {
					$s_amt = $last_tx -> bet_amt;
				}
				if($last_tx -> is_win == 1) {
					$s_amt = intval($item -> base_amt / 100);
				}
				if($last_tx -> is_win == -1) {
					$s_amt = $last_tx -> bet_amt * 2;
				}
				if(($last_tx -> result_amt - $s_amt) < 0) {
					$s_amt = $last_tx -> result_amt;
				}
			}
		}
		$data['s_amt'] = $s_amt;
		$this->load->view('mgmt/water/main', $data);
	}

	public function start()
	{
		$com_id = $this -> get_post('com_id');
		$tab_id = $this -> get_post('tab_id');
		$base_amt = $this -> get_post('base_amt');

		$com_tab = $this -> ct_dao -> find_by_com_and_tab($com_id, $tab_id);

		$data = array();
		$data = $this -> setup_user_data($data);
		$i_data['user_id'] = $data['login_user_id'];
		$i_data['com_id'] = $com_id;
		$i_data['tab_id'] = $tab_id;
		$i_data['tab_name'] = $com_tab -> tab_name;
		$i_data['base_amt'] = $base_amt;
		$i_data['balance'] = $base_amt;
		$i_data['create_date'] = date('Y-m-d');
		$last_id = $this -> dao -> insert($i_data);

		$data['last_id'] = $last_id;

		$itx['guide_id'] = $last_id;
		$itx['bet_type'] = 0;// init
		$itx['result_amt'] = $base_amt;// init
		// $itx['balance'] = $base_amt;// init

		$this -> gtx_dao -> insert($itx);
		$this -> to_json($data);
	}

	public function send_result()
	{
		$com_id = $this -> get_post('com_id');
		$tab_id = $this -> get_post('tab_id');
		$bet_amt = $this -> get_post('bet_amt');
		$is_win = $this -> get_post('is_win');
		$rnd_str = $this -> get_post('rnd_str');
		$bet_balance = $this -> get_post('bet_balance');

		$data = array();
		$data = $this -> setup_user_data($data);
		$item = $this -> dao -> find_by_com_and_tab($data['login_user_id'], $com_id, $tab_id);

		$itx['guide_id'] = $item -> id;
		$itx['bet_type'] = 1;// 輸贏
		$itx['is_win'] = $is_win;// init
		$itx['bet_amt'] = $bet_amt;
		$itx['guess'] = $rnd_str;

		$bet_balance = empty($bet_balance) ? 50 : $bet_balance;
		$itx['p_banker'] = $bet_balance;
		$itx['p_player'] = 100 - intval($bet_balance);

		$is_win_count = $this -> session -> userdata('is_win_count');
		$is_win_count = empty($is_win_count) ? 0 : $is_win_count;

		$banance_diff = $this -> session -> userdata('banance_diff');
		$banance_diff = empty($banance_diff) ? 0 : $banance_diff;
		if($is_win == -1) {
			$is_win_count++;
			if($is_win_count >= 1) {
				// lose twice
				if($rnd_str == '莊') {
					$banance_diff = $banance_diff - 10;
				}
				if($rnd_str == '閒') {
					$banance_diff = $banance_diff + 10;
				}

				if($banance_diff > 20 ) {
					$banance_diff = 20; // max
				}
				if($banance_diff < -20 ) {
					$banance_diff = -20; // min
				}
			}
		} else {
			// reset
			$is_win_count = 0;
			$banance_diff = 0;
		}
		$this -> session -> set_userdata('banance_diff', $banance_diff);
		$this -> session -> set_userdata('is_win_count', $is_win_count);



		$last_tx = $this -> gtx_dao -> find_last_tx($item -> id);
		$r_amt = 0;
		if($is_win == 0 || $is_win == 99) { // tie
			$r_amt = $last_tx -> result_amt;
		}
		if($is_win == 1) { // win
			$r_amt = $last_tx -> result_amt + $bet_amt;
		}
		if($is_win == -1) { // loose
			$r_amt = $last_tx -> result_amt - $bet_amt;
		}
		$itx['result_amt'] = $r_amt;
		$new_tx_id = $this -> gtx_dao -> insert($itx);

		// update balance and diff
		$u_data['balance'] = $itx['result_amt'];
		$u_data['diff_amt'] = $itx['result_amt'] - $item -> base_amt;

		if($r_amt <= 0) {
			$data['is_finish'] = 1;
			$u_data['status'] = 1;
			$u_data['finish_time'] = date('Y-m-d H:i:s');
		}

		$this -> dao -> update($u_data, $item -> id);

		$data['last_id'] = $new_tx_id;


		$this -> to_json($data);
	}

	public function send_finish()
	{
		$com_id = $this -> get_post('com_id');
		$tab_id = $this -> get_post('tab_id');

		$data = array();
		$data = $this -> setup_user_data($data);
		$item = $this -> dao -> find_by_com_and_tab($data['login_user_id'], $com_id, $tab_id);

		$u_data['status'] = 1;
		$u_data['finish_time'] = date('Y-m-d H:i:s');

		$this -> dao -> update($u_data, $item -> id);

		$this -> to_json($data);
	}

	public function get_data() {
		$res = array();
		$data = $this -> get_posts(array(
			'length',
			'start',
			'columns',
			'search',
			'order',
			'user_id',
			'status'
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

			$items = $this -> dao -> query_ajax($q_data);
			$data['item'] = $items[0];
		}
		$this->load->view('mgmt/water/edit', $data);
	}

	public function insert() {
		$res = array();
		$id = $this -> get_post('id');
		$data = $this -> get_posts(array(
			'password',
			'member_name',
			'email',
			'mobile',
			'image_id'
		));

		if(empty($id)) {
			// insert
			$this -> dao -> insert($data);
		} else {
			// update
			unset($data['account']);
			$this -> dao -> update($data, $id);
		}

		$res['success'] = TRUE;
 		$this -> to_json($res);
	}

	public function dispatch() {
		$res = array();
		$id = $this -> get_post('id');
		$data = $this -> get_posts(array(
			'start_lat',
			'start_lng',
			'address',
			'address_note'
		));
		$this -> dao -> update($data, $id);

		$this -> dd_dao -> remove_all_by_take_record_id($id);
		$this -> dispatch_dao -> remove_all_by_take_record_id($id);

		$dispatch_id = $this -> dispatch_dao -> insert(array(
			'take_record_id' => $id
		));

		$res['success'] = TRUE;
		$res['dispatch_id'] = $dispatch_id;

		$dispatch = $this -> dispatch_dao -> find_by_id($dispatch_id);
		$res['dispatch_time'] = $dispatch -> dispatch_time;

		// dispatch drivers
		$distance_meter = 7000;

		$dispatch = $this -> dispatch_dao -> find_by_id($dispatch_id);
		$take_record_id = $dispatch -> take_record_id;
		$take_record = $this -> dao -> find_by_id($take_record_id);

		// find drivers
		$drivers = $this -> drivers_dao -> find_available_by_distance($take_record -> start_lat, $take_record -> start_lng, $distance_meter);
		if(count($drivers) > 0) {
			foreach($drivers as $each) {
				$this -> dd_dao -> insert(array(
					'dispatch_id' => $dispatch_id,
					'driver_id' => $each -> id,
					'take_record_id' => $take_record_id
				));
			}

			$tu_data['take_status'] = 1;
			$tu_data['dispatch_id'] = $dispatch_id;
			$this -> dao -> update($tu_data, $id);

		 	// push
		} else {
			$tu_data['take_status'] = -5; // 無車可派
			$this -> dao -> update($tu_data, $id);

			$res['error_msg'] = "目前無司機";
		}
 		$this -> to_json($res);
	}

	public function dispatch_detail($dispatch_id) {
		$res = array();
		$list = $this -> dd_dao -> find_all_by_dispatch_id($dispatch_id);
		$res['drivers'] = $list;

		$res['success'] = TRUE;
		$res['server_time'] = date('Y-m-d H:i:s');
 		$this -> to_json($res);
	}

	public function update_member_name () {
		$res['success'] = TRUE;
		$take_record_id = $this -> get_post('id');
		$member_name = $this -> get_post('member_name');
		if(!empty($take_record_id) && !empty($member_name)) {
			$tr = $this -> dao -> find_by_id($take_record_id);
			if(!empty($tr)) {
				$this -> members_dao -> update(array(
					'member_name' => $member_name
				), $tr -> member_id);
			}
		}

 		$this -> to_json($res);
	}

	public function delete($id) {
		$res['success'] = TRUE;
		$this -> dao -> delete_status($id, $this -> session -> userdata('user_id'));
		$this -> to_json($res);
	}

	public function search_mobile() {
		$mobile = $this -> get_post('mobile');
		$list = $this -> members_dao -> search_mobile($mobile);
		$res = array();
		$res['list'] = $list;
		$this -> to_json($res);
	}


	function sys_time() {
		$res = array();
		$res['ts'] = date('Y-m-d H:i:s');
		$this -> to_json($res);
	}
}
