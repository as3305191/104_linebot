<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Manual_edit extends MY_Mgmt_Controller {

	function __construct() {
		parent::__construct();
		$this -> load -> model('Manual_dao', 'dao');
		$this -> load -> model('Images_dao', 'img_dao');

	}

	public function index()
	{
		$data = array();
		$data = $this -> setup_user_data($data);
		$this->load->view('mgmt/manual/list', $data);
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
		if(!empty($id)) {
			$q_data = $this -> get_posts(array(
				'length',
				'start',
				'columns',
				'search',
				'order'
			));
			$s = $this -> setup_user_data(array());

			$data['item'] = $this -> dao -> get_val($s['corp'] -> id);
		}

		$this->load->view('mgmt/manual/edit', $data);
	}

	public function insert() {
		$res = array();
		$this -> dao -> set_val($this -> get_post('val'), $this -> get_post('id'));

		$res['success'] = TRUE;
 		$this -> to_json($res);
	}
}
