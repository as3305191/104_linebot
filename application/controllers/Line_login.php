<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use AlibabaCloud\Client\AlibabaCloud;
use AlibabaCloud\Client\Exception\ClientException;
use AlibabaCloud\Client\Exception\ServerException;

class Line_login extends MY_Base_Controller {
	var $_promo_sn = "";
	var $_promo_user_id = "";

	function __construct() {
		parent::__construct();

		$this -> load -> model('Users_dao', 'users_dao');
		$this -> load -> model('Corp_dao', 'corp_dao');
		$this -> load -> model('Wallet_tx_dao', 'wtx_dao');
	}

	public function index() {
		$data = array();
		$_promo_sn = $this -> _promo_sn;
		$_promo_user_id = $this -> _promo_user_id;

		$promo = $this -> get_get("promo");

		$l_user = $this->session->userdata('l_user');
		if(!empty($l_user)) {
			$l_user = $this -> users_dao -> find_by_id($l_user -> id);
			$data['l_user'] = $l_user;
			$data['sum_amt'] = $this -> wtx_dao -> get_sum_amt($l_user -> id);
		}

		if(!empty($promo)) {
			$p_user = $this -> users_dao -> find_by("gift_id", $promo);
			if(!empty($p_user)) {
				$data['p_user'] = $p_user;
			} else {
				show_404();
			}
		}

		if(!empty($l_user)) {
			if($l_user -> is_valid_mobile == 0 && FALSE) {
				// verify mobile
				redirect('line_login/verify_mobile');
			} else {
				// login ok
				$this -> load -> view('line/iagree', $data);
			}
		} else {
			$this -> load -> view('line/iagree', $data);
		}
	}

	public function verify_mobile() {
		$l_user = $this->session->userdata('l_user');
		if(!empty($l_user)) {
			$l_user = $this -> users_dao -> find_by_id($l_user -> id);
			$data['l_user'] = $l_user;
			$this -> load -> view('line/phone_binding01', $data);
		} else {
			redirect("line_login");
		}
	}

	public function verify_mobile_code() {
		$l_user = $this->session->userdata('l_user');
		if(!empty($l_user)) {
			$l_user = $this -> users_dao -> find_by_id($l_user -> id);
			$data['l_user'] = $l_user;
			// $this -> load -> view('line_verify_mobile_regcode', $data);
			$this -> load -> view('line/phone_binding02', $data);
		} else {
			redirect("line_login");
		}
	}

	public function submit_mobile() {
		$res['success'] = TRUE;
		$l_user = $this->session->userdata('l_user');
		if(!empty($l_user)) {
			$mobile = $this -> get_post("mobile");
			if(!empty($mobile)) {
				$m_users = $this -> users_dao -> find_all_by('mobile', $mobile);
				$has_valid_mobile = FALSE;
				foreach($m_users as $user) {
					if($user -> is_valid_mobile == 1) {
						$has_valid_mobile = TRUE;
					}
				}
				if($has_valid_mobile) {
					$res['error_msg'] = "該手機已被註冊";
				} else {
					$reg_code = get_random_digits(4);

					$corp = $this -> corp_dao -> find_by_id(1);

					$lang = 'cht';
					if(mb_strlen($mobile) > 10) {
						// 大陸手機
						$lang = 'chs';
					}
					$res['corp'] = $corp;
					$this -> users_dao -> update(array(
						"mobile" => $mobile,
						"reg_code" => $reg_code,
						"lang" => $lang,
					), $l_user -> id);

					if($lang == 'cht') {
						$res['lang'] = 'cht';
						$msg=iconv("UTF-8","big5","簡訊認證碼為 $reg_code ，此認證碼將於30分鐘後失效。");
						if(!empty($corp -> cht_sms_account)) {
							$m_acc = $corp -> cht_sms_account;
							$m_pwd = $corp -> cht_sms_password;
							$n_res = $this -> curl -> simple_get("http://smexpress.mitake.com.tw:9600/SmSendGet.asp"
								. "?username=$m_acc&password=$m_pwd&dstaddr=$mobile&DestName=SteveYeh&dlvtime=&vldtime=&smbody=$msg");
							$res['n-res'] = $n_res;
						} else {
							$res['empty_smg'] = TRUE;
						}
					} else {
						// 寄送大陸簡訊
						$this -> ali_sms($mobile, $reg_code);
					}
				}
			} else {
				$res['error_msg'] = '手機為空';
			}
		} else {
			$res['error_msg'] = '登入有誤';
		}
		$this -> to_json($res);
	}

	public function verify_mobile_reg_code() {
		$res['success'] = TRUE;

		$l_user = $this->session->userdata('l_user');
		if(!empty($l_user)) {
			$reg_code = $this -> get_post("reg_code");
			$intro_code = $this -> get_post("intro_code");
			$l_user = $this -> users_dao -> find_by_id($l_user -> id);
			if(!empty($l_user)) {

				if($l_user -> reg_code == $reg_code) {
					$u_data = array();
					$u_data['is_valid_mobile'] = 1;
					// valid
					if(!empty($intro_code)) {
						$i_user = $this -> users_dao -> find_by("code", $intro_code);
						$res['i_user'] = $i_user;
						if(empty($i_user)) {
							$res['error_msg'] = "推薦碼不存在";
						} else {
							$u_data['intro_id'] = $i_user -> id;
						}
					}

					if(empty($res['error_msg'])) {
						// 沒有錯誤的話
						$this -> users_dao -> update($u_data, $l_user -> id);

						$p = array();
						$p['to'] = $l_user -> line_sub;
						$p['messages'][] = array(
							"type" => "text",
							"text" => "恭喜驗證成功"
						);
						$p['messages'][] = array(
								'type' => 'template', // 訊息類型 (模板)
								'altText' => '線上儲值|好友贈送|至體驗區免費試玩', // 替代文字
								'template' => array(
										'type' => 'buttons', // 類型 (按鈕)
										'text' => '線上儲值|好友贈送|至體驗區免費試玩', // 文字
										'actions' => array(
												array(
														'type' => 'message', // 類型 (訊息)
														'label' => '線上儲值', // 標籤 2
														'text' => '線上儲值' // 用戶發送文字
												),
												array(
														'type' => 'message', // 類型 (訊息)
														'label' => '好友贈送', // 標籤 2
														'text' => '好友贈送' // 用戶發送文字
												),
												array(
														'type' => 'uri', // 類型 (訊息)
														'label' => '至體驗區免費試玩', // 標籤 2
														'uri' => 'https://wa-lotterygame.com/wa_backend/line/line/phone_binding03' // 用戶發送文字
												),
										)
								)
						);
						$res = call_line_api("POST", "https://api.line.me/v2/bot/message/push", json_encode($p), CHANNEL_ACCESS_TOKEN);
					}
				} else {
					$res['error_msg'] = "認證碼錯誤";
				}
			} else {
				$res['error_msg'] = '手機為空';
			}
		} else {
			$res['error_msg'] = '登入有誤';
		}
		$this -> to_json($res);
	}

	public function signout() {
		$this -> session -> sess_destroy();
		redirect("line_login");
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

}
