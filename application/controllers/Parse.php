<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Parse extends MY_Base_Controller {

	function __construct() {
		parent::__construct();

		$this -> load -> model('Users_dao', 'users_dao');
		$this -> load -> model('Banks_dao', 'banks_dao');
		$this -> load -> model('Corp_dao', 'corp_dao');

	}

	public function index() {
		$data = array();
		$this -> load -> view('parse', $data);
	}


}
