<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Baccarat extends MY_Mgmt_Controller {

	function __construct() {
		parent::__construct();
		$this -> load -> model('Baccarat_tab_dao', 'dao');
		$this -> load -> model('Params_dao', 'params_dao');
		$this -> load -> model('Users_dao', 'users_dao');
		$this -> load -> model('Images_dao', 'img_dao');
		$this -> load -> model('Corp_dao', 'corp_dao');

		$this -> load -> model('Baccarat_tab_round_dao', 'btr_dao');
		$this -> load -> model('Baccarat_tab_round_detail_dao', 'btrd_dao');
		$this -> load -> model('Baccarat_tab_tx_dao', 'tab_tx_dao');

		$this -> load -> model('User_level_dao', 'ul_dao');
	}

	public function index()
	{
		$data = array();
		$data = $this -> setup_user_data($data);
		$data['login_user'] = $this -> users_dao -> find_by_id($data['login_user_id']);

		if($data['login_user'] -> role_id == 99) {
			$data['corp_list'] = $this -> corp_dao -> find_all_company();
		}

		$this->load->view('mgmt/baccarat/list', $data);
	}

	public function get_data() {
		$res = array();
		$data = $this -> get_posts(array(
			'length',
			'start',
			'columns',
			'search',
			'order',
			'lang',
			'corp_id',
			'tab_type'
		));
		$s_data = $this -> setup_user_data(array());
		$login_user = $this -> users_dao -> find_by_id($s_data['login_user_id']);
		if($login_user -> role_id != 99) {
			$data['corp_id'] = $s_data['corp'] -> id;
		}

		$res['items'] = $this -> dao -> query_ajax($data);
		$res['recordsFiltered'] = $this -> dao -> count_ajax($data);
		$res['recordsTotal'] = $this -> dao -> count_all_ajax($data);

		$this -> to_json($res);
	}

	public function get_round_data() {
		$res = array();
		$data = $this -> get_posts(array(
			'length',
			'start',
			'columns',
			'search',
			'order',
			'lang',
			'corp_id',
			'tab_id'
		));
		$s_data = $this -> setup_user_data(array());
		$login_user = $this -> users_dao -> find_by_id($s_data['login_user_id']);
		if($login_user -> role_id != 99) {
			$data['corp_id'] = $s_data['corp'] -> id;
		}

		$res['items'] = $this -> btr_dao -> query_ajax($data);
		$res['recordsFiltered'] = $this -> btr_dao -> count_ajax($data);
		$res['recordsTotal'] = $this -> btr_dao -> count_all_ajax($data);

		$this -> to_json($res);
	}

	public function get_round_detail_data() {
		$res = array();
		$data = $this -> get_posts(array(
			'length',
			'start',
			'columns',
			'search',
			'order',
			'lang',
			'corp_id',
			'round_id'
		));
		$s_data = $this -> setup_user_data(array());
		$login_user = $this -> users_dao -> find_by_id($s_data['login_user_id']);
		if($login_user -> role_id != 99) {
			$data['corp_id'] = $s_data['corp'] -> id;
		}

		$res['items'] = $this -> btrd_dao -> query_ajax($data);
		$res['recordsFiltered'] = $this -> btrd_dao -> count_ajax($data);
		$res['recordsTotal'] = $this -> btrd_dao -> count_all_ajax($data);

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
			$q_data['show_closed'] = 1;
			$list = $this -> dao -> query_ajax($q_data);
			$item = $list[0];

			$data['item'] = $item;
		}

		$s_data = $this -> setup_user_data(array());
		$data['login_user'] = $this -> users_dao -> find_by_id($s_data['login_user_id']);
		$this->load->view('mgmt/baccarat/edit', $data);
	}

	public function insert() {
		$res = array();
		$id = $this -> get_post('id');
		$data = $this -> get_posts(array(
			'tab_name',
			'pos',
			'min_bet_1',
			'max_bet_1',
			'min_bet_2',
			'max_bet_2',
			'min_bet_3',
			'max_bet_3',
			'min_bet_4',
			'max_bet_4',
			'min_bet_5',
			'max_bet_5'
		));

		$status = $this -> get_post('status');
		if(empty($status)) {
			$data['status'] = 0;
		} else {
			$data['status'] = $status;
		}

		if(empty($id)) {
			// insert
			$corp_id = $this -> get_post('corp_id');
			$data['corp_id'] = $corp_id;
			$tab_type = $this -> get_post('tab_type');
			$data['tab_type'] = $tab_type;
			$this -> dao -> insert($data);
		} else {
			// update
			$this -> dao -> update($data, $id);
		}

		// create params
		$this -> params_dao -> find_by_corp_id($id);

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

	public function check_corp_code($id) {
		$code = $this -> get_post('corp_code');
		$list = $this -> dao -> find_all_by('corp_code', $code);
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

	public function add_diff() {
		$res = array();
		$tab_id = $this -> get_post('tab_id');
		$diff = $this -> get_post('diff');
		$i_data['tab_id'] = $tab_id;
		$i_data['tx_amt'] = $diff;
		$i_data['tx_amt_origin'] = $diff;
		$this -> tab_tx_dao -> insert($i_data);

		$res['success'] = TRUE;
		$this -> to_json($res);
	}
}
