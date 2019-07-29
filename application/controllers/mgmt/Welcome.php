<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Welcome extends MY_Base_Controller {

	function __construct() {
		parent::__construct();
		$this -> load -> model('Com_tx_dao', 'com_tx_dao');
		$this -> load -> model('Users_dao', 'users_dao');
		$this -> load -> model('Transaction_record_coins_dao', 'transaction_record_coins_dao');

	}

	public function index()
	{
		$data = array();
		$date= date("Y-m-d");
		$samt_fish_tab = $this -> com_tx_dao -> get_sum_amt_day($date);
		$samt_users= $this -> users_dao -> get_sum_amt_day($date);
		$samt_coin= $this -> transaction_record_coins_dao -> get_sum_amt_day($date);

		$data['samt_fish_tab'] = $samt_fish_tab;
		$data['samt_users'] = $samt_users;
		$data['samt_coin'] = $samt_coin;
		$this -> setup_user_data($data);
		$this->load->view('mgmt/welcome/list', $data);
	}

	public function get_data() {
		$res = array();
		$date = $this -> get_post('date');

		if(empty($date)){
			$date= date("Y-m-d");
		}
		$samt_fish_tab = $this -> com_tx_dao -> get_sum_amt_day($date);
		$samt_users= $this -> users_dao -> get_sum_amt_day($date);
		$samt_coin= $this -> transaction_record_coins_dao -> get_sum_amt_day($date);

		$res['samt_fish_tab'] = $samt_fish_tab;
		$res['samt_users'] = $samt_users;
		$res['samt_coin'] = $samt_coin;


		$this -> to_json($res);
	}
}
