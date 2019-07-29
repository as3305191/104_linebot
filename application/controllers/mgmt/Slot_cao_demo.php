<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Slot_cao_demo extends MY_Base_Controller {

	function __construct() {
		parent::__construct();
	}

	public function index() {
		$bet_amt = 680; // 下注

		$config = array();
		$config['bet_amt'] = $bet_amt;
		$config['corp_id'] = 1;
		$config['tab_id'] = 1;
		$config['user_id'] = 785;
		$config['hall_id'] = 0;

		$data = new stdClass;

		$n_res = $this -> curl -> simple_post("/api/slot_cao_game/bet",$config);
		$json = $n_res;
		$data = json_decode($n_res);
		$data -> json = $json;
		// $data = array_merge($data, $config);
		// echo "$n_res";
		$this->load->view('mgmt/slot_cao_demo/list', $data);
	}

}
