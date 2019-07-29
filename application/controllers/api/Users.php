<?php
class Users extends MY_Base_Controller {

	function __construct() {
		parent::__construct();

		// setup models
		$this -> load -> model('Post_log_dao', 'post_log_dao');

		$this -> load -> model('Wallet_tx_dao', 'wtx_dao');
		$this -> load -> model('Wallet_tx_bdc_dao', 'wtx_bdc_dao');
		$this -> load -> model('Wallet_tx_bkc_dao', 'wtx_bkc_dao');

		$this -> load -> model('Bonus_tx_dao', 'tx_dao');
		$this -> load -> model('Users_dao', 'u_dao');
		$this -> load -> model('Baccarat_tab_round_bet_dao', 'btrb_dao');

		$this -> load -> model('Com_tx_dao', 'ctx_dao');

		$this -> load -> model('Game_session_dao', 'gs_dao');

		$this -> load -> model('Chat_msg_ad_dao', 'chat_msg_ad_dao');

	}

	function reset_black_coin() {
		$this -> wtx_bkc_dao -> reset_all(200000);
	}

	function get_sum_amt() {
		$data = array();
		$payload = $this -> get_payload();
		$user = $this -> users_dao -> find_by_id($payload['user_id']);
		if(!empty($user)) {
			$samt = $this -> wtx_dao -> get_sum_amt($user -> id);
			$data['sum_amt'] = number_format((float)$samt, 2, '.', '');
		} else {
			$data['error_msg'] = "查無使用者";
		}

		$this -> to_json($data);
	}

	function get_sum_bdc_amt($user_id) {
		$data = array();
		$data['sum_amt'] = $this -> wtx_dao -> get_sum_amt($user_id);
		$this -> to_json($data);
	}

	function get_sum_bkc_amt($user_id) {
		$data = array();
		$data['success'] = TRUE;

		$tx = array();
		$tx['user_id'] = $user_id;
		$tx['amt'] = 10000;
		$this -> wtx_bkc_dao -> insert($tx);

		// sum amt
		$data['sum_amt'] = $this -> wtx_bkc_dao -> get_sum_amt($user_id);
		$this -> to_json($data);
	}

	function upload_base64() {
		$res = array();
		$params = array();
		// $params['image_base64'] = $this -> get_post('image_base64');
		// $params['user_id'] = $this -> get_post('user_id');
		$n_res = $this -> curl -> simple_post("http://ckstar99.com/mgmt/images/upload/avatar", $params);
		echo "nres: " . $n_res;
		// $this -> to_json(json_decode($n_res));
	}

	function update_user_info() {
		$res = array('success' => TRUE);

		$payload = $this -> get_payload();
		$user_id = $payload['user_id'];

		$user_name = $this -> get_post('user_name');
		$contact_phone = $this -> get_post('contact_phone');
		$uid = $this -> get_post('uid');
		$birthday = $this -> get_post('birthday');
		$zip = $this -> get_post('zip');
		$address = $this -> get_post('address');
		$email = $this -> get_post('email');

		if(empty($user_id) || empty($user_name) || empty($contact_phone) || empty($uid) || empty($birthday) || empty($address)) {
			$res['error_msg'] = "缺少必填欄位";
		} else {
			$user = $this -> users_dao -> find_by_id($user_id);
			if(empty($user)) {
				$res['error_msg'] = "查無使用者";
			} else {
				$this -> users_dao -> update(array(
					'user_name' => $user_name,
					'contact_phone' => $contact_phone,
					'uid' => $uid,
					'birthday' => $birthday,
					'zip' => $zip,
					'address' => $address,
					'email' => $email,
				), $user_id);
			}
		}


		$this -> to_json($res);
	}

