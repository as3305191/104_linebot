<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Roles extends MY_Base_Controller {

	function __construct() {
		parent::__construct();
		$this -> load -> model('Roles_dao', 'dao');
		$this -> load -> model('Users_dao', 'users_dao');
	}

	public function index()
	{
		$data = array();
		$this -> setup_user_data($data);
		$this->load->view('mgmt/roles/list', $data);
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

		$parent_id = $this -> get_post('parent_id');
		if(!empty($parent_id)) {
			$data['parent_id'] = $parent_id;
		}

		$items = $this -> dao -> query_ajax($data);
		$res['items'] = $items;
		$res['recordsFiltered'] = $this -> dao -> count_ajax($data);
		$res['recordsTotal'] = $this -> dao -> count_all_ajax($data);

		$this -> to_json($res);
	}

	public function edit($id) {
		$data = array();
		$data['id'] = $id;
		if(!empty($id)) {
			$item = $this -> dao -> find_by_id($id);
			if(!empty($item -> image_id)) {
				$item -> img = $this -> img_dao -> find_by_id($item -> image_id);
			}

			$data['item'] = $item;
			$data['menu_list'] = $this -> users_dao -> nav_list_with_role_id($id);
		} else {
			$data['menu_list'] = $this -> users_dao -> nav_list();
		}

		$this->load->view('mgmt/roles/edit', $data);
	}

	public function insert() {
		$res = array();
		$id = $this -> get_post('id');
		$data = $this -> get_posts(array(
			'role_name'
		));

		$nav_ids = $this -> get_post('nav_ids');
		$nav_powers = $this -> get_post('nav_powers');

		if(empty($id)) {
			// insert
			$id = $this -> dao -> insert($data);
		} else {
			// update
			$this -> dao -> update($data, $id);
		}

		$this -> dao -> update_role_power($id, $nav_ids, $nav_powers);

		$res['success'] = TRUE;
 		$this -> to_json($res);
	}

	public function delete($id) {
		$res['success'] = TRUE;
		$this -> dao -> delete($id);
		$this -> to_json($res);
	}
}
