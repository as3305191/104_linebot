<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Member_alert extends MY_Base_Controller {

	function __construct() {
		parent::__construct();
		$this -> load -> model('Member_alert_dao', 'dao');
		$this -> load -> model('Images_dao', 'img_dao');
	}

	public function index()
	{
		$data = array();
		$this -> setup_user_data($data);
		$this->load->view('mgmt/member_alert/list', $data);
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
		if(!empty($id)) {
			$q_data = $this -> get_posts(array(
				'length',
				'start',
				'columns',
				'search',
				'order',
				'company_id'
			));
			$q_data['id'] = $id;
			$items = $this -> dao -> query_ajax($q_data);

			$item = $items[0];
			if(!empty($item -> image_id)) {
				$item -> img = $this -> img_dao -> find_by_id($item -> image_id);
			}
			$data['item'] = $item;
		}
		$data['deal_status_list'] = $this -> dao -> find_all_deal_status();

		$this->load->view('mgmt/member_alert/edit', $data);
	}

	public function insert() {
		$res = array();
		$id = $this -> get_post('id');
		$data = $this -> get_posts(array(
			'deal',
			'deal_status'
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

	public function delete($id) {
		$res['success'] = TRUE;
		$this -> dao -> delete_status($id, $this -> session -> userdata('user_id'));
		$this -> to_json($res);
	}
}
