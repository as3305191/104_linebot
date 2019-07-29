<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Push extends MY_Base_Controller {

	function __construct() {
		parent::__construct();
		$this -> load -> model('Push_dao', 'dao');
	}

	public function index()
	{
		$data = array();
		$this -> setup_user_data($data);
		$this->load->view('mgmt/push/list', $data);
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
			$data['item'] = $item;
		}
		$this->load->view('mgmt/push/edit', $data);
	}

	public function insert() {
		$res = array();
		$id = $this -> get_post('id');
		$data = $this -> get_posts(array(
			'title',
			'content',
			'image_id'
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
		$this -> dao -> delete($id);
		$this -> to_json($res);
	}

	function do_push($id) {
		$res = array('success' => TRUE);
		$item = $this -> dao -> find_by_id($id);
		if(!empty($item)) {
			// push to all
			$this -> curl -> simple_post(PUSH_URL . "/{$item->id}", array(
				"access_token" => CHANNEL_ACCESS_TOKEN
			));

			$this -> dao -> update(array(
				'push_time' => date("Y-m-d H:i:s"),
				'is_push' => 1,
			), $id);
		}

		$this -> to_json($res);
	}
}
