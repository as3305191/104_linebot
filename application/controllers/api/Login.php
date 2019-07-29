<?php
use AlibabaCloud\Client\AlibabaCloud;
use AlibabaCloud\Client\Exception\ClientException;
use AlibabaCloud\Client\Exception\ServerException;

class Login extends MY_Base_Controller {

	function __construct() {
		parent::__construct();
		$this -> load -> model('Corp_dao', 'corp_dao');
		$this -> load -> model('Users_dao', 'users_dao');
		$this -> load -> model('Wallet_tx_bdc_dao', 'wtx_bdc_dao');
		$this -> load -> model('Post_log_dao', 'post_log_dao');
		$this -> load -> model('Wallet_tx_dao', 'wtx_dao');
		$this -> load -> model('Com_tx_dao', 'ctx_dao');
		$this -> load -> model('User_log_dao', 'ul_dao');

	}

	public function list_corps() {
		$res = array();
		$c_list = $this -> corp_dao -> find_active_all();
		$list = array();
		foreach($c_list as $each) {
			$obj = array();
			$obj['corp_id'] = $each -> id;
			$obj['corp_name'] = $each -> corp_name;
			$obj['sys_name_cht'] = $each -> sys_name_cht;
			$obj['is_bd_on'] = $each -> is_bd_on;
			$list[] = $obj;
		}
		$res['list'] = $list;
		$this -> to_json($res);
	}

