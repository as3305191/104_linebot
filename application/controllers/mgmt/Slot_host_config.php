<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Slot_host_config extends MY_Mgmt_Controller {

	function __construct() {
		parent::__construct();
		$this -> load -> model('Slot_host_config_dao', 'dao');
	}

	public function index()
	{
		$data = array();
		$data = $this -> setup_user_data($data);
		$this->load->view('mgmt/slot_host_config/list', $data);
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

		$res['items'] = array();
		$res['recordsFiltered'] = 0;
		$res['recordsTotal'] = 0;

		$this -> to_json($res);
	}

	public function edit($id) {
		$data = array();
		$data['id'] = $id;
		$data = $this -> setup_user_data($data);
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

		$this->load->view('mgmt/slot_host_config/edit', $data);
	}

	public function insert() {
		$res = array();
		$id = $this -> get_post('id');
		$data = $this -> get_posts(array(
			'pool_1_pct',
			'pool_2_pct',
			'pool_3_pct',
			'pool_4_pct',
			'pool_5_pct',
			'pool_6_pct',
			'pool_7_pct',
			'pool_8_pct',
			'pool_9_pct',
			'pool_10_pct',
			'pool_11_pct',
			'pool_12_pct',
			'win_pct',
			'sp_3_pct',
			'sp_6_pct',
			'sp_9_pct',
			'sp_12_pct',
			'sp_18_pct',
			'sp_24_pct',
			'sp_36_pct',
			'sp_48_pct',
			'sp_72_pct',
			'is_sp_pct'
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
}
