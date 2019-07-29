<?php
class Baccarat_game extends MY_Base_Controller {

	function __construct() {
		parent::__construct();
		$this -> load -> model('Baccarat_tab_dao', 'bc_dao');
		$this -> load -> model('Users_dao', 'users_dao');
		$this -> load -> model('Images_dao', 'img_dao');
		$this -> load -> model('Corp_dao', 'corp_dao');

		$this -> load -> model('Baccarat_tab_round_dao', 'btr_dao');
		$this -> load -> model('Baccarat_tab_round_detail_dao', 'btrd_dao');
		$this -> load -> model('api/Baccarat_tab_round_bet_dao', 'btrb_dao');
		$this -> load -> model('Baccarat_tab_tx_dao', 'tab_tx_dao');
		$this -> load -> model('Baccarat_tab_safe_detail_dao', 'btsd_dao');

		$this -> load -> model('Wallet_tx_dao', 'wtx_dao');
		$this -> load -> model('User_level_dao', 'ul_dao');

		header("Access-Control-Allow-Origin: *");
	}

	public function index() {
		echo "hello..";
	}

	public function user_bet($user_id) {
		$search_type = $this -> get_get('search_type');
		$res = array();
		$res['success'] = TRUE;
		$list = $this -> btrb_dao -> find_by_user_id_and_search_type($user_id, $search_type);
		$res['list'] = $list;
		$this -> to_json($res);
	}

	public function user_money($user_id) {
		$samt = $this -> wtx_dao -> get_sum_amt($user_id);
		$s_unfinished = $this -> btrb_dao -> sum_un_finished($user_id);

		$this -> to_json(array('samt' => ($samt - $s_unfinished)));
	}

	public function tab_bet($tab_id) {
		$tab = $this -> bc_dao -> find_by_id($tab_id);

		$this -> to_json(array('item' => ($tab)));
	}

	public function list_tab($corp_id) {
		$res = array();
		$list = $this -> bc_dao -> find_all_public_by_corp($corp_id);
		$res['list'] = $list;
		$this -> to_json($res);
	}

	public function tab_info($tab_id) {
		$paly_seconds = PLAY_SECONDS; // 等待下注 -> 停止下注
		$opening_seconds = OPENING_SECONDS; // 停止下注 -> 開牌
		$open_seconds = OPEN_SECONDS; // 開牌 -> 派彩
		$bonus_seconds = BONUS_SECONDS; // 派彩 -> 結束

		$res = array();

		$a_tab = $this -> bc_dao -> find_by_id($tab_id);
		$res['tab_name'] = $a_tab -> tab_name;

		$a_round = $this -> btr_dao -> find_current_round($tab_id);
		$a_detail = $this -> btrd_dao -> find_current_round_detail($a_round -> id);
		// $a_detail -> winner_count = $this -> btrd_dao -> count_winner_type($a_round -> id);

		$road_list = $this -> btrd_dao -> list_road($a_round -> id);
		$res['road_list'] = $road_list;
		$winner_count = array();
		$winner_type_0['winner_type'] = 0;
		$winner_type_0['cnt'] = 0;
		$winner_type_1['winner_type'] = 1;
		$winner_type_1['cnt'] = 0;
		$winner_type_2['winner_type'] = 2;
		$winner_type_2['cnt'] = 0;
		$winner_type_3['winner_type'] = 3;
		$winner_type_3['cnt'] = 0;
		$winner_type_4['winner_type'] = 4;
		$winner_type_4['cnt'] = 0;
		$winner_type_5['winner_type'] = 5;
		$winner_type_5['cnt'] = 0;

		foreach($road_list as $each) {
			if($each -> winner == 0) {
				$winner_type_0['cnt']++;
			}
			if($each -> winner == 1) {
				$winner_type_1['cnt']++;
			}
			if($each -> winner == 2) {
				$winner_type_2['cnt']++;
			}
			if($each -> winner_type == 3) {
				$winner_type_3['cnt']++;
			}
			if($each -> winner_type == 4) {
				$winner_type_4['cnt']++;
			}
			if($each -> winner_type == 6) {
				$winner_type_3['cnt']++;
				$winner_type_4['cnt']++;
			}
		}
		$winner_count[] = $winner_type_0;
		$winner_count[] = $winner_type_1;
		$winner_count[] = $winner_type_2;
		$winner_count[] = $winner_type_3;
		$winner_count[] = $winner_type_4;
		$winner_count[] = $winner_type_5;
		$a_detail -> winner_count = $winner_count;

		$res['round'] = $a_round;
		$now = time();
		$res['time'] = $now;

		$res['all_bets'] = $this -> btrb_dao -> find_all_by('detail_id', $a_detail -> id);

		switch ($a_detail -> status) {
			case 1:
				$res['count_down'] = $paly_seconds;
				$res['count_down_now'] = $paly_seconds - ($now - $a_detail -> start_time_unix);
				break;
			case 2:
				$res['count_down'] = $opening_seconds;
				$res['count_down_now'] = $opening_seconds - ($now - $a_detail -> opening_time_unix);
				break;
			case 3:
				$res['count_down'] = $open_seconds;
				$res['count_down_now'] = $open_seconds - ($now - $a_detail -> open_time_unix);
				break;
			case 4:
				$res['count_down'] = $bonus_seconds;
				$res['count_down_now'] = $bonus_seconds - ($now - $a_detail -> bonus_time_unix);
				break;

			default:
				$res['count_down'] = 0;
				$res['count_down_now'] = 0;
				break;
		}

		// guess system
		if(rand(0, 100) > 30) { // right
			$res['guess_winner'] = $a_detail -> winner;
			$res['guess_winner_type'] = $a_detail -> winner_type;
		} else {
			$res['guess_winner'] = ($a_detail -> winner == 0 || $a_detail -> winner == 1 ? 2 : 1);
			$res['guess_winner_type'] = ($a_detail -> winner_type == 6 ? 0 : ($a_detail -> winner_type == 3 ? 4 : 3));
		}

		if($a_detail -> status < 3) {
			unset($a_detail -> winner);
			unset($a_detail -> winner_type);
			unset($a_detail -> player_c_0);
			unset($a_detail -> player_c_1);
			unset($a_detail -> player_c_2);
			unset($a_detail -> player_val);
			unset($a_detail -> banker_c_0);
			unset($a_detail -> banker_c_1);
			unset($a_detail -> banker_c_2);
			unset($a_detail -> banker_val);
		} else {
			$a_detail -> player_arr = array();
			$a_detail -> banker_arr = array();
			if(isset($a_detail -> player_c_0)) {
				$a_detail -> player_arr[] = $a_detail -> player_c_0;
			}
			if(isset($a_detail -> player_c_1)) {
				$a_detail -> player_arr[] = $a_detail -> player_c_1;
			}
			if(isset($a_detail -> player_c_2)) {
				$a_detail -> player_arr[] = $a_detail -> player_c_2;
			}
			if(isset($a_detail -> banker_c_0)) {
				$a_detail -> banker_arr[] = $a_detail -> banker_c_0;
			}
			if(isset($a_detail -> banker_c_1)) {
				$a_detail -> banker_arr[] = $a_detail -> banker_c_1;
			}
			if(isset($a_detail -> banker_c_2)) {
				$a_detail -> banker_arr[] = $a_detail -> banker_c_2;
			}
		}
		// $a_detail -> banker_arr = array("0", "31");
		// $a_detail -> player_arr = array("41","34","39");
		$res['detail'] = $a_detail;

		$this -> to_json($res);
	}

