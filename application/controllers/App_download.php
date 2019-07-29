<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class App_download extends MY_Base_Controller {

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
		$data['plist_url'] = "https://wa-lotterygame.com/wa_backend/data/manifest.plist";
		$this -> load -> view('app_download', $data);
	}


	public function ty() {
		$data = array();
		$data['plist_url'] = "https://wa-lotterygame.com/wa_backend/data/ty/manifest.plist";
		$this -> load -> view('app_download', $data);
	}


}
