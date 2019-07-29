<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Config extends MY_Mgmt_Controller {

	function __construct() {
		parent::__construct();
		$this -> load -> model('Config_dao', 'dao');
		$this -> load -> model('Images_dao', 'img_dao');
		$this -> load -> model('Corp_dao', 'corp_dao');

	}

	public function index()
	{
		$data = array();
		$data = $this -> setup_user_data($data);
		$this->load->view('mgmt/config/list', $data);
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
		$data = $this -> setup_user_data($data);
		$data['item'] = $this -> corp_dao -> find_by_id(1);
		$this->load->view('mgmt/config/edit', $data);
	}

	public function insert() {
		$res = array();
		$id = $this -> get_post("id");
		$data = $this -> get_posts(array(
			'is_maintain',
			'maintain_start_time',
			'maintain_end_time',
			'is_maintain_production',
			'maintain_start_time_production',
			'maintain_end_time_production',
			'register_reward_amt',
			'transfer_gift_pct',
			'fish_jp_amt_thresh',
		));

		$this -> corp_dao -> update($data, $id);
		$res['success'] = TRUE;
 		$this -> to_json($res);
	}
}
