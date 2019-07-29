<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Slot_sun_bet extends MY_Mgmt_Controller {

	function __construct() {
		parent::__construct();
		$this -> load -> model('Slot_sun_bet_mgmt_dao', 'dao');
		$this -> load -> model('Slot_sun_tab_dao', 'tab_dao');
		$this -> load -> model('Params_dao', 'params_dao');
		$this -> load -> model('Users_dao', 'users_dao');
		$this -> load -> model('Images_dao', 'img_dao');
		$this -> load -> model('Corp_dao', 'corp_dao');

		$this -> load -> model('Baccarat_tab_round_dao', 'btr_dao');
		$this -> load -> model('Baccarat_tab_round_detail_dao', 'btrd_dao');

		$this -> load -> model('User_level_dao', 'ul_dao');
	}

	public function index()
	{
		$data = array();
		$data = $this -> setup_user_data($data);
		$data['login_user'] = $this -> users_dao -> find_by_id($data['login_user_id']);

		if($data['login_user'] -> role_id == 99) {
			$data['corp_list'] = $this -> corp_dao -> find_all_company();
		}

		$data['tab_list'] = $this -> tab_dao -> find_all();
		$this->load->view('mgmt/slot_sun_bet/list', $data);
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
			'tab_type',
			'create_date',
			's_user_name',
			's_tab_id',
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
		$this->load->view('mgmt/slot_sun_bet/edit', $data);
	}
}
