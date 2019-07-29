<?php
class Ranking extends MY_Base_Controller {

	function __construct() {
		parent::__construct();

		$this -> load -> model('Baccarat_tab_round_bet_dao', 'btrb_dao');
		$this -> load -> model('Baccarat_win_reward_dao', 'bwr_dao');
		$this -> load -> model('Ranking_dbc_weekly_dao', 'rdw_dao');
		$this -> load -> model('Users_dao', 'users_dao');
		$this -> load -> model('Wallet_tx_bdc_dao', 'wtx_bdc_dao');
		$this -> load -> model('Corp_dao', 'corp_dao');
	}

	public function baccarat() {
		$res = array();
		$res['success'] = TRUE;
		$corp_id = $this -> get_post("corp_id");
		$win_date = $this -> get_post("win_date");
		$limit = 100;
		if(empty($win_date)) {
			$res['error_msg'] = "缺少必要欄位";
		} else {
			$list = $this -> btrb_dao -> list_ranking_all($win_date, $limit);
			foreach($list as $each) {
				$each -> total_win = $each -> total_win * 100;
			}
			$res['list'] = $list;
		}

		$this -> to_json($res);
	}

	public function baccarat_reward() {
		$res = array();
		$res['success'] = TRUE;
		$win_date = date("Y-m-d");
		$limit = 3;
		// $corp_list = $this -> corp_dao -> find_active_all();
		// $list = $this -> btrb_dao -> list_ranking_all_ytc($win_date, $limit);
		// foreach ($list as $user) {
		// 	if($user -> corp_id == 112) {
		// 		$reward = 0;
		// 		if($user -> rank == 1) {
		// 			$reward = 10000;
		// 		}
		// 		if($user -> rank == 2) {
		// 			$reward = 3000;
		// 		}
		// 		if($user -> rank == 3) {
		// 			$reward = 2000;
		// 		}
		//
		// 		$bwr = array();
		// 		$bwr['corp_id'] = $user -> corp_id;
		// 		$bwr['win_date'] = $win_date;
		// 		$bwr['user_id'] = $user -> user_id;
		// 		$bwr['amt'] = $reward;
		// 		$bwr['rank'] = $user -> rank;
		// 		$last_id = $this -> bwr_dao -> insert($bwr);
		//
		// 		// player
		// 		$tx = array();
		// 		$tx['baccarat_win_reward_id'] = $last_id;
		// 		$tx['corp_id'] = $user -> corp_id; // corp id
		// 		$tx['user_id'] = $user -> user_id;
		// 		$tx['amt'] = $reward;
		// 		$tx['type_id'] = 130; // 百家樂排行獎金
		// 		$tx['brief'] = "會員 {$user->nick_name} 百家樂 {$win_date} 排行第 {$user->rank} 名 獲得藍鑽 {$reward}";
		// 		$this -> wtx_bdc_dao -> insert($tx);
		// 	}
		// }

		$this -> to_json($res);
	}

	public function list_year_week() {
		$res = array();
		$res['success'] = TRUE;
		$corp_id = $this -> get_post("corp_id");

		if(empty($corp_id)) {
			$res['error_msg'] = "缺少必要欄位";
		} else {
			$list = $this -> rdw_dao -> list_year_week($corp_id);
			foreach($list as $yw) {
				$y = substr("{$yw->year_week_id}",0, 4);
				$w = substr("{$yw->year_week_id}",4);
				// $yw -> year_id = $y;
				// $yw -> week_id = $w;
				$r = $this -> getStartAndEndDate($w, $y);
				$s = $r[0];
				$e = $r[1];
				$yw -> dt_str = "{$s}~{$e}";
				$yw -> start = "{$s}";
				$yw -> end = "{$e}";
			}
			$res['list'] = $list;
		}

		$this -> to_json($res);
	}

	public function bdc() {
		$res = array();
		$res['success'] = TRUE;
		$corp_id = $this -> get_post("corp_id");
		$year_week_id = $this -> get_post("year_week_id");

		if(empty($corp_id)) {
			$res['error_msg'] = "缺少必要欄位";
		} else {
			$list = $this -> rdw_dao -> list_rank_by_year_week($corp_id, $year_week_id);

			$res['list'] = $list;
		}

		$this -> to_json($res);
	}

	public function do_weekly() {
		$res = array();
		$res['success'] = TRUE;
		$year_id = intval(date("Y"));
		$week_id = intval(date('W')) -1;
		$year_week_id = "{$year_id}{$week_id}";

		$user_list = $this -> users_dao -> find_all();
		foreach($user_list as $user) {
			$item = $this -> rdw_dao -> find_by_user_and_year_week($user -> id, $year_week_id);
			$amt = $this -> wtx_bdc_dao -> get_sum_amt($user -> id);
			if(empty($item)) {
				// insert
				$i = array();
				$i['corp_id'] = $user -> corp_id;
				$i['user_id'] = $user -> id;
				$i['year_id'] = $year_id;
				$i['week_id'] = $week_id;
				$i['year_week_id'] = $year_week_id;
				$i['amt'] = $amt;
				$i['update_time'] = date("Y-m-d H:i:s");
				$this -> rdw_dao -> insert($i);
			} else {
				// update
				$this -> rdw_dao -> update(array(
					'amt' => $amt,
					'update_time' => date("Y-m-d H:i:s")
				), $item -> id);
			}
		}

		$this -> to_json($res);
	}

	function getStartAndEndDate($week, $year)
	{
	    $time = strtotime("1 January $year", time());
	    $day = date('w', $time);
	    $time += ((7*$week)+1-$day)*24*3600;
	    $return[0] = date('Y-m-d', $time);
	    $time += 6*24*3600;
	    $return[1] = date('Y-m-d', $time);
	    return $return;
	}

	function getIsoWeeksInYear($year) {
    $date = new DateTime;
    $date->setISODate($year, 53);
    return ($date->format("W") === "53" ? 53 : 52);
	}

	function server_time() {
		$res = array();
		$res['success'] = TRUE;
		$now = time();
		$due = date("Y-m-d", $now) . " 23:59:59";
		$unix_due = strtotime($due);
		$unix_diff = $unix_due - $now;
		$unix_diff = ($unix_diff < 0 ? 0 : $unix_diff);

		$res['unix_due'] = $unix_due;
		$res['rem_seconds'] = ($unix_diff % (60 * 60)) % 60;
		$res['rem_minutes'] = floor(($unix_diff % (60 * 60)) / 60);
		$res['rem_hours'] = floor($unix_diff / (60 * 60));
		$res['unix_time'] = $now;
		$res['date_time'] = date("Y-m-d H:i:s", $now);
		$this -> to_json($res);
	}
}
?>
