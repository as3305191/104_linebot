<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Wallet_tx extends MY_Base_Controller {

	function __construct() {
		parent::__construct();
		$this -> load -> model('Wallet_tx_dao', 'dao');
	}

	public function index()
	{
		$data = array();
		$data = $this -> setup_user_data($data);

		$data['sum_amt'] = $this -> dao -> get_sum_amt($data['login_user_id']);
		$this->load->view('mgmt/wallet_tx/list', $data);
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
			'company_id'
		));

		$res['items'] = $this -> dao -> query_ajax($data);
		$res['recordsFiltered'] = $this -> dao -> count_ajax($data);
		$res['recordsTotal'] = $this -> dao -> count_all_ajax($data);

		$this -> to_json($res);
	}
}
