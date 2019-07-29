<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Lottery extends MY_Base_Controller {

	function __construct() {
		parent::__construct();

		$this -> load -> model('Lottery_dao', 'dao');
	}

	public function index()
	{
		$data = array();
		$this -> setup_user_data($data);

		// $this -> to_json($data);

		$this->load->view('mgmt/lottery/list', $data);
	}

	public function get_data() {
		$res = array();

		$data = $this -> get_posts(array(
			'length',
			'start',
			'columns',
			'search',
			'order',
			'user_id'
		));

		$res['items'] = $this -> dao -> query_ajax($data);
		$res['recordsFiltered'] = $this -> dao -> count_ajax($data);
		$res['recordsTotal'] = $this -> dao -> count_all_ajax($data);

		$this -> to_json($res);
	}

	public function edit($id) {
		$data = array();
		if(!empty($id)) {
			$item = $this -> dao -> find_by_id($id);
			$data['item'] = $item;
		}


		$this->load->view('mgmt/lottery/edit', $data);
	}

	public function insert() {
		$res = array();
		$res['success'] = "true";

		$id = $this -> get_post("item_id");

		$data = $this -> get_posts(array(
			'sn',
			'lottery_name',
			'price',
			'is_basic',
			'image_id',
			'win_image_id',
		));
		$price = intval($data['price']);
		$data['ratio']= 50000;
		$data['total_num']= $price/50000;

		if(!empty($id)){
			$this -> dao -> update($data, $id);
		} else {
			$id = $this -> dao ->insert($data);
		}

		$this -> to_json($res);
	}

	function delete($id) {
		$res = array();
		$res['success'] = "true";
		$this -> dao -> delete($id);
		$this -> to_json($res);
	}
}
