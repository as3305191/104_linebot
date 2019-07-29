<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Agent_type extends MY_Mgmt_Controller {

	function __construct() {
		parent::__construct();
		$this -> load -> model('Agent_type_dao', 'dao');
		$this -> load -> model('Images_dao', 'img_dao');
		$this -> load -> model('Game_types_dao', 'gt_dao');
	}

	public function index()
	{
		$data = array();
		$this -> setup_user_data($data);
		$this->load->view('mgmt/agent_type/list', $data);
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

		$s = $this -> setup_user_data(array());
		$data['corp_id'] = $s['corp'] -> id;

		$res['items'] = $this -> dao -> query_ajax($data);
		$res['recordsFiltered'] = $this -> dao -> count_ajax($data);
		$res['recordsTotal'] = $this -> dao -> count_all_ajax($data);

		$this -> to_json($res);
	}

	public function edit($id) {
		$data = array();

		$data = $this -> setup_user_data($data);

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

		$data['gt_list'] = $this -> gt_dao -> find_all();

		$this->load->view('mgmt/agent_type/edit', $data);
	}

	public function insert() {
		$res = array();
		$id = $this -> get_post('id');
		$data = $this -> get_posts(array(
			'bonus',
			'win_loose_bonus',
		));

		if(empty($id)) {
			// insert
			$data['corp_id'] = $this -> get_post('corp_id');
			$this -> dao -> insert($data);
		} else {
			// update
			// $data['corp_id'] = $this -> get_post('corp_id');
			$this -> dao -> update($data, $id);
		}

		$res['success'] = TRUE;
 		$this -> to_json($res);
	}

	public function delete($id) {
		$res['success'] = TRUE;
		$this -> dao -> delete($id);
		$this -> to_json($res);
	}

}
