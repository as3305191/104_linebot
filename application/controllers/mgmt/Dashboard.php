<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Dashboard extends MY_Base_Controller {

	function __construct() {
		parent::__construct();
		$this -> load -> model('Images_dao', 'img_dao');

		$this -> load -> model('Params_dao', 'params_dao');
	}

	public function index()
	{
		$data = array();
		$data = $this -> setup_user_data($data);
		$corp = $data['corp'];
		$data['param'] = $this -> params_dao -> find_by_corp_id($corp -> id);

		$this->load->view('mgmt/dashboard/list', $data);
	}
}
