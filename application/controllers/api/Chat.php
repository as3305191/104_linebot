<?php
class Chat extends MY_Base_Controller {

	function __construct() {
		parent::__construct();

		$this -> load -> model('Chat_room_dao', 'chat_room_dao');
		$this -> load -> model('Chat_message_dao', 'chat_message_dao');
		$this -> load -> model('Chat_friends_dao', 'chat_friends_dao');

		$this -> load -> model('Wallet_tx_dao', 'wtx_dao');
		$this -> load -> model('Wallet_tx_bdc_dao', 'wtx_bdc_dao');
		$this -> load -> model('Bonus_tx_dao', 'tx_dao');
		// $this -> load -> model('Baccarat_tab_round_bet_dao', 'btrb_dao');

		$this -> load -> model('Users_dao', 'users_dao');
	}

	public function send_in_public() {
		$res = array();
		$res['success'] = TRUE;
		$data['corp_id'] = $this -> get_post('corp_id');
		$data['user_id'] = $this -> get_post('user_id');
		$data['message_type'] = $this -> get_post('message_type');
		$data['message'] = $this -> get_post('message');
		$data['emoji_name'] = $this -> get_post('emoji_name');

		$last_id = $this -> chat_message_dao -> insert($data);

		$res['list'] = array();
		$res['list'][] = $this -> chat_message_dao -> find_me($last_id);
		// $res["sql"] = $this -> chat_message_dao -> db -> last_query();

		$this -> to_json($res);
	}

	public function send_message() {
		$res = array();
		$res['success'] = TRUE;

		$payload = $this -> get_payload();
		$user_id = $payload['user_id'];

		$data['user_id'] = $user_id;
		$data['target_user_id'] = $this -> get_post('target_user_id');
		$data['message_type'] = $this -> get_post('message_type');
		$data['message'] = $this -> get_post('message');
		$data['emoji_name'] = $this -> get_post('emoji_name');

		if(empty($data['emoji_name'])) {
			$data['emoji_name'] = '';
		}

		$this -> chat_message_dao -> add_message($data);

		$this -> to_json($res);
	}

	public function block_friend() {
		$res = array();
		$res['success'] = TRUE;

		$payload = $this -> get_payload();
		$user_id = $payload['user_id'];

		$target_user_id = $this -> get_post('target_user_id');
		$this -> chat_friends_dao -> block_friend($user_id, $target_user_id);

		$this -> to_json($res);
	}

	public function remove_friend() {
		$res = array();
		$res['success'] = TRUE;

		$payload = $this -> get_payload();
		$user_id = $payload['user_id'];

		$target_user_id = $this -> get_post('target_user_id');
		$this -> chat_friends_dao -> remove_friend($user_id, $target_user_id);

		$this -> to_json($res);
	}

	public function list_rooms() {
		$res = array();
		$res['success'] = TRUE;

		$payload = $this -> get_payload();
		$user_id = $payload['user_id'];

		$query = $this -> get_post('query');
		if(empty($user_id)) {
			$res['error_msg'] = "缺少必要欄位";
		} else {
			$list = $this -> chat_room_dao -> list_by_user_and_query($user_id, $query);
			foreach($list as $each) {
				$each -> friend_image_url = !empty($each -> friend_image_id) ? base_url("api/images/get/{$each->friend_image_id}") : '';
				$each -> friend_image_url_thumb = !empty($each -> friend_image_id) ? base_url("api/images/get/{$each->friend_image_id}/thumb") : '';
			}
			$res['list'] = $list;
		}
		$this -> to_json($res);
	}

	public function list_friends() {
		$res = array();
		$res['success'] = TRUE;
		$payload = $this -> get_payload();
		$user_id = $payload['user_id'];

		$query = $this -> get_post('query');
		if(empty($user_id)) {
			$res['error_msg'] = "缺少必要欄位";
		} else {
			$list = $this -> chat_friends_dao -> list_by_user_and_query($user_id, $query);
			foreach($list as $each) {
				$each -> friend_image_url = !empty($each -> friend_image_id) ? base_url("api/images/get/{$each->friend_image_id}") : '';
				$each -> friend_image_url_thumb = !empty($each -> friend_image_id) ? base_url("api/images/get/{$each->friend_image_id}/thumb") : '';
			}
			$res['list'] = $list;
		}
		$this -> to_json($res);
	}

