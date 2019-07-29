<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Slot_demo extends MY_Base_Controller {

	function __construct() {
		parent::__construct();

		$this -> load -> model('Slot_demo_dao', 'sd_dao');
	}

	// public function test() {
	// 	$bet_amt = 100; // 下注
	//
	// 	$config = array();
	// 	$config['bet_amt'] = $bet_amt;
	// 	$config['corp_id'] = 1;
	// 	$config['tab_id'] = 1;
	// 	$config['user_id'] = 785;
	// 	$config['hall_id'] = -1;
	// 	$n_res = $this -> curl -> simple_post("/api/slot_game/bet",$config);
	// 	echo $n_res;
	// }
	//
	// public function ptest() {
	// 	$res = array();
	// 	$res['success'] = TRUE;
	// 	$res['try'] = $this -> get_post('try');
	// 	return $this -> to_json($res);
	// }

	public function index() {
		$bet_amt = 680; // 下注

		$config = array();
		$config['bet_amt'] = $bet_amt;
		$config['corp_id'] = 1;
		$config['tab_id'] = 1;
		$config['user_id'] = 785;
		$config['hall_id'] = 0;

		$n_res = $this -> curl -> simple_post("/api/slot_game/bet",$config);
		$data = json_decode($n_res);
		// $data = array_merge($data, $config);
		// echo "$data";
		$this->load->view('mgmt/slot_demo/list', $data);
	}

}
