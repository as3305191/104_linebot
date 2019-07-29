<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Guide extends MY_Base_Controller {

	function __construct() {
		parent::__construct();
		$this -> load -> model('Guide_dao', 'dao');
		$this -> load -> model('Guide_tx_dao', 'gtx_dao');
		$this -> load -> model('Images_dao', 'img_dao');

		$this -> load -> model('Company_dao', 'c_dao');
		$this -> load -> model('Users_dao', 'u_dao');
		$this -> load -> model('Marquee_dao', 'm_dao');
		$this -> load -> model('Com_tab_dao', 'ct_dao');

		$this -> load -> model('Params_dao', 'params_dao');
	}

	public function index()
	{
		$data = array();
		$data = $this -> setup_user_data($data);
		$corp = $data['corp'];
		$data['param'] = $this -> params_dao -> find_by_corp_id($corp -> id);

		$user = $this -> u_dao -> find_by_id($data['login_user_id']);
		$data['l_user'] = $user;

		if(($user -> role_id != 1 && $user -> role_id != 99) && (empty($user -> end_time) || strtotime($user -> end_time) < time())) {
			$this->load->view('mgmt/guide/buy_info', $data);
			return;
		}
		$data['marquee_list'] = $this -> m_dao -> find_all_order();

		$this->load->view('mgmt/guide/list', $data);
	}

	public function com_select()
	{
		$data = array();
		$data = $this -> setup_user_data($data);
		$data['company_list'] = $this -> c_dao -> find_all();
		$data['guide_list'] = $this -> dao -> list_all_unfinished($data['login_user_id']);

		$user = $this -> u_dao -> find_by_id($data['login_user_id']);
		if(($user -> role_id != 1 && $user -> role_id != 99) && (empty($user -> end_time) || strtotime($user -> end_time) < time())) {
			redirect('mgmt/guide');
			return;
		}

		$this->load->view('mgmt/guide/com_select', $data);
	}

	public function table_select()
	{
		$com_id = $this -> get_get('com_id');

		$data = array();
		$data = $this -> setup_user_data($data);

		$user = $this -> u_dao -> find_by_id($data['login_user_id']);
		if(($user -> role_id != 1 && $user -> role_id != 99) && (empty($user -> end_time) || strtotime($user -> end_time) < time())) {
			redirect('mgmt/guide');
			return;
		}

		$data['company'] = $this -> c_dao -> find_by_id($com_id);
		$data['tab_list'] = $this -> ct_dao -> find_all_by_com_id($com_id);
		$this->load->view('mgmt/guide/table_select', $data);
	}

	public function set_yn() {
		$com_id = $this -> get_get('com_id');
		$tab_id = $this -> get_get('tab_id');
		$this -> session -> set_userdata('s_yn', 'yes');
		redirect("mgmt/guide/main?com_id=$com_id&tab_id=$tab_id");
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
			redirect('mgmt/guide');
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
		$has_3time_win_loose = 0;
		if(!empty($item)) {

			$last_tx = $this -> gtx_dao -> find_last_tx($item -> id);
			$last_tx_list = $this -> gtx_dao -> find_last_tx_count($item -> id, 5);
			$cont_win_count = $this -> gtx_dao -> cont_win_count($item -> id);
			$cont_loose_count = $this -> gtx_dao -> cont_loose_count($item -> id);
			$has_3time_win_loose = $this -> gtx_dao -> check3times_win_loose($item -> id);

			if($last_tx -> bet_type == 0) { // 初始
				$s_amt = intval($last_tx -> result_amt / 100);
			} else {
				if($last_tx -> is_win == 0 || $last_tx -> is_win == 99) {
					$s_amt = $last_tx -> bet_amt;
				}
				if($last_tx -> is_win == 1) {
					$s_amt = intval($item -> base_amt / 100);
					// echo "cont_win_count : $cont_win_count";
					if($cont_win_count == 1 || ($cont_win_count % 2 == 1)) {
						$s_amt = $last_tx -> bet_amt * 2;
					} else {
						$s_amt = intval($item -> base_amt / 100);
					}
				}
				if($last_tx -> is_win == -1) { // loose
					$s_amt = intval($item -> base_amt / 100);
				}

				if($cont_loose_count >= 5) { // 100 (輸）> 100(輸）>100(輸）> 100(輸）> 100((輸）> 連輸５把時 啟動 ２倍下注 變２００
					$s_amt = $last_tx -> bet_amt * 2;
				}

				if(count($last_tx_list) > 2 ) {
					$last1 = $last_tx_list[0];
					$last2 = $last_tx_list[1];
					if($last2 -> is_win == 1 && $last1 -> is_win == -1) {
						$s_amt = $last_tx -> bet_amt;
					}
				}

				if($has_3time_win_loose >= 3) {
					$this -> session -> set_userdata('last_3time_id', $last_tx -> id);
					if($last_tx -> is_win == -1) {
						$s_amt = $last_tx -> bet_amt * 2;
					}
				}

				$last_3time_id = $this -> session -> userdata('last_3time_id');
				if(count($last_tx_list) > 2 ) {
					$last2 = $last_tx_list[1];
					if($last_3time_id == $last2 -> id ) {
						$s_amt = intval($item -> base_amt / 100);
					}
				}

				$data['sec'] = $last_3time_id;

				// last ----------------
				if(($last_tx -> result_amt - $s_amt) < 0) {
					$s_amt = $last_tx -> result_amt;
				}
			}
		}
		$data['s_amt'] = $s_amt;
		$data['has_3time_win_loose'] = $has_3time_win_loose;
		$this->load->view('mgmt/guide/main', $data);
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
					$banance_diff = $banance_diff - 20;
				}
				if($rnd_str == '閒') {
					$banance_diff = $banance_diff + 20;
				}

				if($banance_diff > 40 ) {
					$banance_diff = 40; // max
				}
				if($banance_diff < -40 ) {
					$banance_diff = -40; // min
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
		$this->load->view('mgmt/guide/edit', $data);
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

	public function do_cancel() {
		$res = array();
		$id = $this -> get_post('id');
		$data['take_status'] = -4;
		$data['dispatch_id'] = 0;
		$data['driver_id'] = 0; // reset driver
		$data['assign_time'] = NULL;
		$this -> dao -> update($data, $id);

		// remove all dispatch
		$this -> dd_dao -> remove_all_by_take_record_id($id);
		$this -> dispatch_dao -> remove_all_by_take_record_id($id);

		$res['success'] = TRUE;
 		$this -> to_json($res);
	}

	public function do_response() {
		$res = array();
		$dispatch_id = $this -> get_post('dispatch_id');
		$driver_id = $this -> get_post('driver_id');
		$this -> dd_dao -> update_response($dispatch_id, $driver_id, 0, 0);

		$res['success'] = TRUE;
 		$this -> to_json($res);
	}

	public function do_assign() {
		$res = array();
		// take record id
		$id = $this -> get_post('id');
		$take_record = $this -> dao -> find_by_id($id);

		// 派遣
		$dd = $this -> dd_dao -> assign_choose($take_record -> dispatch_id);

		if(!empty($dd)) {
			// found, then do assign confirm and remove others
			$this -> dd_dao -> assign_confirm($take_record -> dispatch_id, $dd -> driver_id);

			// change satus and mark driver
			$data['take_status'] = 2;
			$data['driver_id'] = $dd -> driver_id;
			$data['assign_time'] = date('Y-m-d H:i:s');
			$this -> dao -> update($data, $id);

		} else {
			// not found, remove all
			// remove all dispatch
			$this -> dd_dao -> remove_all_by_take_record_id($id);
			$this -> dispatch_dao -> remove_all_by_take_record_id($id);

			// change satus and mark driver
			$data['take_status'] = -5; // no cars
			$data['driver_id'] = 0;
			$data['dispatch_id'] = 0;
			$data['assign_time'] = NULL;
			$this -> dao -> update($data, $id);

			$res['error_msg'] = "找不到司機分派";
		}

		$res['success'] = TRUE;
		$this -> to_json($res);
	}

	public function list_dispatch() {
		$res = array();
		$id = $this -> get_post('id');
		$data['take_status'] = -4;
		$this -> dao -> update($data, $id);

		$res['success'] = TRUE;
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

	public function rapid_add() {
		$res = array();

		$mobile = $this -> get_post('mobile');
		$list = $this -> members_dao -> find_all_by_mobile($mobile);
		$member_id = 0;
		if(count($list) > 0) {
			// exists
			$item = $list[0];
			$member_id = $item -> id;

			$ol_list = $this -> dao -> find_online_by_member($item -> id);
			if(count($ol_list) > 0 ) {
				$res['error_msg'] = "已有未結案派車紀錄";
			} else {
				$res['last_id'] = $this -> dao -> insert(array(
					'member_id' => $member_id
				));
			}

		} else {
			// creat new member
			$member_id = $this -> members_dao -> insert(array(
				'mobile' => $mobile
			));

			$res['last_id'] = $this -> dao -> insert(array(
				'member_id' => $member_id
			));
		}

		$this -> to_json($res);
	}

	function sys_time() {
		$res = array();
		$res['ts'] = date('Y-m-d H:i:s');
		$this -> to_json($res);
	}
}
