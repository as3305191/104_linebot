<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Line_callback extends MY_Base_Controller {

	function __construct() {
		parent::__construct();

		$this -> load -> model('Users_dao', 'users_dao');
		$this -> load -> model('Post_log_dao', 'post_log_dao');
		$this -> load -> model('Promo_game_dao', 'pg_dao');
		$this -> load -> model('Wallet_tx_dao', 'wtx_dao');
		$this -> load -> model('Com_tx_dao', 'ctx_dao');
		$this -> load -> model('Corp_dao', 'corp_dao');
	}

	public function index() {
		echo "line_callback";
		$this -> do_log("Line_callback");

		$code = $this -> get_get("code");
		$state = $this -> get_get("state");
		$error = $this -> get_get("error");
		$friendship_status_changed = $this -> get_get("friendship_status_changed");

		// show error
		if(!empty($error)) {
			echo "error:" . $error;
			return;
		}
		echo date("Y-m-d H:i:s");

		$params = array();
		$params["grant_type"] = "authorization_code";
		$params["code"] = $code;
		$params["client_id"] = LOGIN_CHANNEL_ID;
		$params["client_secret"] = LOGIN_CHANNEL_SECRET;
		$params["redirect_uri"] = BASE_URL . "/line_callback";

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded'));
		curl_setopt($ch, CURLOPT_URL, 'https://api.line.me/oauth2/v2.1/token');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_POST, 1);
		// Edit: prior variable $postFields should be $postfields;
		curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
		// curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0); // On dev server only!
		$result = curl_exec($ch);
		// $result = '{"access_token":"eyJhbGciOiJIUzI1NiJ9.Plo1be2E62-GGh4bxPfznnEvYF0MNoalTxHPgrxHMlJL04VeouSlgZ-mlit16w4Fn0jnyPM19evG4jN3GZtS8hK7dqFymTO0uyC3O4WAYrN_LJDV7IAfg9nFGF4Jq2ZoBVocBjOsQsoMCzYXKcP8h0FyWai3x9X2Uua5FtAUcbI.8GPnELQNgqU6dKJanq0sBgfDQUEb8D9uUSPZRS63RIc","token_type":"Bearer","refresh_token":"iVf7iJmRARpS82FLgSxv","expires_in":2592000,"scope":"openid profile","id_token":"eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwczovL2FjY2Vzcy5saW5lLm1lIiwic3ViIjoiVTNmODg2M2MzMTE5OThhM2Y5N2IzOWFjYWU2OTk5ZjA3IiwiYXVkIjoiMTU4ODc3OTQ2MSIsImV4cCI6MTU2MTAzNzkxNiwiaWF0IjoxNTYxMDM0MzE2LCJub25jZSI6ImFhYTEyMyIsIm5hbWUiOiLlsI_mnpcifQ.XdBOlmCUkrmyg18qkwx3arWyTyR99HXFNYoNPI7Z3IU"}';

		$this -> do_log("Line_callback_result", $result);

		$result = json_decode($result);

		if(!empty($result -> id_token)) {
			$payload = jwt_decode($result -> id_token, "8fff89d82ad8c0ec68334fdd2aef6a89");
			$user = $this -> users_dao -> find_by('line_sub', $payload['sub']);

			$this -> do_log("Line_callback_payload", json_encode($payload));
			if(empty($user) || empty($user -> line_sub)) {
				// create one
				$i_data = array();
				$i_data['corp_id'] = 1;
				$i_data['account'] = $payload['sub'];
				$i_data['line_sub'] = $payload['sub'];
				$i_data['line_iat'] = $payload['iat'];
				$i_data['line_name'] = $payload['name'];
				$i_data['line_picture'] = !empty($payload['picture']) ? $payload['picture'] : '';

				// get gift id
				$find_code = FALSE;
				while(!$find_code) {
					$code = generate_random_digit(6);
					$c_list = $this -> users_dao -> find_all_by('gift_id', $code);
					$find_code = (count($c_list) == 0);
					$i_data['gift_id'] = $code;
				}

				// 12碼錢包
				// get wallet code
				$find_code = FALSE;
				while(!$find_code) {
					$code = coin_token(16);
					$c_list = $this -> users_dao -> find_all_by('wallet_code', $code);
					$find_code = (count($c_list) == 0);
					$i_data['wallet_code'] = $code;
				}

				$user_id = $this -> users_dao -> insert($i_data);

				$user = $this -> users_dao -> find_by_id($user_id);

				// check nick name
				$nick_name = $user -> line_name;

				$n_user = $this -> users_dao -> find_by("nick_name", $nick_name);
				if(!empty($n_user)) { // 已被使用
					$nick_name = "user{$user->gift_id}";
				}
				$this -> users_dao -> update(array(
					'nick_name' => $nick_name
				), $user_id);
				$user = $this -> users_dao -> find_by_id($user_id);

				// game check
				// $this -> do_game_check($payload, $user);

				$this -> do_login($user);

				if(isset($payload['nonce']) && !empty($payload['nonce'])) {
					$gift_id = $payload['nonce'];
					$puser = $this -> users_dao -> find_by("gift_id", $gift_id);
					if(!empty($puser)) {
						$this -> users_dao -> update(array(
							'promo_user_id' => $puser -> id
						), $user -> id);

						//
						// // call line
						$p = array();
						$p['to'] = $puser -> line_sub;
						$p['messages'][] = array(
							"type" => "text",
							"text" => "您已推薦 {$user->nick_name} 成功"
						);
						$ret = call_line_api("POST", "https://api.line.me/v2/bot/message/push", json_encode($p), CHANNEL_ACCESS_TOKEN);
					}
				}

				// 獲得50000金幣
				$corp = $this -> corp_dao -> find_by_id(1);


				// call line
				$p = array();
				$p['to'] = $user -> line_sub;
				$p['messages'][] = array(
					"type" => "text",
					"text" => "恭喜您註冊成功"
				);
				$ret = call_line_api("POST", "https://api.line.me/v2/bot/message/push", json_encode($p), CHANNEL_ACCESS_TOKEN);

			} else {
				// game check
				// $this -> do_game_check($payload, $user);
				// update email
				$u_data = array();
				$u_data['line_name'] = $payload['name'];
				$u_data['line_iat'] = $payload['iat'];

				if(isset($payload['picture'])) {
					$u_data['line_picture'] = $payload['picture'];
				}

				if(empty($user -> nick_name)) {
					$u_data['nick_name'] = "user{$user->gift_id}";
				}

				$this -> users_dao -> update($u_data, $user -> id);
				$user = $this -> users_dao -> find_by_id($user -> id);
				// login
				$this -> do_login($user);
			}


			redirect($state . "?line_id=" . $payload['sub']);
		} else {
			echo "no token";
		}
	}

	public function lottery_ticket() {
		echo "line_callback--";
		$this -> do_log("Line_callback_lottery_ticket");

		$code = $this -> get_get("code");
		$state = $this -> get_get("state");
		$error = $this -> get_get("error");
		$friendship_status_changed = $this -> get_get("friendship_status_changed");

		// show error
		if(!empty($error)) {
			echo "error:" . $error;
			return;
		}
		echo date("Y-m-d H:i:s");

		$params = array();
		$params["grant_type"] = "authorization_code";
		$params["code"] = $code;
		$params["client_id"] = CHANNEL_ID;
		$params["client_secret"] = CHANNEL_SECRET;
		$params["redirect_uri"] = BASE_URL . "/line_callback/lottery_ticket";

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded'));
		curl_setopt($ch, CURLOPT_URL, 'https://api.line.me/oauth2/v2.1/token');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_POST, 1);
		// Edit: prior variable $postFields should be $postfields;
		curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
		// curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0); // On dev server only!
		$result = curl_exec($ch);

		$this -> do_log("Line_callback_result", $result);

		$result = json_decode($result);
		if(!empty($result -> id_token)) {
			$payload = jwt_decode($result -> id_token, "empty");
			$user = $this -> users_dao -> find_by('line_sub', $payload['sub']);

			// $this -> do_log("Line_callback_payload_str", $payload);
			$this -> do_log("Line_callback_payload", json_encode($payload));
			if(empty($user)) {
				// find by email
				$email = !empty($payload['email']) ? $payload['email'] : '';

				$user = NULL;
				if(!empty($email)) {
					$user = $this -> users_dao -> find_by('account', $email);
				}

				if(!empty($user) && FALSE) { // 不會跑這邊
					// update email
					$u_data['line_email'] = $email;
					$u_data['line_iat'] = $payload['iat'];
					$u_data['line_name'] = $payload['name'];
					$u_data['line_email'] = $email;
					$u_data['line_picture'] = $payload['picture'];

					$this -> users_dao -> update($u_data, $user -> id);
					$user = $this -> users_dao -> find_by_id($user -> id);

					// login
					$this -> do_login($user);
				} else {
					// create one
					$i_data = array();
					$i_data['account'] = !empty($email) ? $email : $payload['sub'];
					$i_data['line_sub'] = $payload['sub'];
					$i_data['line_iat'] = $payload['iat'];
					$i_data['line_name'] = $payload['name'];
					$i_data['line_email'] = $email;
					$i_data['line_picture'] = $payload['picture'];

					$user_id = $this -> users_dao -> insert($i_data);

					// $this -> users_dao -> update(array(
					// 	'game_sn' => date("mdHi{$user_id}")
					// ), $user_id);
					$user = $this -> users_dao -> find_by_id($user_id);

					// game check
					// $this -> do_game_check($payload, $user);

					$this -> do_login($user);
				}
			} else {
				// game check
				// $this -> do_game_check($payload, $user);

				// login
				$this -> do_login($user);
			}

			if($payload['nonce'] != "aaa123") {
				redirect(BASE_URL . "/home/promo/" . $payload['nonce']);
				return;
			}
			redirect(BASE_URL . "/home/lottery_ticket?uid={$user->id}");
		}
	}

	function do_game_check($payload, $user) {
		$pg = NULL;
		if($payload['nonce'] != "aaa123") { // 沒有推薦遊戲就是aaa123
			$pg = $this -> pg_dao -> find_by("sn", $payload['nonce']);
			if(!empty($pg)) {
				$i_data = array();
				$i_data["user_id"] = $user -> id;
				$i_data["parent_user_id"] = $pg -> user_id;
				$i_data["parent_game_id"] = $pg -> id;
				$last_id = $this -> pg_dao -> insert($i_data);
				$this -> pg_dao -> update(array(
					'sn' =>  date("mdHi{$last_id}")
				), $last_id);
			}
		}

		return $pg;
	}

	function do_login($user) {
		$this -> session -> set_userdata('l_user', $user); // set login user
	}

	function dec() {
		$acc = "eyJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwczovL2FjY2Vzcy5saW5lLm1lIiwic3ViIjoiVWFiZjEzZTRjOTIzODY2OWE4NTc0MDE3NjM2NDM4NDdjIiwiYXVkIjoiMTYxMzE0ODIwMSIsImV4cCI6MTUzOTk0MDI4OSwiaWF0IjoxNTM5OTM2Njg5LCJub25jZSI6ImFhYTEyMyIsIm5hbWUiOiLlsI_mnpciLCJwaWN0dXJlIjoiIn0.Nv07taj-t_DJjwP8zS1Beo6ZfTrguwZ8eFkL7r-DWMU";
		$res = jwt_decode($acc, "8fff89d82ad8c0ec68334fdd2aef6a89");
		echo json_encode($res);
	}

	function login() {
		echo "login...";
		$this -> do_log("Line_callback login");
	}

	private function do_log($tag = '', $note = '') {
		$i_data['post'] =json_encode($_POST, JSON_UNESCAPED_UNICODE);
		$actual_link = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
		$i_data['tag'] = $tag;
		$i_data['full_path'] = $actual_link;
		$i_data['note'] = $note;
		$this -> post_log_dao -> insert($i_data);
	}

	public function do_test() {
		// call line
		$puser = $this -> users_dao -> find_by_id(29);
		$amt = 1999;
		$p = array();
		$p['to'] = $puser -> line_sub;
		$p['messages'][] = array(
			"type" => "text",
			"text" => "獲得推廣獎金 {$amt}"
		);
		$ret = call_line_api("POST", "https://api.line.me/v2/bot/message/push", json_encode($p), CHANNEL_ACCESS_TOKEN);
	}


}
