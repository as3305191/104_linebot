<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Guide_records extends MY_Base_Controller {

	function __construct() {
		parent::__construct();
		$this -> load -> model('Guide_dao', 'dao');
		$this -> load -> model('Users_dao', 'users_dao');
		$this -> load -> model('Guide_tx_dao', 'gtx_dao');
	}

	public function index()
	{
		$data = array();
		$data = $this -> setup_user_data($data);
		$this->load->view('mgmt/guide_records/list', $data);
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
			'company_id'
		));

		$s_data = $this -> setup_user_data(array());
		$l_user = $this -> users_dao -> find_by_id($s_data['login_user_id']);
		if($l_user -> role_id == 99) {
			unset($data['user_id']);
		}

		$res['items'] = $this -> dao -> query_ajax($data);
		$res['recordsFiltered'] = $this -> dao -> count_ajax($data);
		$res['recordsTotal'] = $this -> dao -> count_all_ajax($data);

		$this -> to_json($res);
	}

	public function edit($id) {
		$data = array();
		$data['id'] = $id;
		$data['list'] = $this -> gtx_dao -> find_all_by_guide_id($id);

		$s_data = $this -> setup_user_data(array());
		$l_user = $this -> users_dao -> find_by_id($s_data['login_user_id']);
		$data['login_user'] = $l_user;
		$this->load->view('mgmt/guide_records/edit', $data);
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
}
