<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Customer_service extends MY_Mgmt_Controller {

	function __construct() {
		parent::__construct();
		$this -> load -> model('Customer_service_dao', 'dao');
		$this -> load -> model('User_msg_dao', 'user_msg_dao');

	}

	public function index()
	{
		$data = array();
		$data = $this -> setup_user_data($data);
		$data['login_user'] = $this -> dao -> find_by_id($data['login_user_id']);
		$this->load->view('mgmt/customer_service/list', $data);
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
				'order',
			));
			$q_data['id'] = $id;
			$list = $this -> dao -> query_ajax($q_data);
			$item = $list[0];


			$data['item'] = $item;
		}

		$s_data = $this -> setup_user_data(array());
		$data['login_user'] = $this -> dao -> find_by_id($s_data['login_user_id']);
		$data['login_user_id'] = $s_data['login_user_id'];

		$this->load->view('mgmt/customer_service/edit', $data);
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
