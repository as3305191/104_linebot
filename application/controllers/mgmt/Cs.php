<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Cs extends MY_Mgmt_Controller {

	function __construct() {
		parent::__construct();
		$this -> load -> model('Cs_dao', 'dao');
		$this -> load -> model('Cs_talk_main_dao', 'cm_dao');
		$this -> load -> model('Users_dao', 'u_dao');
		$this -> load -> model('Images_dao', 'img_dao');

	}

	public function index()
	{
		$data = array();
		$data = $this -> setup_user_data($data);
		$data['login_user'] = $this -> u_dao -> find_by_id($data['login_user_id']);
		$data['last_id'] = $this -> dao -> last_id($data['login_user_id']);
		$this->load->view('mgmt/cs/list', $data);
	}

	public function chat_list($user_id)
	{
		$data = array();
		$data = $this -> setup_user_data($data);
		$data['list'] = $this -> dao -> list_all($user_id);
 		$this->load->view('mgmt/cs/chat_list', $data);
	}

	public function check_last_id()
	{
		$user_id = $this -> get_post('user_id');
		$data = array();
		$data['last_id'] = $this -> dao -> last_id($user_id);
 		$this -> to_json($data);
	}

	public function admin_chat_list($user_id)
	{
		$data = array();
		$data = $this -> setup_user_data($data);
		$data['list'] = $this -> dao -> list_all($user_id);
 		$this->load->view('mgmt/cs/admin_chat_list', $data);
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
		}

		$data['last_id'] = $this -> dao -> last_id($id);
		$this->load->view('mgmt/cs/edit', $data);
	}

	public function insert() {
		$res = array();
		$id = $this -> get_post('id');
		$data = $this -> get_posts(array(
			'account',
			'password',
			'user_name',
			'email',
			'line_id',
			'image_id'
		));

		$role_id = $this -> get_post('role_id');
		if(!empty($role_id)) {
			$data['role_id'] = $role_id;
		}

		if(empty($id)) {
			// insert
			$intro_code = $this -> get_post('intro_code');
			if(!empty($intro_code)) {
				$intro_user = $this -> dao -> find_by('code', $intro_code);
				if(!empty($intro_user)) {
					if($intro_user -> role_id == 2 || $intro_user -> role_id == 1) { // 經理人或系統管理者
						$data['manager_code'] = $intro_code;
						$data['manager_id'] = $intro_user -> id;
					} else {
						// find the manager
						$data['manager_code'] = $intro_user -> manager_code;
						$data['manager_id'] = $intro_user -> manager_id;
					}
					$data['intro_code'] = $intro_code;
					$data['intro_id'] = $intro_user -> id;
				}
			}
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

	public function do_send() {
		$res = array();
		$i_data = $this -> get_posts(array(
			'user_id',
			'send_user_id',
			'msg'
		));

		$last_id = $this -> dao -> insert($i_data);
		$res['last_id'] = $last_id;

		// update cs talk main
		$item = $this -> cm_dao -> find_by_id($i_data['user_id']);
		if(empty($item)) {
			// create
			$s_data = $this -> setup_user_data(array());

			$this -> cm_dao -> insert(array(
				'id' => $i_data['user_id'],
				'msg' => $i_data['msg'],
				'corp_id' => $s_data['corp'] -> id,
				'update_time' => date('Y-m-d H:i:s')
 			));
		} else {
			$this -> cm_dao -> update(array(
				'msg' => $i_data['msg'],
				'update_time' => date('Y-m-d H:i:s'),
				'unread' => ($i_data['user_id'] == $i_data['send_user_id'])
 			), $i_data['user_id']);
		}

		$res['success'] = TRUE;
 		$this -> to_json($res);
	}

	public function delete($id) {
		$res['success'] = TRUE;
		//$this -> dao -> delete_status($id, $this -> session -> userdata('user_id'));
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

	public function check_code() {
		$code = $this -> get_post('intro_code');
		$list = $this -> dao -> find_all_by('code', $code);
		$res = array();
		$res['valid'] = (count($list) > 0);
		$this -> to_json($res);
	}

	public function chg_user() {
		$user_id = $this -> get_post('user_id');
		$this -> session -> set_userdata('user_id', $user_id);
		$res = array();

		$this -> to_json($res);
	}
}
