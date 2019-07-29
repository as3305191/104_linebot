<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Lucky_draw extends MY_Mgmt_Controller {

	function __construct() {
		parent::__construct();
		$this -> load -> model('Corp_dao', 'dao');
		$this -> load -> model('Params_dao', 'params_dao');
		$this -> load -> model('Users_dao', 'users_dao');
		$this -> load -> model('Images_dao', 'img_dao');
		$this -> load -> model('Files_dao', 'file_dao');

	}

	public function index()
	{
		$data = array();
		$data = $this -> setup_user_data($data);
		$data['login_user'] = $this -> users_dao -> find_by_id($data['login_user_id']);
		$this->load->view('mgmt/lucky_draw/list', $data);
	}

	public function get_data() {
		$res = array();
		$data = $this -> get_posts(array(
			'length',
			'start',
			'columns',
			'search',
			'order',
			'lang'
		));
		$s_data = $this -> setup_user_data(array());
		$login_user = $this -> users_dao -> find_by_id($s_data['login_user_id']);
		if($login_user -> role_id != 99) {
			$data['id'] = $s_data['corp'] -> id;
		}

		$data['show_closed'] = 1;
		$res['items'] = $this -> dao -> query_ajax($data);
		$res['recordsFiltered'] = $this -> dao -> count_ajax($data);
		$res['recordsTotal'] = $this -> dao -> count_all_ajax($data);

		$this -> to_json($res);
	}

	public function edit($id) {
		$data = array();
		$data['id'] = $id;
		if(!empty($id)) {
			$q_data = $this -> get_posts(array(
				'length',
				'start',
				'columns',
				'search',
				'order'
			));
			$q_data['id'] = $id;
			$q_data['show_closed'] = 1;
			$list = $this -> dao -> query_ajax($q_data);
			$item = $list[0];

			$data['item'] = $item;

			$item -> logo_img = $this -> img_dao -> find_by_id($item -> logo_image_id);
			$item -> line_logo_img = $this -> img_dao -> find_by_id($item -> line_logo_image_id);
			$item -> bg_img = $this -> img_dao -> find_by_id($item -> bg_image_id);
			$item -> video_file = $this -> file_dao -> find_by_id($item -> video_file_id);
		}

		$s_data = $this -> setup_user_data(array());
		$data['login_user'] = $this -> users_dao -> find_by_id($s_data['login_user_id']);
		$this->load->view('mgmt/lucky_draw/edit', $data);
	}

	public function insert() {
		$res = array();
		$id = $this -> get_post('id');
		$data = $this -> get_posts(array(
			'corp_code',
			'corp_name',
			'sys_name',
			'sys_name_cht',
			'corp_url',
			'slogan',
			'video_link',
			'merchant_id',
			'intro_rate',
			'corp_rate',
			'sys_rate',
			'smart_user_rate',
			'smart_intro_rate',
			'smart_corp_rate',
			'smart_sys_rate',
			'bank_name',
			'bank_account',
			'branch_name',
			'huihepay_appid',
			'huihepay_key',
			'best_pay_id',
			'best_pay_key',
			'hash_key',
			'hash_iv',
			'price_buy',
			'price_sell',
			'price_avg',
			'daily_dig',
			'intro_dig',
			'cht_sms_account',
			'cht_sms_password',
			'chs_sms_account',
			'chs_sms_password',
			'logo_image_id',
			'line_logo_image_id',
			'bg_image_id',
			'video_file_id',
			'line',
			'line_url',
			'wechat',
			'wechat_url',
			'manager_sms',
			'disable_upgrade'
		));

		// tx
		$corp_rate = 0;
		if(!empty($data['corp_rate'])) {
			$corp_rate = floatval($data['corp_rate']);
		}
		$intro_rate = 0;
		if(!empty($data['intro_rate'])) {
			$intro_rate = floatval($data['intro_rate']);
		}
		$sys_rate = 0;
		if(!empty($data['sys_rate'])) {
			$sys_rate = floatval($data['sys_rate']);
		}
		$data['total_rate'] = $corp_rate + $intro_rate + $sys_rate;

		// smart tx
		$smart_user_rate = 0;
		if(!empty($data['smart_user_rate'])) {
			$smart_user_rate = floatval($data['smart_user_rate']);
		}
		$smart_corp_rate = 0;
		if(!empty($data['smart_corp_rate'])) {
			$smart_corp_rate = floatval($data['smart_corp_rate']);
		}
		$smart_intro_rate = 0;
		if(!empty($data['smart_intro_rate'])) {
			$smart_intro_rate = floatval($data['smart_intro_rate']);
		}
		$smart_sys_rate = 0;
		if(!empty($data['smart_sys_rate'])) {
			$smart_sys_rate = floatval($data['smart_sys_rate']);
		}
		$data['smart_total_rate'] = $smart_user_rate + $smart_corp_rate + $smart_intro_rate + $smart_sys_rate;

		$data['price_buy'] = floatval( $data['price_avg']) * 1.1;
		$data['price_sell'] = floatval( $data['price_avg']) * 0.9;

		$status = $this -> get_post('status');
		if(empty($status)) {
			$data['status'] = 0;
		} else {
			$data['status'] = $status;
		}

		if(empty($id)) {
			// insert
			$lang = $this -> get_post('lang');
			$data['lang'] = $lang;
			if($lang == 'chs') {
				// chs
				$data['currency'] = 'RMB';
			}
			if($lang == 'cht') {
				// cht
				$data['currency'] = 'NTD';
			}
			$this -> dao -> insert($data);
		} else {
			// update
			$this -> dao -> update($data, $id);
		}

		// create params
		$this -> params_dao -> find_by_corp_id($id);

		$res['success'] = TRUE;
 		$this -> to_json($res);
	}

	public function delete($id) {
		$res['success'] = TRUE;
		$this -> dao -> delete_status($id, $this -> session -> userdata('user_id'));
		$this -> to_json($res);
	}

	public function check_account($id) {
		$account = $this -> get_post('account');
		$list = $this -> dao -> find_all_by('account', $account);
		$res = array();
		if(!empty($id)) {
			if (count($list) > 0) {
				$item = $list[0];
				if($item -> id == $id) {
					$res['valid'] = TRUE;
				} else {
					$res['valid'] = FALSE;
				}

				$res['item'] = $item;
			} else {
				$res['valid'] = TRUE;
			}
		} else {
			if (count($list) > 0) {
				$res['valid'] = FALSE;
			} else {
				$res['valid'] = TRUE;
			}
		}

		$this -> to_json($res);
	}

	public function check_corp_code($id) {
		$code = $this -> get_post('corp_code');
		$list = $this -> dao -> find_all_by('corp_code', $code);
		$res = array();
		if(!empty($id)) {
			if (count($list) > 0) {
				$item = $list[0];
				if($item -> id == $id) {
					$res['valid'] = TRUE;
				} else {
					$res['valid'] = FALSE;
				}

				$res['item'] = $item;
			} else {
				$res['valid'] = TRUE;
			}
		} else {
			if (count($list) > 0) {
				$res['valid'] = FALSE;
			} else {
				$res['valid'] = TRUE;
			}
		}

		$this -> to_json($res);
	}

	public function chg_user() {
		$user_id = $this -> get_post('user_id');
		$this -> session -> set_userdata('user_id', $user_id);
		$res = array();

		$this -> to_json($res);
	}
}
