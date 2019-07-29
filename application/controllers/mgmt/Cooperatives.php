<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Cooperatives extends MY_Base_Controller {

	function __construct() {
		parent::__construct();
		$this -> load -> model('Cooperatives_dao', 'dao');
		$this -> load -> model('Images_dao', 'img_dao');
		$this -> load -> model('Zip_dao', 'zip_dao');
	}

	public function index()
	{
		$data = array();
		$this -> setup_user_data($data);
		$this->load->view('mgmt/cooperatives/list', $data);
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
		$data['id'] = $id;

		// city and district
		$data['city_list'] = $this -> zip_dao -> find_all_city(TRUE);

		if(!empty($id)) {
			$item = $this -> dao -> find_by_id($id);
			if(!empty($item -> image_id)) {
				$item -> img = $this -> img_dao -> find_by_id($item -> image_id);
			}
			// district
			$data['district_list'] = $this -> zip_dao -> find_district_by_city($item -> city);
			$data['item'] = $item;
		} else {
			// insert
			$data['district_list'] = $this -> zip_dao -> find_district_by_city($data['city_list'][0]-> city);
		}
		$this->load->view('mgmt/cooperatives/edit', $data);
	}

	public function insert() {
		$res = array();
		$id = $this -> get_post('id');
		$data = $this -> get_posts(array(
			'cooperative_name',
			'init_number',
			'chg_number',
			'phone',
			'city',
			'district',
			'address',
			'chair_man'
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
