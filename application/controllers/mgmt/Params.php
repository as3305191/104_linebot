<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Params extends MY_Mgmt_Controller {

	function __construct() {
		parent::__construct();
		$this -> load -> model('Params_dao', 'dao');
		$this -> load -> model('Images_dao', 'img_dao');
	}

	public function index()
	{
		$data = array();
		$data = $this -> setup_user_data($data);
		$this->load->view('mgmt/params/list', $data);
	}

	public function get_data() {
		$res = array();

		$res['items'] = array();
		$res['recordsFiltered'] = array();
		$res['recordsTotal'] = array();

		$this -> to_json($res);
	}

	public function edit($user_id) {
		$user = $this -> users_dao -> find_by_id($user_id);
		$item = $this -> dao -> find_by_corp_id($user -> corp_id);
		$data['item'] = $item;

		$this->load->view('mgmt/params/edit', $data);
	}

	public function insert() {
		$res = array();

		$id = $this -> get_post('id');
		$data = $this -> get_posts(array(
			'total_amt_0',
			'total_amt_1',
			'online_amt_0',
			'online_amt_1'
		));
		$this -> dao -> update($data, $id);

		$res['success'] = TRUE;
 		$this -> to_json($res);
	}

	public function delete($id) {
		$res['success'] = TRUE;
		$this -> dao -> delete($id);
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
