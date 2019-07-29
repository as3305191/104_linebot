<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Slot_sun_tab extends MY_Mgmt_Controller {

	function __construct() {
		parent::__construct();
		$this -> load -> model('Slot_sun_tab_dao', 'dao');
		$this -> load -> model('Slot_sun_bet_dao', 'sb_dao');
		$this -> load -> model('Slot_sun_bet_pool_dao', 'sbp_dao');
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

		$this->load->view('mgmt/slot_sun_tab/list', $data);
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

		$data['show_all'] = 'YES';
		$list = $this -> dao -> query_ajax($data);
		foreach($list as $each) {
			$each -> pool_val_1 = $this -> sbp_dao -> sum_amt($each->id, 1);
			$each -> pool_val_2 = $this -> sbp_dao -> sum_amt($each->id, 2);
			$each -> pool_val_3 = $this -> sbp_dao -> sum_amt($each->id, 3);
			$each -> pool_val_4 = $this -> sbp_dao -> sum_amt($each->id, 4);
			$each -> pool_val_5 = $this -> sbp_dao -> sum_amt($each->id, 5);
			$each -> pool_val_6 = $this -> sbp_dao -> sum_amt($each->id, 6);
		}
		$res['items'] = $list;
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
			$q_data['show_all'] = 'YES';

			$list = $this -> dao -> query_ajax($q_data);
			$item = $list[0];

			$data['item'] = $item;
		}

		$s_data = $this -> setup_user_data(array());
		$data['login_user'] = $this -> users_dao -> find_by_id($s_data['login_user_id']);
		$this->load->view('mgmt/slot_sun_tab/edit', $data);
	}

	public function insert() {
		$res = array();
		$id = $this -> get_post('id');
		$data = $this -> get_posts(array(
			'tab_name',
			'status',
			'pos'
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

	public function add_diff() {
		$res = array();
		$tab_id = $this -> get_post('tab_id');
		$diff = $this -> get_post('diff');
		$pool_type = $this -> get_post('pool_type');

		$tab = $this -> dao -> find_by_id($tab_id);
		if(!empty($tab)) {
			$i_data['tab_id'] = $tab_id;
			$i_data['pool_type'] = $pool_type;
			$i_data['amt'] = $diff;
			$this -> sbp_dao -> insert($i_data);
		}

		$res['success'] = TRUE;
		$this -> to_json($res);
	}
}
