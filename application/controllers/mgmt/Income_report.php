<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Income_report extends MY_Base_Controller {

	function __construct() {
		parent::__construct();
		$this -> load -> model('Bonus_tx_dao', 'dao');
		$this -> load -> model('Products_dao', 'p_dao');
		$this -> load -> model('Pay_types_dao', 'pt_dao');
		$this -> load -> model('Users_dao', 'u_dao');
	}

	public function index()
	{
		$data = array();
		$data = $this -> setup_user_data($data);
		$this->load->view('mgmt/income_report/list', $data);
	}

	public function get_data() {
		$res = array();
		$data = $this -> get_posts(array(
			'length',
			'start',
			'columns',
			'search',
			'order',
			'user_id',
			'c_date'
		));

		$l_data = $this -> setup_user_data(array());
		$login_user_id = $l_data['login_user_id'];
		$login_user = $this -> u_dao -> find_by_id($login_user_id);
		$data['login_user'] = $login_user;

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
			$list = $this -> dao -> query_ajax($q_data);
			$item = $list[0];

			$data['item'] = $item;
		}

		$data['product_list'] = $this -> p_dao -> find_all();
		$data['pay_type_list'] = $this -> pt_dao -> find_all();

		$this->load->view('mgmt/income_report/edit', $data);
	}

	public function insert() {
		$res = array();
		$id = $this -> get_post('id');
		$s_data = $this -> setup_user_data(array());
		$data = $this -> get_posts(array(
			'product_id',
			'pay_type_id'
		));
		$data['user_id'] = $s_data['login_user_id'];

		if(empty($id)) {
			// insert
			$product = $this -> p_dao -> find_by_id($data['product_id']);
			$data['total_price'] =  $product -> price;
			$data['sn'] = 'B' . date('YmdHis');
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
		$this -> dao -> delete_status($id, $this -> session -> userdata('user_id'));
		$this -> to_json($res);
	}
}