	function mark_lv_is_read() {
		$res = array('success' => TRUE);
		$user_id = $this -> get_post('user_id');
		$lv_is_read = $this -> get_post('lv_is_read');

		if(empty($user_id)) {
			$res['error_msg'] = "缺少必填欄位";
		} else {
			$user = $this -> users_dao -> find_by_id($user_id);
			if(empty($user)) {
				$res['error_msg'] = "查無使用者";
			} else {
				$u_data = array();
				$u_data['lv_is_read'] = $lv_is_read;

				if(count($u_data) > 0) {
					$this -> users_dao -> update($u_data, $user_id);
				} else {
					$res['error_msg'] = "無資料可更新";
				}
			}
		}


		$this -> to_json($res);
	}

	function update_user_msg() {
		$res = array('success' => TRUE);
		$user_id = $this -> get_post('user_id');
		$user_msg = $this -> get_post('user_msg');

		if(empty($user_id)) {
			$res['error_msg'] = "缺少必填欄位";
		} else {
			$user = $this -> users_dao -> find_by_id($user_id);
			if(empty($user)) {
				$res['error_msg'] = "查無使用者";
			} else {
				$u_data = array();
				$u_data['user_msg'] = $user_msg;

				if(count($u_data) > 0) {
					$this -> users_dao -> update($u_data, $user_id);
				} else {
					$res['error_msg'] = "無資料可更新";
				}
			}
		}


		$this -> to_json($res);
	}

	function get_update_user_bd() {
		$res = array('success' => TRUE);
		$user_id = $this -> get_post('user_id');

		if(empty($user_id)) {
			$res['error_msg'] = "缺少必填欄位";
		} else {
			$user = $this -> users_dao -> find_by_id($user_id);
			if(empty($user)) {
				$res['error_msg'] = "查無使用者";
			} else {
				if($user -> is_update == 1) {
					if($user -> is_update_paid == 0) {
						$tx = array();
						$tx['corp_id'] = $user -> corp_id; // corp id
						$tx['user_id'] = $user -> id;

						$amt = 100;
						$tx['amt'] = $amt;
						$tx['type_id'] = 141;
						$tx['brief'] = "會員 $user->account 認證個人資料獎勵藍鑽 $amt ";
						$this -> wtx_bdc_dao -> insert($tx);

						$this -> users_dao -> update(array(
							'is_update_paid' => 1
						), $user_id);
					} else {
						$res['error_msg'] = "已領取過了";
					}
				} else {
					$res['error_msg'] = "尚未更新個人資料";
				}
			}
		}

		$this -> to_json($res);
	}

	function info() {
		$data = array();

		$payload = $this -> get_payload();

		$user = $this -> users_dao -> find_by_id($payload['user_id']);

		$data['sum_amt'] = $this -> wtx_dao -> get_sum_amt($user -> id);
		$data['create_time'] = $user -> create_time;
		$data['last_login_time'] = $user -> last_login_time;
		$data['line_picture'] = $user -> line_picture;
		$data['nick_name'] = $user -> nick_name;
		$data['gift_id'] = $user -> gift_id;

		$this -> to_json($data);
	}

	function info_all() {
		$data = array();

		$payload = $this -> get_payload();

		$user = $this -> users_dao -> find_by_id($payload['user_id']);

		$data['sum_amt'] = $this -> wtx_dao -> get_sum_amt($user -> id);
		$data['create_time'] = $user -> create_time;
		$data['last_login_time'] = $user -> last_login_time;
		$data['line_picture'] = $user -> line_picture;
		$data['nick_name'] = $user -> nick_name;
		$data['gift_id'] = $user -> gift_id;

		$data['user_name'] = $user -> user_name;
		$data['contact_phone'] = $user -> contact_phone;
		$data['mobile'] = $user -> mobile;
		$data['is_valid_mobile'] = $user -> is_valid_mobile;
		$data['email'] = $user -> email;
		$data['uid'] = $user -> uid;
		$data['birthday'] = $user -> birthday;
		$data['zip'] = $user -> zip;
		$data['address'] = $user -> address;
		$data['lv'] = $user -> lv;
		$data['accu_lv'] = $user -> accu_lv;
		$data['thresh_lv'] = 10000000;

		$this -> to_json($data);
	}

