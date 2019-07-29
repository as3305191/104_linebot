<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Customer_service_line extends MY_Mgmt_Controller {

	function __construct() {
		parent::__construct();
		$this -> load -> model('Users_dao', 'users_dao');
		$this -> load -> model('Customer_service_line_dao', 'cs_line_dao');
		$this -> load -> model('Customer_service_line_room_dao', 'cs_line_room_dao');
		$this -> load -> model('User_msg_dao', 'user_msg_dao');

	}

	public function index()
	{
		$data = array();
		$data = $this -> setup_user_data($data);
		$data['login_user'] = $this -> users_dao -> find_by_id($data['login_user_id']);
		$this->load->view('mgmt/customer_service_line/list', $data);
	}

	public function get_data() {
		$res = array();
		$data = $this -> get_posts(array(
			'length',
			'start',
			'columns',
			'search',
			'order',
		));

		$s_data = $this -> setup_user_data(array());
		$data['corp_id'] = $s_data['corp'] -> id;

		$res['items'] = $this -> cs_line_room_dao -> query_ajax($data);
		$res['recordsFiltered'] = $this -> cs_line_room_dao -> count_ajax($data);
		$res['recordsTotal'] = $this -> cs_line_room_dao -> count_all_ajax($data);

		$this -> to_json($res);
	}

	public function edit($id) {
		$data = array();
		$data['id'] = $id;
		if(!empty($id)) {
			$room = $this -> cs_line_room_dao -> find_by_id($id);
			$data['room'] = $room;
		}

		$this->load->view('mgmt/customer_service_line/edit', $data);
	}

	public function chat_list($user_id) {
		$data = array();
		$data['msg_list'] = $this -> cs_line_dao -> list_msg_by_user($user_id);

		$this->load->view('mgmt/customer_service_line/chat_list', $data);
	}

	public function insert() {
		$res = array();
		$id = $this -> get_post('id');
		$data = $this -> get_posts(array(
			'answer',
		));

		$s_data = $this -> setup_user_data(array());
		$data['answer_user_id'] = $s_data['login_user_id'];

		if(empty($id)) {

			$this -> dao -> insert($data);
		} else {
			// update
			$data['answer_time'] = date("Y-m-d H:i:s");
			$this -> dao -> update($data, $id);

			$item = $this -> dao -> find_by_id($id);

			// add to message list
			$this -> user_msg_dao -> insert(array(
				'user_id' => $item -> user_id,
				'msg' => "問題:{$item->question} 客服回覆:{$item->answer}",
			));
		}

		$res['success'] = TRUE;
 		$this -> to_json($res);
	}

	public function add_msg() {
		$res = array();
		$user_id = $this -> get_post('user_id');
		$msg = $this -> get_post('msg');

		$user = $this -> users_dao -> find_by_id($user_id);

		$s_data = $this -> setup_user_data(array());
		$reply_user_id = $s_data['login_user_id'];
		$data['is_cs'] = 1; // 客服回覆
		$this -> cs_line_dao -> add_cs_msg($user_id, $msg, $reply_user_id);

		// send to line
		$p = array();
		$p['to'] = $user -> line_sub;
		$p['messages'][] = array(
			"type" => "text",
			"text" => $msg
		);
		$ret = call_line_api("POST", "https://api.line.me/v2/bot/message/push", json_encode($p), CHANNEL_ACCESS_TOKEN);

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
