<?php
class Niuniu_tab_bet_dao extends MY_Model {

	function __construct() {
		parent::__construct();

		// initialize table name
		parent::set_table_name('niuniu_tab_bet');

		$this -> alias_map = array(

		);
	}

	function do_player_bet($tab_id, $user_id, $times, $hall_id) {
		$this -> load -> model('Niuniu_tab_round_dao', 'nn_tab_round_dao');

		$ret = array();
		$ret['success'] = TRUE;

		$a_round_list = $this -> nn_tab_round_dao -> find_unfinished($tab_id, $hall_id);
		if(count($a_round_list) > 0) {
			$a_round = $a_round_list[0];

			$list = $this -> find_all_by_round_and_user($a_round -> id, $user_id);
			if(count($list) == 0) {
				// 只能有一個
				$last_id = $this -> insert(array(
					'round_id' => $a_round -> id,
					'user_id' => $user_id,
					'tab_id' => $tab_id,
					'hall_id' => $hall_id,
					'is_banker' => 0,
					'player_times' => $times,
					'bet_amt' => nn_min_bet($hall_id),
				));
			} else {
				$ret['error_msg'] = "已經設定過倍數";
			}
		} else {
			$ret['error_msg'] = "尚未開局";
		}

		return $ret;
	}

	function do_banker_bet($tab_id, $user_id, $times, $hall_id) {
		$this -> load -> model('Niuniu_tab_round_dao', 'nn_tab_round_dao');

		$ret = array();
		$ret['success'] = TRUE;

		$a_round_list = $this -> nn_tab_round_dao -> find_unfinished($tab_id, $hall_id);
		if(count($a_round_list) > 0) {
			$a_round = $a_round_list[0];

			$list = $this -> find_all_by_round_and_user($a_round -> id, $user_id);

			if(count($list) == 0) { // 不存在
				$other_bankers = $this -> find_all_banker_by_round($a_round -> id);
				foreach($other_bankers as $a_banker) {
					// 刪除其他banker
					$this -> delete($a_banker -> id);
				}

				// 只能下注一次
				$list = $this -> find_all_banker_by_round_and_user($a_round -> id, $user_id);
				if(count($list) == 0) {
					$last_id = $this -> insert(array(
						'round_id' => $a_round -> id,
						'user_id' => $user_id,
						'tab_id' => $tab_id,
						'hall_id' => $hall_id,
						'is_banker' => 1,
						'banker_times' => $times,
						'bet_amt' => nn_min_bet($hall_id),
					));
				} else {
					$ret['error_msg'] = "莊家已存在";
				}
			} else {
				$ret['error_msg'] = "已經設定過倍數";
			}
		} else {
			$ret['error_msg'] = "尚未開局";
		}


		return $ret;
	}

	function find_all_by_tab($tab_id, $hall_id) {
		$this -> db -> where("tab_id", $tab_id);
		$this -> db -> where("hall_id", $hall_id);
		$list = $this -> db -> get($this -> table_name) -> result();
		return $list;
	}

	function find_all_by_round($round_id) {
		$this -> db -> where("round_id", $round_id);
		$list = $this -> db -> get($this -> table_name) -> result();
		return $list;
	}

	function find_all_by_round_and_user($round_id, $user_id) {
		$this -> db -> where("round_id", $round_id);
		$this -> db -> where("user_id", $user_id);
		$list = $this -> db -> get($this -> table_name) -> result();
		return $list;
	}

	function find_all_banker_by_round_and_user($round_id, $user_id) {
		$this -> db -> where("user_id", $user_id);
		$this -> db -> where("round_id", $round_id);
		$this -> db -> where("is_banker", 1);
		$list = $this -> db -> get($this -> table_name) -> result();
		return $list;
	}

	function find_all_banker_by_round($round_id) {
		$this -> db -> where("round_id", $round_id);
		$this -> db -> where("is_banker", 1);
		$list = $this -> db -> get($this -> table_name) -> result();
		return $list;
	}

	function find_all_player_by_round_and_user($round_id, $hall_id) {
		$this -> db -> where("user_id", $user_id);
		$this -> db -> where("round_id", $round_id);
		$this -> db -> where("is_banker", 0);
		$list = $this -> db -> get($this -> table_name) -> result();
		return $list;
	}

	function find_all_player_by_round($round_id) {
		$this -> db -> where("round_id", $round_id);
		$this -> db -> where("is_banker", 0);
		$list = $this -> db -> get($this -> table_name) -> result();
		return $list;
	}

	function find_all_by_user($user_id, $hall_id) {
		$this -> db -> where("user_id", $user_id);
		$this -> db -> where("hall_id", $hall_id);
		$this -> db -> order_by("id", "desc");
		$list = $this -> db -> get($this -> table_name) -> result();
		return $list;
	}
}
?>
