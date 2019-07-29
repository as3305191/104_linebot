<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class App extends MY_Base_Controller {
	function __construct() {
		ini_set('memory_limit', '1024M');
		parent::__construct();
	}

	public function index()
	{
		echo "welcome index";
	}
	

	public function corp($corp_code)
	{
		$data = array();
		$this -> load -> model('Users_dao', 'users_dao');
		$this -> load -> model('Images_dao', 'img_dao');
		$this -> load -> model('Marquee_dao', 'marquee_dao');
		$this -> load -> model('Corp_dao', 'corp_dao');

		$corp = $this -> corp_dao -> find_by('corp_code', $corp_code);

		if(empty($corp)) {
			echo "corp not found";
			return;
		}

		// setup user data
		$data = $this -> setup_user_data($data);

		// get user name
		$user_id = $data['login_user_id'];
		$user = $this -> users_dao -> find_me($user_id);

		$corp = $this -> corp_dao -> find_by_id($user -> corp_id);
		if($corp_code != $corp -> corp_code) {
			echo "wrong corp";
			return;
		}

		// check mobile valid
		if((($user -> role_id == 2 || $user -> role_id == 3) // only 會員 & 經理人 & cht
			&& $user -> is_valid_mobile == 0)) {
			if($user -> lang != 'chs') {
				//redirect('login/mobile_valid');
			} else if(strtotime($user -> create_time) > strtotime(date('2017-08-15 00:00:00')) ){
				//redirect('login/mobile_valid');
			}
		}

		$data['login_user_name'] = $user -> user_name;
		$data['login_user'] = $user;

		$user_img = $this -> img_dao -> find_by_id($user -> image_id);
		$user_img_url = "";
		if(!empty($user_img)) {
			$user_img_url = base_url('mgmt/images/get/' . $user_img -> id);
		}

		$data['user_img_url'] = $user_img_url;

		// group list
		$data['group_list'] = $this -> users_dao -> find_group_users($user_id);

		// get menu data
		$list = $this -> users_dao -> nav_list_by_role_id($user -> role_id);
		$data['menu_list'] = $list;

		// marquee list
		$data['marquee_list'] = $this -> marquee_dao -> find_all_order();

		$this->load->view('layout/main', $data);
	}


	public function dash()
	{
		$data = array();
		$this -> load -> model('Users_dao', 'users_dao');
		$this -> load -> model('Images_dao', 'img_dao');
		$this -> load -> model('Marquee_dao', 'marquee_dao');
		$this -> load -> model('Params_dao', 'params_dao');

		// setup user data
		$data = $this -> setup_user_data($data);

		// get user name
		$user_id = $data['login_user_id'];
		$user = $this -> users_dao -> find_me($user_id);

		// check mobile valid
		if((($user -> role_id == 2 || $user -> role_id == 3) // only 會員 & 經理人 & cht
			&& $user -> is_valid_mobile == 0)) {
			if($user -> lang != 'chs') {
				redirect('login/mobile_valid');
			} else if(strtotime($user -> create_time) > strtotime(date('2017-08-15 00:00:00')) ){
				redirect('login/mobile_valid');
			}
		}

		$data['login_user_name'] = $user -> user_name;
		$data['login_user'] = $user;

		$user_img = $this -> img_dao -> find_by_id($user -> image_id);
		$user_img_url = "";
		if(!empty($user_img)) {
			$user_img_url = base_url('mgmt/images/get/' . $user_img -> id);
		}

		$data['user_img_url'] = $user_img_url;

		// group list
		$data['group_list'] = $this -> users_dao -> find_group_users($user_id);

		// get menu data
		$list = $this -> users_dao -> nav_list_by_role_id($user -> role_id);
		$data['menu_list'] = $list;

		// marquee list
		$data['marquee_list'] = $this -> marquee_dao -> find_all_order();

		$corp = $data['corp'];
		$data['param'] = $this -> params_dao -> find_by_corp_id($corp -> id);

		$this->load->view('dash', $data);
	}

	public function test() {
		$res['success'] = TRUE;
		$this -> load -> model('Users_dao', 'users_dao');
		$list = $this -> users_dao -> nav_list();
		$res['list'] = $list;
		$this -> to_json($res);
	}
}