	function add_money() {
		$res = array('success' => TRUE);

		$payload = $this -> get_payload();
		$user_id = $payload['user_id'];
		$user = $this -> users_dao -> find_by_id($user_id);

		$amt = $this -> get_post("amt");
		if(!empty($user)) {
			$tx = array();
			$tx['corp_id'] = $user -> corp_id; // corp id
			$tx['user_id'] = $user -> id;

			// $amt = 100;
			$tx['amt'] = $amt;
			$tx['tx_type'] = "sys_api"; //sys api
			$tx['brief'] = "系統api儲值 $amt";
			$last_id = $this -> wtx_dao -> insert($tx);
		} else {
			$res['error_msg'] = "查無使用者";
		}

		$this -> to_json($res);
	}

	function update_nick_name() {
		$res = array('success' => TRUE);
		$nick_name = $this -> get_post('nick_name');

		$payload = $this -> get_payload();
		$user_id = $payload['user_id'];

		$user = $this -> users_dao -> find_by('nick_name', $nick_name);
		$me = $this -> users_dao -> find_by_id($user_id);
		if(!empty($me))  {
			if(empty($user)) {
				// update
				if($me -> is_nick_name_changed == 1) {
					$res['error_msg'] = "僅能更新一次";
				} else {
					$this -> users_dao -> update(array(
						'nick_name' => $nick_name,
					), $user_id);
				}

			} else {

				if($user -> id == $user_id) {
				} else {
					if($user -> corp_id == $me -> corp_id) {
						$res['error_msg'] = "此暱稱已被使用";
					} else {
						// update
							$res['hello'] = 'hellol';
						if($me -> is_nick_name_changed == 1) {
							$res['error_msg'] = "僅能更新一次";
						} else {
							$this -> users_dao -> update(array(
								'nick_name' => $nick_name,
								'is_nick_name_changed' => 1
							), $user_id);
						}

					}
				}
			}
		} else {
			$res['error_msg'] = "查無使用者";
		}

		$this -> to_json($res);
	}

	public function donate() {
		$res = array("success" => TRUE);
		$user_id = $this -> get_post("user_id");
		$amt = $this -> get_post("amt");
		$user = $this -> users_dao -> find_by_id($user_id);
		if(!empty($user)) {
			$samt = $this -> wtx_dao -> get_sum_amt($user_id);
			if(intval($samt) < intval($amt)) {
				$res['error_msg'] = "餘額不足";
			} elseif(intval($amt) < 1000) {
				$res['error_msg'] = "至少捐1000";
			} else {
				$tx = array();
				$tx['corp_id'] = $user -> corp_id; // corp id
				$tx['user_id'] = $user -> id;

				// $amt = 100;
				$tx['amt'] = -$amt;
				$tx['type_id'] = 900;
				$tx['brief'] = "捐款 $amt";
				$last_id = $this -> wtx_dao -> insert($tx);

				$tx = array();
				$tx['corp_id'] = $user -> corp_id;
				$tx['amt'] = $amt;
				$tx['income_type'] = "捐款";
				$tx['income_id'] = $last_id;
				$tx['note'] = "客戶捐款 {$user->id} {$amt}";
				$this -> ctx_dao -> insert($tx);
			}
		} else {
			$res['error_msg'] = "查無使用者";
		}
		$this -> to_json($res);
	}

	private function do_log($tag = '') {
		$i_data['post'] =json_encode($_POST, JSON_UNESCAPED_UNICODE);
		$actual_link = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
		$i_data['tag'] = $tag;
		$i_data['full_path'] = $actual_link;
		$this -> post_log_dao -> insert($i_data);
	}

	public function list_fortune() {
		$res = array();
		$page = $this -> get_post('page');

		$res['page'] = $page;

		$list = $this -> u_dao -> find_by_fortune(array('page' => $page));
		$res['list'] = $list;
		$this -> to_json($res);
	}

	public function update_wtx() {
		$users = $this -> users_dao -> find_all();
		foreach($users as $user) {
			$samt = $this -> wtx_dao -> get_sum_amt($user -> id);
			$this -> users_dao -> update(array(
				"sum_amt" => $samt
			), $user -> id);
		}
		echo "end..";
	}

}
?>
