<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User_group extends MY_Base_Controller {

	function __construct() {
		parent::__construct();
		$this -> load -> model('User_group_dao', 'dao');
	}

	public function index()
	{
		$data = array();
		$this -> setup_user_data($data);
		$this->load->view('mgmt/user_group/list', $data);
	}

	public function get_data() {
		$res = array();
		$data = $this -> get_posts(array(
			'length',
			'start',
			'columns',
			'search',
			'order',
			'company_id'
		));

		$res['items'] = $this -> dao -> query_ajax($data);
		$res['recordsFiltered'] = $this -> dao -> count_ajax($data);
		$res['recordsTotal'] = $this -> dao -> count_all_ajax($data);

		$this -> to_json($res);
	}

	public function edit($id) {
		$data = array();

		$q_data = $this -> get_posts(array(
			'length',
			'start',
			'columns',
			'search',
			'order',
			'company_id'
		));
		$q_data['id'] = $id;

		if(!empty($id)) {
			$items = $this -> dao -> query_ajax($q_data);
			$data['item'] = $items[0];
		}

		$this->load->view('mgmt/user_group/edit', $data);
	}

	public function insert() {
		$res = array();
		$id = $this -> get_post('id');
		$data = $this -> get_posts(array(
			'group_name'
		));

		if(empty($id)) {
			// insert
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

	public function check_account() {
		$account = $this -> get_post('account');
		$list = $this -> dao -> find_all_by('account', $account);
		$res = array();
		if (count($list) > 0) {
			$res['valid'] = FALSE;
		} else {
			$res['valid'] = TRUE;
		}
		$this -> to_json($res);
	}
}
