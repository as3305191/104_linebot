<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pay_chs extends MY_Base_Controller {

	function __construct() {
		parent::__construct();

		$this->load->helper('captcha');

		$this -> load -> model('Users_dao', 'users_dao');
		$this -> load -> model('Banks_dao', 'banks_dao');
		$this -> load -> model('Corp_dao', 'corp_dao');
		$this -> load -> model('App_version_dao', 'app_version_dao');

		include APPPATH . 'third_party/smsapi.class.php';
	}

	public function index() {
		$data = array();
		$item = $this -> app_version_dao -> find_by_id(1);
		$item_ios = $this -> app_version_dao -> find_by_id(2);
		$item_ios_switch = $this -> app_version_dao -> find_by_id(3);

		$item -> ios_version = $item_ios -> version;
		$item -> ios_switch = $item_ios_switch -> version;
		$data['item'] = $item;
		$this -> load -> view('pay_chs', $data);
	}


}
