<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pay_special extends MY_Mgmt_Controller {

	function __construct() {
		parent::__construct();
		$this -> load -> model('Pay_special_dao', 'dao');
		$this -> load -> model('Params_dao', 'params_dao');
		$this -> load -> model('Users_dao', 'users_dao');
		$this -> load -> model('Images_dao', 'img_dao');
		$this -> load -> model('Corp_dao', 'corp_dao');

		$this -> load -> model('Baccarat_tab_round_dao', 'btr_dao');
		$this -> load -> model('Baccarat_tab_round_detail_dao', 'btrd_dao');
		$this -> load -> model('Baccarat_tab_tx_dao', 'tab_tx_dao');

		$this -> load -> model('User_level_dao', 'ul_dao');
	}

	public function index()
	{
		$data = array();
		$data = $this -> setup_user_data($data);
		$data['login_user'] = $this -> users_dao -> find_by_id($data['login_user_id']);

		if($data['login_user'] -> role_id == 99) {
			$data['corp_list'] = $this -> corp_dao -> find_all_company();
		} else {
			$data['corp_list'] = $this -> corp_dao -> find_all_by('id', $data['login_user'] -> corp_id); // my corp only
		}

		$this->load->view('mgmt/pay_special/list', $data);
	}

	public function get_data() {
		$res = array();
		$data = $this -> get_posts(array(
			'length',
			'start',
			'columns',
			'search',
			'order',
			'lang',
			'corp_id',
			'tab_type'
		));
		$s_data = $this -> setup_user_data(array());
		$login_user = $this -> users_dao -> find_by_id($s_data['login_user_id']);
		if($login_user -> role_id != 99) {
			$data['corp_id'] = $s_data['corp'] -> id;
		}

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
			$q_data['id'] = $id;
			$q_data['show_closed'] = 1;
			$list = $this -> dao -> query_ajax($q_data);
			$item = $list[0];

			$data['item'] = $item;
		}

		$s_data = $this -> setup_user_data(array());
		$data['login_user'] = $this -> users_dao -> find_by_id($s_data['login_user_id']);
		$this->load->view('mgmt/pay_special/edit', $data);
	}

	public function insert() {
		$res = array();
		$id = $this -> get_post('id');
		$data = $this -> get_posts(array(
			'ps_name',
			'dead_line',
			'bonus_percent',
			'wash_times',
			'min_pay',
			'max_pay'
		));

		if(empty($id)) {
			// insert
			$corp_id = $this -> get_post('corp_id');
			$data['corp_id'] = $corp_id;
			$this -> dao -> insert($data);
		} else {
			// update
			$this -> dao -> update($data, $id);
		}

		// create params
		$this -> params_dao -> find_by_corp_id($id);

		$res['success'] = TRUE;
 		$this -> to_json($res);
	}

	public function delete($id) {
		$res['success'] = TRUE;
		$this -> dao -> delete_status($id, $this -> session -> userdata('user_id'));
		$this -> to_json($res);
	}


}