	public function list_up_message() {
		$res = array();
		$res['success'] = TRUE;
		$room_id = $this -> get_post('room_id');
		$min_msg_id = $this -> get_post('min_msg_id');
		$length = $this -> get_post('length');
		if(empty($length)) {
			$length  = 20;
		}

		if(empty($room_id) || empty($min_msg_id)) {
			$res['error_msg'] = "缺少必要欄位";
		} else {
			$res['list'] = $this -> chat_message_dao -> list_down_messages($room_id, $length, $min_msg_id, TRUE);
		}
		$this -> to_json($res);
	}

	public function list_down_message() {
		$res = array();
		$res['success'] = TRUE;
		$room_id = $this -> get_post('room_id');
		$max_msg_id = $this -> get_post('max_msg_id');
		$length = $this -> get_post('length');
		if(empty($length)) {
			$length  = 20;
		}

		if(empty($room_id)) {
			$res['error_msg'] = "缺少必要欄位";
		} else {
			if(empty($max_msg_id)) {
				$res['list'] = $this -> chat_message_dao -> list_down_messages($room_id, $length);
			} else {
				$res['list'] = $this -> chat_message_dao -> list_down_messages($room_id, $length, $max_msg_id);
			}

		}
		$this -> to_json($res);
	}

	public function find_friend() {
		$res = array();
		$res['success'] = TRUE;
		$payload = $this -> get_payload();
		$corp_id = $payload['corp_id'];

		$query = $this -> get_post('query');
		$user_id = $this -> get_post('user_id');
		if(empty($corp_id) || ((empty($query) || mb_strlen($query) < 1) && empty($user_id))) {
			$res['error_msg'] = "缺少必要欄位";
		} else {
			// $user = $this -> users_dao -> find_by_account_and_corp($corp_id, $query);
			$user = NULL;
			if(!empty($user_id)) {
				$user = $this -> users_dao -> find_by_id($user_id);
			}
			if(!empty($query) && empty($user)) {
				$user = $this -> users_dao -> find_by("nick_name", $query);
			}

			// $res['sql'] = $this -> users_dao -> db -> last_query();
			if(!empty($user)) {
				$res['user_id'] = $user -> id;
				$res['sum_amt'] = $this -> wtx_dao -> get_sum_amt($user -> id);
				// $res['sum_rolling_amt'] = $this -> btrb_dao -> find_total_rolling_by_user_id($user -> id, date('Y-m-d', strtotime('-7 days')));
				// $res['intro_code'] = $user -> intro_code;
				$res['create_time'] = $user -> create_time;
				$res['last_login_time'] = $user -> last_login_time;
				// $res['user_account'] = $user -> account;
				$res['user_name'] = $user -> user_name;
				$res['nick_name'] = $user -> nick_name;

				$res['image_url'] = !empty($user -> image_id) ? base_url("api/images/get/{$user->image_id}") : '';
				$res['image_thumb_url'] = !empty($user -> image_id) ? base_url("api/images/get/{$user->image_id}/thumb") : '';
			} else {
				$res['error_msg'] = "查無使用者";
			}
		}
		$this -> to_json($res);
	}

	public function chat_with() {
		$res = array();
		$res['success'] = TRUE;

		$payload = $this -> get_payload();
		$user_id = $payload['user_id'];

		$target_user_id = $this -> get_post('target_user_id');
		if(empty($user_id) || empty($target_user_id)) {
			$res['error_msg'] = "缺少必要欄位";
		} else {
			if($user_id == $target_user_id) {
				$res['error_msg'] = "使用者重複";
			} else {
				$user_1 = $this -> users_dao -> find_by_id($user_id);
				$user_2 = $this -> users_dao -> find_by_id($target_user_id);
				if(!empty($user_1) && !empty($user_2)) {
					// add friend
					$this -> chat_friends_dao -> add_friend($user_1, $user_2);

					// create chat room
					$this -> chat_room_dao -> create_room($user_1, $user_2);
					$room = $this -> chat_room_dao -> find_my_room($user_1 -> id, $user_2 -> id);
					if(!empty($room)) {
						$f = $this -> users_dao -> find_by_id($room -> friend_user_id);
						$room -> friend_user_account = $f -> account;
					}
					$res['room'] = $room;
				} else {
					$res['error_msg'] = "查無使用者";
				}
			}
		}
		$this -> to_json($res);
	}



}
?>
