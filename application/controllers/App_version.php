<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class App_version extends MY_Base_Controller {

	function __construct() {
		parent::__construct();

		$this->load->helper('captcha');

		$this -> load -> model('Users_dao', 'users_dao');
		$this -> load -> model('Banks_dao', 'banks_dao');
		$this -> load -> model('Corp_dao', 'corp_dao');
		$this -> load -> model('App_version_dao', 'app_version_dao');

		include APPPATH . 'third_party/smsapi.class.php';
	}

	public function mobile_valid() {
		$data = array();
		$user_id = $this -> session -> userdata('user_id');
		$data['login_user_id'] = $user_id;
		$data['login_user'] = $this -> users_dao -> find_by_id($user_id);

		$corp = $this -> corp_dao -> find_by_id($data['login_user'] -> corp_id);
		$data['corp'] = $corp;
		$this -> load -> view('login_mobile', $data);
	}

	public function index() {
		$data = array();
		$item = $this -> app_version_dao -> find_by_id(1);
		$item_ios = $this -> app_version_dao -> find_by_id(2);
		$item_ios_switch = $this -> app_version_dao -> find_by_id(3);

		$item -> ios_version = $item_ios -> version;
		$item -> ios_switch = $item_ios_switch -> version;
		$data['item'] = $item;
		$data = $this -> get_captcha($data);
		$this -> load -> view('app_version', $data);
	}

	public function cov($corp_code) {
		$data = array();
		$corp = $this -> corp_dao -> find_by_corp_code($corp_code);
		if(empty($corp)) {
			redirect("/login");
			return;
		}

		if($corp -> status == 2) {
			redirect("/nosession/close");
			return;
		}


		if($corp -> status > 0) {
			redirect("/login");
			return;
		}

		// check login
		if(!empty($this -> session -> userdata('user_id'))) {
			redirect("/app/#mgmt/dashboard");
			return;
		}

		// update session
		$this -> session -> set_userdata('corp', $corp);
		$data['corp'] = $corp;

		$account = $this -> get_post('account');
		$password = $this -> get_post('password');
		if (!empty($account) && !empty($password)) {
			$user = $this -> users_dao -> find_by_corp_and_account($corp -> id, $account);
			if (!empty($user) && $user -> password == $password) {
				$this -> session -> set_userdata('user_id', $user -> id);
				$menu_list = $this -> users_dao -> nav_list_by_role_id($user -> role_id);
				foreach($menu_list as $each) {
					if($each -> base_path == "mgmt/dashboard") {
						redirect("/app/#mgmt/dashboard");
					}
				}
				redirect("/app/#mgmt/welcome");
			}

			$data['msg'] = "Wrong account or password";
		}

		$data = $this -> get_captcha($data);
		$this -> load -> view('loginv', $data);
	}

	public function do_change() {
		$res = array();

		$version = $this -> get_post('version');
		$ios_version = $this -> get_post('ios_version');
		$ios_switch = $this -> get_post('ios_switch');
		$captcha = $this -> get_post('captcha');
		if (!empty($version) && !empty($captcha)) {
			$captcha_word = $this -> session -> userdata('captcha_word');
			if($captcha == $captcha_word) {
				$this -> app_version_dao -> update(array(
					'version' => $version
				), 1);
				$this -> app_version_dao -> update(array(
					'version' => $ios_version
				), 2);
				$this -> app_version_dao -> update(array(
					'version' => $ios_switch
				), 3);
			} else {
				$res['msg'] = "驗證碼錯誤";
			}
		} else {
			$res['msg'] = "請輸入必填資料";
		}

		$this -> to_json($res);
	}

	function get_captcha($data) {
		// numeric random number for captcha
		$random_number = substr(number_format(time() * rand(),0,'',''),0,4);
		// setting up captcha config
		//echo __DIR__;

		$vals = array(
			 'word' => $random_number,
			 'img_path' => './img/captcha/',
			 'img_url' => base_url().'img/captcha/',
			 'img_width' => 140,
			 'img_height' => 32,
			 'expiration' => 7200,
			 'colors'        => array(
                'background' => array(255, 255, 255),
                'border' => array(255, 255, 255),
                'text' => array(0, 0, 0),
                'grid' => array(255, 40, 40)
      		  )
			);

		$data['captcha'] = create_captcha($vals);
		$this->session->set_userdata('captcha_word',$data['captcha']['word']);

		return $data;
	}

	public function refresh_captcha() {
		$data = $this -> get_captcha(array());
		$this -> to_json($data);
	}

	public function refresh_lang() {
		$data = array();
		$lang = $this -> get_post('lang');
		if(!empty($lang)) {
			$this -> session -> set_userdata('lang', $lang);
		} else {
			$lang = $this -> session -> userdata('lang');
			if(empty($lang)) {
				$lang = 'cht';
				$this -> session -> set_userdata('lang', $lang);
			}
		}

		$data['success'] = TRUE;
		$this -> to_json($data);
	}

	public function register() {
		// check login
		if(!empty($this -> session -> userdata('user_id'))) {
			redirect("/app/#mgmt/dashboard");
			return;
		}
		redirect('nosession');
		// $data = array();
		//
		// $corp = $this -> corp_dao -> find_default_corp();
		// // update session
		// $this -> session -> set_userdata('corp', $corp);
		// $data['corp'] = $corp;
		//
		// $code = $this -> get_get('code');
		// $list = $this -> users_dao -> find_all_by('code', $code);
		// if(count($list) > 0) {
		// 	$data['code'] = $code;
		// }
		// $data['is_reg'] = TRUE;
		// $data['bank_list'] = $this -> banks_dao -> find_my_all();
		//
		// $this -> load -> view('login', $data);
	}

	public function co_forgot($corp_code) {
		$data = array();

		$corp = $this -> corp_dao -> find_by_corp_code($corp_code);
		if(empty($corp)) {
			redirect("/register");
			return;
		}

		// check login
		if(!empty($this -> session -> userdata('user_id'))) {
			redirect("/app/#mgmt/dashboard");
			return;
		}

		// update session
		$this -> session -> set_userdata('corp', $corp);
		$data['corp'] = $corp;

		$code = $this -> get_get('code');
		$list = $this -> users_dao -> find_all_by('code', $code);
		if(count($list) > 0) {
			$data['code'] = $code;
		}
		$data['is_forgot'] = TRUE;
		$data = $this -> get_captcha($data);
		$this -> load -> view('loginv', $data);
	}



	public function do_forgot() {
		$res = array();
		$corp_id = $this -> get_post('corp_id');
		$corp = $this -> corp_dao -> find_by_id($corp_id);

		// update session
		$this -> session -> set_userdata('corp', $corp);

		$account = $this -> get_post('account');
		$captcha = $this -> get_post('captcha');
		if (!empty($account) && !empty($captcha)) {
			$captcha_word = $this -> session -> userdata('captcha_word');
			if($captcha == $captcha_word) {
				$user = $this -> users_dao -> find_by_corp_and_account($corp -> id, $account);
				if (!empty($user)) {
					// send sms
					if(!empty($user -> mobile)) {
						$mobile = $user -> mobile;

						if($user -> lang == 'chs') {
							// chs
							if(!empty($corp -> chs_sms_account)) {
								$msg="帐号 $user->account 您的密码为 $user->password";
								$api = new SmsApi($corp -> chs_sms_account, $corp -> chs_sms_password);
								$api -> sendAll($mobile, $msg);
							}

						} else {
							// default cht
							// $msg=iconv("UTF-8","big5","帳號 $user->account 您的密碼為 $user->password");
							// if(!empty($corp -> cht_sms_account)) {
							// 	$m_acc = $corp -> cht_sms_account;
							// 	$m_pwd = $corp -> cht_sms_password;
							// 	$n_res = $this -> curl -> simple_get("http://smexpress.mitake.com.tw/SmSendGet.asp"
							// 		. "?username=$m_acc&password=$m_pwd&dstaddr=$mobile&DestName=SteveYeh&dlvtime=&vldtime=&smbody=$msg");
							// }
							if(!empty($corp -> chs_sms_account)) {
								$msg="帐号 $user->account 您的密码为 $user->password";
								$api = new SmsApi($corp -> chs_sms_account, $corp -> chs_sms_password);
								$api -> sendAll($mobile, $msg);
							}
						}
					} else {
						$res['msg'] = "尚未設定手機號碼";
					}
				} else {
					$res['msg'] = "帳號錯誤";
				}
			} else {
				$res['msg'] = "驗證碼錯誤";
			}
		} else {
			$res['msg'] = "請輸入必填資料";
		}

		$this -> to_json($res);
	}

	public function co_register($corp_code) {
		$data = array();

		$corp = $this -> corp_dao -> find_by_corp_code($corp_code);
		if(empty($corp)) {
			redirect("/register");
			return;
		}

		// check login
		if(!empty($this -> session -> userdata('user_id'))) {
			redirect("/app/#mgmt/dashboard");
			return;
		}

		// update session
		$this -> session -> set_userdata('corp', $corp);
		$data['corp'] = $corp;

		$code = $this -> get_get('code');
		$list = $this -> users_dao -> find_all_by('code', $code);
		if(count($list) > 0) {
			$data['code'] = $code;
		}
		$data['is_reg'] = TRUE;

		$lang = $this -> session -> userdata('lang');
		$country = 0;
		if($lang == 'chs') {
			$country = 1;
		}
		$data['bank_list'] = $this -> banks_dao -> find_all_by_country($country);
		$this -> load -> view('loginv', $data);
	}

	public function do_reg() {
		$res = array();
		$res['success'] = TRUE;

		$i_data['corp_id'] = $this -> get_post('corp_id');
		$i_data['account'] = $this -> get_post('account');
		$i_data['user_name'] = $this -> get_post('user_name');
		$i_data['password'] = $this -> get_post('password');
		$i_data['email'] = $this -> get_post('email');
		$i_data['line_id'] = $this -> get_post('line_id');
		$i_data['wechat_id'] = $this -> get_post('wechat_id');
		$i_data['bank_id'] = $this -> get_post('bank_id');
		$i_data['bank_account'] = $this -> get_post('bank_account');
		$i_data['lang'] = $this -> get_post('lang');
		$intro_code = $this -> get_post('intro_code');
		$i_data['role_id'] = 3; // general

		$o_user = $this -> users_dao -> find_by_corp_and_account($i_data['corp_id'], $i_data['account']);
		if(!empty($o_user)) {
			$res['last_id'] = 0;
		} else {
			if(!empty($intro_code)) {
				$intro_user = $this -> users_dao -> find_by('code', $intro_code);
				if(!empty($intro_user)) {
					if($intro_user -> role_id == 11 || $intro_user -> role_id == 1) { // 公司管理人或股東
						$i_data['shareholder_code'] = $intro_code;
						$i_data['shareholder_id'] = $intro_user -> id;

						// also manager
						$i_data['manager_code'] = $intro_code;
						$i_data['manager_id'] = $intro_user -> id;
					} else if($intro_user -> role_id == 2) { // 經理人
						$i_data['shareholder_code'] = $intro_user -> shareholder_code;
						$i_data['shareholder_id'] = $intro_user -> shareholder_id;

						$i_data['manager_code'] = $intro_code;
						$i_data['manager_id'] = $intro_user -> id;
					} else { // 會員
						$i_data['shareholder_code'] = $intro_user -> shareholder_code;
						$i_data['shareholder_id'] = $intro_user -> shareholder_id;

						$i_data['manager_code'] = $intro_user -> manager_code;
						$i_data['manager_id'] = $intro_user -> manager_id;
					}

					$i_data['intro_code'] = $intro_code;
					$i_data['intro_id'] = $intro_user -> id;
				}
			}

			// get code
			$find_code = FALSE;
			while(!$find_code) {
				$code = generate_random_string();
				$c_list = $this -> users_dao -> find_all_by('code', $code);
				$find_code = (count($c_list) == 0);
				$i_data['code'] = $code;
			}

			$last_id = $this -> users_dao -> insert($i_data);
			$res['last_id'] = $last_id;

			$corp = $this -> corp_dao -> find_by_id($i_data['corp_id']);
			$res['corp_code'] = $corp -> corp_code;
		}
		$this -> to_json($res);
	}

	public function check_account() {
		$account = $this -> get_get('account');
		$list = $this -> users_dao -> find_all_by('account', $account);

		$corp = $this -> session -> userdata('corp');
		$contain = FALSE;
		foreach($list as $each) {
			if($each -> corp_id == $corp -> id) {
				$contain = TRUE;
			}
		}
		echo ($contain ? 'false' : 'true');
	}

	public function check_code() {
		$code = $this -> get_get('intro_code');
		$list = $this -> users_dao -> find_all_by('code', $code);
		echo (count($list) > 0 ? 'true' : 'false');
	}

	public function logout() {
		$corp = $this -> session -> userdata('corp');
		$this -> session -> sess_destroy();

		$this -> session -> set_userdata('corp', $corp);
		redirect($corp -> corp_code . '/login');
	}

	function update_mobile_and_get_reg_code() {
		$res = array();
		$member_id = $this -> input -> get('member_id', TRUE);
		$mobile = $this -> input -> get('mobile', TRUE);

		if(!empty($member_id) && !empty($mobile)) {
			$member = $this -> members_dao -> find_by('mobile', $mobile);
			if(!empty($member)) {
				// alerady set
				if($member -> id == $member_id) {
					// it's me
					$this -> get_mobile_reg_code();
					return;
				} else {
					if($member -> is_valid_mobile == 1) {
						$res['error'] = TRUE;
						$res['error_msg'] = "已被他人認證過了";
					} else {
						// not used by others
						$this -> get_mobile_reg_code();
						return;
					}
				}
			} else {
				// to be my phone
				//$this -> members_dao -> update(array('mobile' => $mobile), $member_id);
				$this -> get_mobile_reg_code();
				return;
			}
		} else {
			$res['error'] = TRUE;
			$res['error_msg'] = "資料有問題";
		}

		$res['success'] = TRUE;
		$this -> to_json($res);
	}

	// mobile verify
	function get_mobile_reg_code() {
		$res = array();
		$user_id = $this -> get_post('user_id');
		$mobile = $this -> get_post('mobile');
		if(!empty($user_id) && !empty($mobile)) {
			$user = $this -> users_dao -> find_by_id($user_id);
			if(!empty($user)) {
				$reg_code = $user -> reg_code;
				$corp = $this -> corp_dao -> find_by_id($user -> corp_id);

				if(empty($reg_code) || trim($mobile) != trim($user->mobile)
					|| (!empty($user -> reg_code_create_time) && $this -> is_out_of_time($user -> reg_code_create_time))) {
					$reg_code = $this -> get_reg_code();
					$u_data['mobile'] = trim($mobile);
					$u_data['reg_code'] = $reg_code;
					$reg_code_create_time = date('Y-m-d H:i:s');
					$u_data['reg_code_create_time'] = $reg_code_create_time;
					$u_data['is_valid_mobile'] = 0;
					$this -> users_dao -> update($u_data, $user_id);

					$res['reg_code_create_time'] = $reg_code_create_time;
				} else {
					$res['reg_code_create_time'] = $user -> reg_code_create_time;
				}

				if($user -> lang == 'chs') {
					$msg = "简讯认证码为 $reg_code ，此认证码将于30分钟后失效。";

					if(!empty($corp -> chs_sms_account)) {
						$api = new SmsApi($corp -> chs_sms_account, $corp -> chs_sms_password);
						$api -> sendAll($mobile, $msg);
					}

				} else {
					// default cht
					// $msg=iconv("UTF-8","big5","簡訊認證碼為 $reg_code ，此認證碼將於30分鐘後失效。");
					// if(!empty($corp -> cht_sms_account)) {
					// 	$m_acc = $corp -> cht_sms_account;
					// 	$m_pwd = $corp -> cht_sms_password;
					// 	$n_res = $this -> curl -> simple_get("http://smexpress.mitake.com.tw/SmSendGet.asp"
					// 		. "?username=$m_acc&password=$m_pwd&dstaddr=$mobile&DestName=SteveYeh&dlvtime=&vldtime=&smbody=$msg");
					// }
					$msg = "简讯认证码为 $reg_code ，此认证码将于30分钟后失效。";
					if(!empty($corp -> chs_sms_account)) {
						$api = new SmsApi($corp -> chs_sms_account, $corp -> chs_sms_password);
						$api -> sendAll($mobile, $msg);
					}
				}
			}


		} else {
			$res['msg'] = '缺少欄位';
		}

		$res['success'] = TRUE;
		$this -> to_json($res);
	}

	// mobile reg code verify
	function check_mobile_reg_code() {
		$res = array();
		$user_id = $this -> get_post('user_id');
		$reg_code = $this -> get_post('reg_code');

		$is_valid = 0;
		if(!empty($user_id) && !empty($reg_code)) {
			$user = $this -> users_dao -> find_by_id($user_id);
			if(!empty($user) && $reg_code == $user -> reg_code) {
				$res['msg'] = 'right';
				if(!empty($user -> reg_code_create_time) && !$this -> is_out_of_time($user -> reg_code_create_time)) {
					$is_valid = 1;
					$u_data['is_valid_mobile'] = 1;
					$this -> users_dao -> update($u_data, $user_id);
				}
			} else {
				$res['msg'] = 'wrong';
			}

		} else {
			$res['msg'] = 'no user';
		}
 		$res['is_valid'] = $is_valid;
		$res['success'] =TRUE;
		$this -> to_json($res);
	}

	private function is_out_of_time($dt) {
		// 30 min
		$min = 30;
		$diff = (strtotime(date('Y-m-d H:i:s')) - strtotime($dt)) / 60;
		if($diff <= $min) {
			return FALSE;
		}
		return TRUE;
	}

	private function get_reg_code() {
		$digits = 4;
		return rand(pow(10, $digits - 1), pow(10, $digits) - 1);
	}

}
