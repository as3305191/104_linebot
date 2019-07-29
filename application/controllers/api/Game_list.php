<?php
class Game_list extends MY_Base_Controller {

	function __construct() {
		parent::__construct();
		$this -> load -> model('Game_list_dao', 'gl_dao');
	}

	public function go($game_id, $user_id) {
		$user = $this -> users_dao -> find_by_id($user_id);
		$game = $this -> gl_dao -> find_by_id($game_id);
		if(empty($user)) {
			echo "查無使用者";
			return;
		}
		if(empty($game)) {
			echo "查無此遊戲";
			return;
		}
		if($game -> is_wang == 1) {
			$this -> go_wang($game, $user);
		} else {
			if($game -> id == 1) {
				redirect("https://wa-lotterygame.com/games/{$game->file_path}?user_id={$user_id}&tab_id={$user->corp_id}&hall_id=0");
			} else {
				redirect("https://wa-lotterygame.com/games/{$game->file_path}?corp_id={$user->corp_id}&user_id={$user_id}");
			}
		}
	}

	public function go_wang($game, $user) {
		$res = array('success' => TRUE);

		if(!empty($user)) {
			$corp = $this -> corp_dao -> find_by_id($user -> corp_id);
			$params = array(
				'SessionId' => "{$user->wang_session_id}",
				'GameId' => "$game->wang_game_id",
			);
			$res['params'] = $params;

			$params = json_encode($params, JSON_NUMERIC_CHECK);
			$output = py_des3_encode($params);

			$p = array();
			$p["Params"] = $output;
			$p["AccessToken"] = $corp -> wang_token;
			$n_res = $this -> curl -> simple_post(WANG_URL . "RedirectToGame", $p);
			$res['n_res'] = json_decode($n_res);
			$res = json_decode($n_res);
			if($res -> ErrorCode == 0) {
				$res = py_des3_decode($res -> Data);
				$obj = json_decode($res);
				redirect($obj -> RedirectUrl);
			} else {
				$this -> to_json($res);
			}
			return;
		} else {
			$res['error_msg'] = "查無使用者";
 		}

		$this -> to_json($res);
	}

	public function status() {
		$res = array();
		$res['list'] = $this -> gl_dao -> find_all();
		$this -> to_json($res);
	}

	public function status_by_corp() {
		$res = array("success" => TRUE);
		$this -> gl_dao -> check_clone();

		$corp_id = $this -> get_post('corp_id');
		if(!empty($corp_id)) {
			$res['list'] = $this -> gl_dao -> find_all_by_corp($corp_id);
		} else {
			$res['error_msg'] = "缺少corp_id";
		}

		$this -> to_json($res);
	}
}
?>
