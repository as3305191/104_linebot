<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Cs extends MY_Base_Controller {

	function __construct() {
		parent::__construct();

		$this -> load -> model('Users_dao', 'users_dao');
		$this -> load -> model('Customer_service_dao', 'cs_dao');
		$this -> load -> model('Corp_dao', 'corp_dao');

	}

	public function list_my($user_id) {
		$data = array();
		$data['user_id'] = $user_id;

		$this -> cs_dao -> mark_read($user_id);

		$data['all_list'] = $this -> cs_dao -> list_all($user_id);
		$data['yet_answer_list'] = $this -> cs_dao -> list_by_status($user_id,0);
		$data['answer_list'] = $this -> cs_dao -> list_by_status($user_id,1);

		$this -> load -> view('cs/list', $data);
	}


}