	public function do_bet() {
		$res = array();
		$res['success'] = TRUE;

		$user_id = $this -> get_post('user_id');
		$detail_id = $this -> get_post('detail_id');

		// create new or find old
		$a_bet = $this -> btrb_dao -> check_new($user_id, $detail_id);

		$bets = $this -> get_post('bets');
		$bets = json_decode($bets);
		$res['bets'] = $bets;
		$i_data['user_id'] = $user_id;
		$user = $this -> users_dao -> find_by_id($user_id);

		if(!empty($user)) {
			$i_data['corp_id'] = $user -> corp_id;
			$i_data['detail_id'] = $detail_id;

			$bet_val = 0;
			for($i = 0; $i<5 ; $i++) {
				$this_bet_val = (!empty($bets[$i]) ? $bets[$i] : 0);

				$col_i = ('bet_' . $i);
				$bet_val += $this_bet_val; // new
				$i_data[$col_i] = $this_bet_val;
			}
			$i_data['total_bet'] = $bet_val;

			$rolling_val = 0; // add old
			$rolling_val += (!empty($bets[0]) ? $bets[0] : 0);
			$rolling_val += (!empty($bets[3]) ? $bets[3] : 0);
			$rolling_val += (!empty($bets[4]) ? $bets[4] : 0);
			if(!empty($bets[1]) && !empty($bets[2])) {
				$rolling_val += abs($bets[1] - $bets[2]);
			} else {
				$rolling_val += (!empty($bets[1]) ? $bets[1] : 0);
				$rolling_val += (!empty($bets[2]) ? $bets[2] : 0);
			}
			$i_data['rolling'] = $rolling_val;

			$res['i_data'] = $i_data;

			// remaining
			$samt = $this -> wtx_dao -> get_sum_amt($user_id);
			$s_unfinished = $this -> btrb_dao -> sum_un_finished_without_me($user_id, $a_bet -> id);

			// still can bet
			if($bet_val + $s_unfinished <= $samt) {
				$this -> btrb_dao -> update($i_data, $a_bet -> id);
			} else {
				$res['error'] = TRUE;
				$res['error_msg'] = "金額不足";
				$res['error_code'] = "not_enough_money";
			}
		} else {
			$res['error'] = TRUE;
			$res['error_msg'] = "查無使用者";
		}


		$this -> to_json($res);
	}

	public function bet_recored($user_id) {
		$res = array();
		$res['success'] = TRUE;

		$list = $this -> btrb_dao -> find_by_user($user_id);
		foreach($list as $each) {
			$each -> detail = $this -> btrd_dao -> find_by_id($each -> detail_id);
		}
		$res['list'] = $list;
		$this -> to_json($res);
	}

	public function test() {

		$res = "hello..";

		echo $res;
	}
}
?>
