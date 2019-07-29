<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Corp_users extends MY_Mgmt_Controller {

	function __construct() {
		parent::__construct();
		$this -> load -> model('Users_dao', 'dao');
		$this -> load -> model('Corp_dao', 'corp_dao');
	}

	public function index()
	{
		$data = array();
		$data = $this -> setup_user_data($data);
		$data['login_user'] = $this -> users_dao -> find_by_id($data['login_user_id']);
		$this->load->view('mgmt/corp_users/list', $data);
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
		$login_user = $this -> users_dao -> find_by_id($s_data['login_user_id']);
		if($login_user -> role_id == 99) {
			$data['is_sys_admin_user'] = 1;
		} else {
			$data['is_corp_admin_user'] = 1;
			$data['corp_id'] = $login_user -> corp_id;
		}

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

			$data['corp'] = $this -> corp_dao -> find_by_id($item -> corp_id);
		}

		$s_data = $this -> setup_user_data(array());
		$login_user = $this -> users_dao -> find_by_id($s_data['login_user_id']);
		$data['login_user'] = $login_user;
		if($login_user -> role_id == 99) {
			// all roles
			$data['role_list'] = $this -> dao -> find_sys_roles();
			$data['corp_list'] = $this -> corp_dao -> find_all_company();
		} else {
			$data['role_list'] = $this -> dao -> find_corp_roles();
			$data['corp_list'] = $this -> corp_dao -> find_my_company($login_user -> corp_id);
		}

		$this->load->view('mgmt/corp_users/edit', $data);
	}

	public function insert() {
		$res = array();
		$id = $this -> get_post('id');
		$data = $this -> get_posts(array(
			'account',
			'password',
			'image_id',
		));

		$role_id = $this -> get_post('role_id');
		if(!empty($role_id)) {
			$data['role_id'] = $role_id;
		}

		if(empty($id)) {
			$corp_id = $this -> get_post('corp_id');
			$data['corp_id'] = $corp_id;

			// get code
			$find_code = FALSE;
			while(!$find_code) {
				$code = generate_random_string();
				$c_list = $this -> dao -> find_all_by('code', $code);
				$find_code = (count($c_list) == 0);
				$data['code'] = $code;
			}
			$this -> dao -> insert($data);
		} else {
			// update
			$this -> dao -> update($data, $id);
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
}