	public function do_reg() {
		$res = array('success' => TRUE);
		$corp_id = $this -> get_post('corp_id');
		$account = $this -> get_post('account');
		$password = $this -> get_post('password');
		$mobile = $this -> get_post('mobile');
		$intro_code = $this -> get_post('intro_code');

		if(empty($corp_id) || empty($account) || empty($password)|| empty($mobile)) {
			$res['error_msg'] = '缺少必要參數';
		} else {
			$user = $this -> users_dao -> find_by_account_and_corp($corp_id, $account);
			if(!empty($user)) {
				$res['error_msg'] = '帳號已存在';
			} else {

				$valid_mobile_users = $this -> users_dao -> find_all_valid_by_mobile_and_corp($corp_id, $mobile);
				if(!empty($valid_mobile_users)) {
					$res['error_msg'] = '此手機號碼已被認證';
				} else {
					$i_data = array();
					$i_data['corp_id'] = $corp_id;
					$i_data['account'] = $account;
					$i_data['password'] = $password;

					// intro
					if(!empty($intro_code)) {
						$intro_user = $this -> users_dao -> find_by("code", $intro_code);
						if(!empty($intro_user)) {
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

					// set dbc wallet code
					$i_data['wallet_code_dbc'] = coin_token(34);
					$i_data['wallet_code_ntd'] = coin_token(35);
					$i_data['wallet_code_bdc'] = coin_token(36);
					$i_data['wallet_code_btc'] = '3AkzfW99twBhjZtb8sSXFWNEDTpoEYLmuQ';
					$i_data['wallet_code_eth'] = '0x08f42bf6f720f0de21df0af68d488cda14c72564';

					$last_id = $this -> users_dao -> insert($i_data);
					$res['last_id'] = $last_id;
					$res['user_id'] = $last_id;

					$this -> users_dao -> update(array(
						'nick_name' => 'user' . $last_id
					), $last_id);

					$user = $this -> users_dao -> find_by_id($last_id);

					$code = get_random_digits(4);

					$lang = 'cht';
					if(mb_strlen($mobile) > 10) {
						// 大陸手機
						$lang = 'chs';
					}

					$this -> users_dao -> update(array(
						'reg_code' => $code,
						'mobile' => $mobile,
						'lang' => $lang,
					), $user -> id);

					$corp = $this -> corp_dao -> find_by_id($user -> corp_id);

					if($lang == 'cht') {
						$msg=iconv("UTF-8","big5","簡訊認證碼為 $code ，此認證碼將於30分鐘後失效。");
						if(!empty($corp -> cht_sms_account)) {
							$m_acc = $corp -> cht_sms_account;
							$m_pwd = $corp -> cht_sms_password;
							$n_res = $this -> curl -> simple_get("http://smexpress.mitake.com.tw:9600/SmSendGet.asp"
								. "?username=$m_acc&password=$m_pwd&dstaddr=$mobile&DestName=SteveYeh&dlvtime=&vldtime=&smbody=$msg");
						}
					} else {
						// 寄送大陸簡訊
						$this -> ali_sms($mobile, $code);
					}

					$res['code'] = $code;
				}
			}
		}

		$this -> to_json($res);
	}

	public function do_forget() {
		$res = array('success' => TRUE);
		$corp_id = $this -> get_post('corp_id');
		$mobile = $this -> get_post('mobile');

		if(empty($corp_id) || empty($mobile)) {
			$res['error_msg'] = '缺少必要參數';
		} else {
			$mobile_user_list = $this -> users_dao -> find_all_valid_by_mobile_and_corp($corp_id, $mobile);
			if(count($mobile_user_list) > 0) {
				$user = $mobile_user_list[0];
				$code = get_random_digits(4);
				if($user -> mobile == $mobile) {
					$this -> users_dao -> update(array(
						'reg_code' => $code
					), $user -> id);
					$res['code'] = $code;
					$res['account'] = $user -> account;

					$mobile = $user -> mobile;
					$corp = $this -> corp_dao -> find_by_id($corp_id);

					if($user -> lang == 'cht') {
						$msg=iconv("UTF-8","big5","簡訊認證碼為 $code ，此認證碼將於30分鐘後失效。");
						if(!empty($corp -> cht_sms_account)) {
							$m_acc = $corp -> cht_sms_account;
							$m_pwd = $corp -> cht_sms_password;
							$n_res = $this -> curl -> simple_get("http://smexpress.mitake.com.tw:9600/SmSendGet.asp"
								. "?username=$m_acc&password=$m_pwd&dstaddr=$mobile&DestName=SteveYeh&dlvtime=&vldtime=&smbody=$msg");
						}
					} else {
						// 寄送大陸簡訊
						$this -> ali_sms($mobile, $code);
					}

					// $res['nres'] = urlencode($n_res);

				} else {
					$res['error_msg'] = '手機號碼錯誤';
				}
			} else {
				$res['error_msg'] = '查無帳號';
			}
		}

		$this -> to_json($res);
	}

	public function do_forget_verify() {
		$res = array('success' => TRUE);
		$corp_id = $this -> get_post('corp_id');
		$account = $this -> get_post('account');
		$code = $this -> get_post('code');

		if(empty($corp_id) || empty($account) || empty($code)) {
			$res['error_msg'] = '缺少必要參數';
		} else {
			$user = $this -> users_dao -> find_by_account_and_corp($corp_id, $account);
			if(!empty($user)) {
				if($user -> reg_code == $code) {
					$res['user_id'] = $user -> id;
				} else {
					$res['error_msg'] = '認證碼錯誤';
				}
			} else {
				$res['error_msg'] = '查無此帳號';
			}
		}

		$this -> to_json($res);
	}

	public function new_pass() {
		$res = array('success' => TRUE);
		$user_id = $this -> get_post('user_id');
		$password = $this -> get_post('password');

		if(empty($user_id) || empty($password)) {
			$res['error_msg'] = '缺少必要參數';
		} else {
			$user = $this -> users_dao -> find_by_id($user_id);
			if(!empty($user)) {
				$this -> users_dao -> update(array(
					'password' => $password
				), $user -> id);
			} else {
				$res['error_msg'] = '查無此帳號';
			}
		}

		$this -> to_json($res);
	}

	public function line_login() {
		$res = array('success' => TRUE);
		$corp_id = $this -> get_post('corp_id');
		$line_sub = $this -> get_post('line_sub');

		$line_name = $this -> get_post('line_name');
		$line_picture = $this -> get_post('line_picture');

		if(empty($corp_id) || empty($line_sub) || strlen($line_sub) < 10) {
			$res['error_msg'] = '缺少必要參數';
		} else {
			$user = $this -> users_dao -> find_by_line_sub_and_corp($corp_id, $line_sub);
			if(!empty($user)) {
				$res['is_valid_mobile'] = $user -> is_valid_mobile;

				// bd tokens
				$bd_token = md5(uniqid(rand(), true));

				// jwt token
				$payload = array();
				$payload['corp_id'] = $user -> corp_id;
				$payload['user_id'] = $user -> id;
				$payload['user_name'] = $user -> user_name;
				$payload['nick_name'] = $user -> nick_name;
				$payload['user_account'] = $user -> account;

				// update token
				$token = md5(uniqid(rand(), true));
				$payload['token'] = $token;

				$token = jwt_encode($payload, "jihad");
				// update last login & token
				$this -> users_dao -> update(array(
					'last_login_time' => date('Y-m-d H:i:s'),
					'bd_token' => $bd_token,
					'auth_token' => $token
				), $user -> id);
				$res['bd_token'] = $bd_token;
				$res['auth_token'] = $token;
				$i_data["user_id"] =$payload['user_id'];
				$i_data["ip"] = get_ip();
				$i_data["log_type"] = "web";
				$this-> ul_dao ->insert($i_data);
			} else {
				$res['error_msg'] = "查無USER";
			}
		}

		$this -> to_json($res);
	}


	public function line_login_4app() {
		$this -> do_log("line_login_4app");

		$res = array('success' => TRUE);
		$corp_id = $this -> get_post('corp_id');
		$line_sub = $this -> get_post('line_sub');

		$line_name = $this -> get_post('line_name');
		$line_picture = $this -> get_post('line_picture');

		if(empty($corp_id) || empty($line_sub) || strlen($line_sub) < 10) {
			$res['error_msg'] = '缺少必要參數';
		} else {
			$user = $this -> users_dao -> find_by_line_sub_and_corp($corp_id, $line_sub);
			if(!empty($user)) {
				// update
				// update line_name and picture
				if(!empty($line_name)) {
					$this -> users_dao -> update(array(
						'line_name' => $line_name,
					), $user -> id);
				}

				if(!empty($line_picture)) {
					$this -> users_dao -> update(array(
						'line_picture' => $line_picture,
					), $user -> id);
				}
				$user = $this -> users_dao -> find_by_id($user -> id);

			} else {
				// create user
				$i_data = array();
				$i_data['corp_id'] = 1;
				$i_data['is_app'] = 1;
				$i_data['account'] = $line_sub;
				$i_data['line_sub'] = $line_sub;
				$i_data['line_iat'] = time();

				if(!empty($line_name)) {
					$i_data['line_name'] = $line_name;
				}
				if(!empty($line_picture)) {
					$i_data['line_picture'] = $line_picture;
				}

				// get gift id
				$find_code = FALSE;
				while(!$find_code) {
					$code = get_random_digits(6);
					$c_list = $this -> users_dao -> find_all_by('gift_id', $code);
					$find_code = (count($c_list) == 0);
					$i_data['gift_id'] = $code;
				}

				$user_id = $this -> users_dao -> insert($i_data);

				$user = $this -> users_dao -> find_by_id($user_id);

				// check nick name
				$nick_name = $user -> line_name;

				$n_user = $this -> users_dao -> find_by("nick_name", $nick_name);
				if(!empty($n_user) || empty($nick_name)) { // 已被使用或是空的
					$nick_name = "user{$user->gift_id}";
				}
				$this -> users_dao -> update(array(
					'nick_name' => $nick_name
				), $user_id);
				$user = $this -> users_dao -> find_by_id($user_id);

				// 獲得50000金幣
				$corp = $this -> corp_dao -> find_by_id(1);
				$amt = $corp -> register_reward_amt;
				$tx = array();
				$tx['tx_type'] = "first_reward";
				$tx['tx_id'] = $user -> id;
				$tx['corp_id'] = $user -> corp_id; // corp id
				$tx['user_id'] = $user -> id;
				$tx['amt'] = $amt;

				$tx['brief'] = "獲得註冊獎金 $amt";
				$this -> wtx_dao -> insert($tx);

				$tx = array();
				$tx['corp_id'] = $user -> corp_id;
				$tx['amt'] = -$amt;
				$tx['income_type'] = "註冊獎金";
				$tx['income_id'] = $user -> id;
				$tx['note'] = "頒發註冊獎金 {$user->id} {$amt}";
				$this -> ctx_dao -> insert($tx);

				// call line
				$p = array();
				$p['to'] = $user -> line_sub;
				$p['messages'][] = array(
					"type" => "text",
					"text" => "恭喜您獲得註冊獎金 {$amt}"
				);
				$ret = call_line_api("POST", "https://api.line.me/v2/bot/message/push", json_encode($p), CHANNEL_ACCESS_TOKEN);

			}

			$res['is_valid_mobile'] = $user -> is_valid_mobile;

			// bd tokens
			$bd_token = md5(uniqid(rand(), true));

			// jwt token
			$payload = array();
			$payload['corp_id'] = $user -> corp_id;
			$payload['user_id'] = $user -> id;
			$payload['user_name'] = $user -> user_name;
			$payload['nick_name'] = $user -> nick_name;
			$payload['user_account'] = $user -> account;

			// update token
			$token = md5(uniqid(rand(), true));
			$payload['token'] = $token;

			$token = jwt_encode($payload, "jihad");
			// update last login & token
			$this -> users_dao -> update(array(
				'last_login_time' => date('Y-m-d H:i:s'),
				'bd_token' => $bd_token,
				'auth_token' => $token
			), $user -> id);
			$res['bd_token'] = $bd_token;
			$res['auth_token'] = $token;
			$i_data_1["user_id"] =$payload['user_id'];
			$i_data_1["ip"] = get_ip();
			$i_data_1["log_type"] = "app";
			$this-> ul_dao ->insert($i_data_1);
		}

		$this -> to_json($res);
	}

	public function check_token() {
		$res = array('success' => TRUE);

		$this -> do_log("check_token");

		$payload = $this -> get_payload();
		$user = $this -> users_dao -> find_by_id($payload['user_id']);

		$bd_token = $this -> get_post('bd_token');

		if(empty($user) || empty($bd_token)) {
			$res['error_msg'] = '缺少必要參數';
		} else {
			if(!empty($user)) {
				if($bd_token == $user -> bd_token) {
					$res['bd_token'] = $user -> bd_token;
				} else {
					$res['error_msg'] = 'bd_token不符';
				}
			} else {
				$res['error_msg'] = '查無使用者';
			}
		}

		$this -> to_json($res);
	}

	public function clear_mobile() {
		$res = array('success' => TRUE);

		$payload = $this -> get_payload();

		$user_id = $payload['user_id'];
		$user = $this -> users_dao -> find_by_id($user_id);
		if(!empty($user)) {
			$this -> users_dao -> update(array(
				'is_valid_mobile' => 0
			), $user_id);
		} else {
			$res['error_msg'] = "查無使用者";
		}
		$this -> to_json($res);
	}

	public function verify_mobile() {
		$res = array('success' => TRUE);

		$payload = $this -> get_payload();

		$user_id = $payload['user_id'];
		$mobile = $this -> get_post('mobile');

		if(empty($user_id) || empty($mobile)) {
			$res['error_msg'] = '缺少必要參數';
		} else {
			$user = $this -> users_dao -> find_by_id($user_id);
			if(!empty($user)) {
				if($user -> is_valid_mobile == 0) {
					$valid_mobile_users = $this -> users_dao -> find_all_valid_by_mobile_and_corp($user -> corp_id, $mobile);
					if(count($valid_mobile_users) > 0) {
						$res['error_msg'] = '此手機號碼已被認證';
					} else {
						$code = get_random_digits(4);
						$this -> users_dao -> update(array(
							'reg_code' => $code,
							'mobile' => $mobile
						), $user -> id);

						$corp = $this -> corp_dao -> find_by_id(1);

						$msg=iconv("UTF-8","big5","簡訊認證碼為 $code ，此認證碼將於30分鐘後失效。");
						if(!empty($corp -> cht_sms_account)) {
							$m_acc = $corp -> cht_sms_account;
							$m_pwd = $corp -> cht_sms_password;
							$r_url = "http://smexpress.mitake.com.tw/SmSendGet.asp"
								. "?username=$m_acc&password=$m_pwd&dstaddr=$mobile&DestName=SteveYeh&dlvtime=&vldtime=&smbody=$msg";

							$n_res = $this -> curl -> simple_get($r_url);
							// error_log($r_url);
							// $res["r_url"] = $r_url;
						}

						$res['code'] = $code;
					}
				} else {
					$res['error_msg'] = '此會員已認證過手機';
				}
			} else {
				$res['error_msg'] = '查無使用者';
			}
		}
		$this -> to_json($res);
	}

	public function verify_mobile_code() {
		$res = array('success' => TRUE);

		$payload = $this -> get_payload();
		$user_id = $payload['user_id'];
		$code = $this -> get_post('code');
		if(empty($user_id) || empty($code)) {
			$res['error_msg'] = '缺少必要參數';
		} else {
			$user = $this -> users_dao -> find_by_id($user_id);
			if(!empty($user)) {
				$valid_mobile_users = $this -> users_dao -> find_all_valid_by_mobile_and_corp($user -> corp_id, $user -> mobile);
				if(count($valid_mobile_users) > 0) {
					$res['error_msg'] = '此手機號碼已被認證';
				} else {
					if($user -> reg_code == $code) {
						$res['user_id'] = $user -> id;
						$this -> users_dao -> update(array(
							'is_valid_mobile' => 1
						), $user -> id);
					} else {
						$res['error_msg'] = '認證碼錯誤';
					}
				}
			} else {
				$res['error_msg'] = '查無此帳號';
			}
		}

		$this -> to_json($res);
	}

	function get_verify_mobile_bd() {
		$res = array('success' => TRUE);
		$user_id = $this -> get_post('user_id');

		if(empty($user_id)) {
			$res['error_msg'] = "缺少必填欄位";
		} else {
			$user = $this -> users_dao -> find_by_id($user_id);
			if(empty($user)) {
				$res['error_msg'] = "查無使用者";
			} else {
				if($user -> is_valid_mobile == 1) {
					if($user -> is_valid_mobile_paid == 0) {
						$tx = array();
						$tx['corp_id'] = $user -> corp_id; // corp id
						$tx['user_id'] = $user -> id;

						$amt = 30;
						$tx['amt'] = $amt;
						$tx['type_id'] = 141;
						$tx['brief'] = "會員 $user->account 認證個人資料獎勵藍鑽 $amt ";
						$this -> wtx_bdc_dao -> insert($tx);

						$this -> users_dao -> update(array(
							'is_valid_mobile_paid' => 1
						), $user_id);
					} else {
						$res['error_msg'] = "已領取過了";
					}
				} else {
					$res['error_msg'] = "尚未通過驗證";
				}
			}
		}

		$this -> to_json($res);
	}

	function get_upload_user_image_bd() {
		$res = array('success' => TRUE);
		$user_id = $this -> get_post('user_id');

		if(empty($user_id)) {
			$res['error_msg'] = "缺少必填欄位";
		} else {
			$user = $this -> users_dao -> find_by_id($user_id);
			if(empty($user)) {
				$res['error_msg'] = "查無使用者";
			} else {
				if($user -> image_id > 0) {
					if($user -> is_upload_image_paid == 0) {
						$tx = array();
						$tx['corp_id'] = $user -> corp_id; // corp id
						$tx['user_id'] = $user -> id;

						$amt = 100;
						$tx['amt'] = $amt;
						$tx['type_id'] = 142;
						$tx['brief'] = "會員 $user->account 上傳大頭照獎勵藍鑽 $amt ";
						$this -> wtx_bdc_dao -> insert($tx);

						$this -> users_dao -> update(array(
							'is_upload_image_paid' => 1
						), $user_id);
					} else {
						$res['error_msg'] = "已領取過了";
					}
				} else {
					$res['error_msg'] = "尚未通過驗證";
				}
			}
		}

		$this -> to_json($res);
	}

	function test_sms() {
		header("Content-Type:text/html; charset=utf-8");
		$corp = $this -> corp_dao -> find_by_id(1);
		$code = '1234';
		$mobile = '0925815921';
		$msg=iconv("UTF-8","big5","簡訊認證碼為 $code ，此認證碼將於30分鐘後失效。");
		if(!empty($corp -> cht_sms_account)) {
			$m_acc = $corp -> cht_sms_account;
			$m_pwd = $corp -> cht_sms_password;
			$n_res = $this -> curl -> simple_get("http://smexpress.mitake.com.tw:9600/SmSendGet.asp"
				. "?username=$m_acc&password=$m_pwd&dstaddr=$mobile&DestName=SteveYeh&dlvtime=&vldtime=&smbody=$msg");
				// echo $n_res;
				$n_res = urlencode($n_res);
				echo $n_res;
		}
	}

	public function ali_sms($mobile, $code) {
		// 17374011706
		AlibabaCloud::accessKeyClient('LTAI8jOk3rc03h1c', 'ryEA40EyE2AnogZc3XmxdW8WeIlA7l')
                        ->regionId('cn-hangzhou') // replace regionId as you need
                        ->asGlobalClient();

		try {
		    $result = AlibabaCloud::rpcRequest()
		                          ->product('Dysmsapi')
		                          // ->scheme('https') // https | http
		                          ->version('2017-05-25')
		                          ->action('SendSms')
		                          ->method('POST')
		                          ->options([
		                                        'query' => [
		                                          'PhoneNumbers' => $mobile,
		                                          'SignName' => 'WnA娱乐',
		                                          'TemplateCode' => 'SMS_162199626',
		                                          'TemplateParam' => '{"code":"' . $code . '"}',
		                                        ],
		                                    ])
		                          ->request();
		    // print_r($result->toArray());
				$arr = $result->toArray();
				$this -> post_log_dao -> insert(array(
					"tag" => "ali_sms_success",
					"post" => json_encode($arr),
				));
		} catch (ClientException $e) {
		    // echo $e->getErrorMessage() . PHP_EOL;
				$this -> post_log_dao -> insert(array(
					"tag" => "ali_sms_error",
					"post" => $e->getErrorMessage() . PHP_EOL,
				));
		} catch (ServerException $e) {
			$this -> post_log_dao -> insert(array(
				"tag" => "ali_sms_error",
				"post" => $e->getErrorMessage() . PHP_EOL,
			));
		}
		// echo "test sms";
	}

	private function do_log($tag = '', $note = '') {
		$i_data['post'] =json_encode($_POST, JSON_UNESCAPED_UNICODE);
		$actual_link = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
		$i_data['tag'] = $tag;
		$i_data['full_path'] = $actual_link;
		$i_data['note'] = $note;
		$i_data['q'] = file_get_contents('php://input');
		$i_data['h'] = json_encode(getallheaders());
		$this -> post_log_dao -> insert($i_data);
	}

}
?>
