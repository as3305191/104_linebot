<?php
class Rl_tab_bet_dao extends MY_Model {

	function __construct() {
		parent::__construct();

		// initialize table name
		parent::set_table_name('rl_tab_bet');

		$this -> alias_map = array(

		);
	}

	function do_player_bet($tab_id, $pos, $user_id, $bet_amt, $bet_type, $bet_ball, $hall_id) {
		$this -> load -> model('Rl_tab_round_dao', 'nn_tab_round_dao');
		$this -> load -> model('Wallet_tx_dao', 'wtx_dao');
		$this -> load -> model('Wallet_tx_bkc_dao', 'wtx_bkc_dao');

		$ret = array();
		$ret['success'] = TRUE;

		$a_round_list = $this -> nn_tab_round_dao -> find_unfinished($tab_id, $hall_id);
		$item = NULL;
		$a_round = NULL;
		if(count($a_round_list) > 0) {
			$a_round = $a_round_list[0];

			if($a_round -> status != 0) {
				$ret['error_msg'] = "未在下注狀態";
			} else {
				$list = $this -> find_all_by_round_and_user($a_round -> id, $user_id);
				// $ret['list'] = $this -> db -> last_query();
				if(count($list) == 0 || TRUE) {
					// 只能有一個
					$last_id = $this -> insert(array(
						'round_id' => $a_round -> id,
						'user_id' => $user_id,
						'tab_id' => $tab_id,
						'hall_id' => $hall_id,
						'bet_amt' => $bet_amt,
						'bet_type' => $bet_type,
						'bet_ball' => $bet_ball,
					));
					$item = $this -> find_by_id($last_id);

					$tx = array();
					$tx['bj_bet_id'] = $item -> id;
					$tx['user_id'] = $item -> user_id;
					$tx['amt'] = -$item -> bet_amt;
					$tx['type_id'] = 130;
					$tx['brief'] = "Rl 下注 {$item->bet_amt} ";
					if($hall_id < 0) {
						$this -> wtx_bkc_dao -> insert($tx);
					} else {
						$this -> wtx_dao -> insert($tx);
					}
				} else {
					// $obj = $list[0];
					// // update
					// $this -> update(array(
					// 	'bet_amt' => $bet_amt
	 				// ), $obj -> id);
					//
					// $item = $this -> find_by_id($obj -> id);
					$ret['error_msg'] = "已下注";
				}
			}

		} else {
			$ret['error_msg'] = "尚未開局";
		}

		$ret['bet'] = $item;
		// $ret['round'] = $a_round;
		return $ret;
	}

	function do_player_bet_arr($tab_id, $pos, $user_id, $bet_amt_arr, $bet_type_arr, $bet_ball_arr, $hall_id) {
		$this -> load -> model('Rl_tab_round_dao', 'nn_tab_round_dao');
		$this -> load -> model('Wallet_tx_dao', 'wtx_dao');
		$this -> load -> model('Wallet_tx_bkc_dao', 'wtx_bkc_dao');

		$ret = array();
		$ret['success'] = TRUE;

		$a_round_list = $this -> nn_tab_round_dao -> find_unfinished($tab_id, $hall_id);
		$item = NULL;
		$a_round = NULL;

		$bet_arr = array();
		if(count($a_round_list) > 0) {
			$a_round = $a_round_list[0];

			if($a_round -> status != 0) {
				$ret['error_msg'] = "未在下注狀態";
			} else {
				$s_amt = $this -> wtx_dao -> get_sum_amt($user_id);
				$sum_bet_amt = 0;
				foreach($bet_amt_arr as $each) {
					$sum_bet_amt += floatval($each);
				}

				if($sum_bet_amt > $s_amt) {
					$ret['error_msg'] = "餘額不足";
				} else {
					$this -> db -> trans_begin();

					for($i = 0 ; $i < count($bet_amt_arr) ; $i++) {
						$bet_amt = $bet_amt_arr[$i];
						$bet_type = $bet_type_arr[$i];
						$bet_ball = $bet_ball_arr[$i];

						// 只能有一個
						$last_id = $this -> insert(array(
							'round_id' => $a_round -> id,
							'user_id' => $user_id,
							'tab_id' => $tab_id,
							'hall_id' => $hall_id,
							'bet_amt' => $bet_amt,
							'bet_type' => $bet_type,
							'bet_ball' => json_encode($bet_ball),
						));
						$item = $this -> find_by_id($last_id);

						$tx = array();
						
						$tx['bj_bet_id'] = $item -> id;
						$tx['user_id'] = $item -> user_id;
						$tx['amt'] = -$item -> bet_amt;
						$tx['type_id'] = 130;
						$tx['brief'] = "Rl 下注 {$item->bet_amt} ";
						if($hall_id < 0) {
							$this -> wtx_bkc_dao -> insert($tx);
						} else {
							$this -> wtx_dao -> insert($tx);
						}

						$bet_arr[] = $item;
					}

					if ($this->db->trans_status() === FALSE)
					{
					        $this->db->trans_rollback();
									$bet_arr = array();
					}
					else
					{
					        $this->db->trans_commit();
					}
				}
			}

		} else {
			$ret['error_msg'] = "尚未開局";
		}

		$ret['bet_arr'] = $bet_arr;
		// $ret['round'] = $a_round;
		return $ret;
	}

	function do_next_card($tab_id, $pos, $user_id, $hall_id) {

		$this -> load -> model('Rl_tab_round_dao', 'nn_tab_round_dao');

		$ret = array();
		$ret['success'] = TRUE;

		$a_round_list = $this -> nn_tab_round_dao -> find_unfinished($tab_id, $hall_id);
		$item = NULL;
		$a_round = NULL;

		if(count($a_round_list) > 0) {
			$a_round = $a_round_list[0];

			$list = $this -> find_all_by_round_pos_and_user($a_round -> id, $pos, $user_id);
			if(count($list) == 0) {
				$ret['error_msg'] = "尚未下注";
			} else {
				$obj = $list[0];
				$item = $this -> player_get_card($a_round -> id, $obj -> id);
			}
		} else {
			$ret['error_msg'] = "尚未開局";
		}

		$ret['bet'] = $item;
		// $ret['round'] = $a_round;
		return $ret;
	}

	function end_player_game($tab_id, $hall_id) {

		$this -> load -> model('Rl_tab_round_dao', 'nn_tab_round_dao');

		$ret = array();
		$ret['success'] = TRUE;

		$a_round_list = $this -> nn_tab_round_dao -> find_unfinished($tab_id, $hall_id);
		$a_round = NULL;

		if(count($a_round_list) > 0) {
			$a_round = $a_round_list[0];
			// 莊家補牌或是開牌
			if($a_round -> card_val < 17) { // 小於 17一律補牌
				// 補牌
				$a_round = $this -> banker_get_card($a_round -> id);
			}

			// 標記玩家完成
			$this -> nn_tab_round_dao -> update(array(
				'all_done' => 1
			), $a_round -> id);

			// reload
			$a_round = $this -> nn_tab_round_dao -> find_by_id($a_round -> id);
		} else {
			$ret['error_msg'] = "尚未開局";
		}

		// $ret['bet'] = $item;
		$ret['round'] = $a_round;
		return $ret;
	}

	function player_get_card($round_id, $bet_id) {
		$obj = $this -> find_by_id($bet_id);
		$card = $this -> nn_tab_round_dao -> next_card($round_id);

		for($i = 1 ; $i <= 5 ; $i++) {
			$prop = "card_{$i}";
			if($obj -> $prop == -1) {
				// update
				$this -> update(array(
					"$prop" => $card
				), $obj -> id);
				break;
			}
		}

		// reload
		$obj = $this -> find_by_id($obj -> id);

		$card_arr = array();
		for($i = 1 ; $i <= 5 ; $i++) {
			$prop = "card_{$i}";
			if($obj -> $prop > -1) {
				$card_arr[] = $obj -> $prop;
			}
		}

		$val = $this -> rl_val($card_arr);

		$this -> update(array(
			"card_val" => $val
		), $obj -> id);

		$obj = $this -> find_by_id($obj -> id);

		return $obj;
	}

	function banker_get_card($round_id) {
		$a_round = $this -> nn_tab_round_dao -> find_by_id($round_id);
		$card = $this -> nn_tab_round_dao -> next_card($round_id);
		// 補牌
		for($i = 1 ; $i <= 5 ; $i++) {
			$prop = "card_{$i}";
			if($a_round -> $prop == -1) {
				// update
				$this -> nn_tab_round_dao -> update(array(
					"$prop" => $card
				), $a_round -> id);
				break;
			}
		}

		// reload
		$a_round = $this -> nn_tab_round_dao -> find_by_id($a_round -> id);

		// 計算分數
		$card_arr = array();
		for($i = 1 ; $i <= 5 ; $i++) {
			$prop = "card_{$i}";
			if($a_round -> $prop > -1) {
				$card_arr[] = $a_round -> $prop;
			}
		}
		$val = $this -> rl_val($card_arr);

		// 更新分數
		$this -> nn_tab_round_dao -> update(array(
			"card_val" => $val
		), $a_round -> id);

		// reload
		$a_round = $this -> nn_tab_round_dao -> find_by_id($a_round -> id);
		return $a_round;
	}

	function rl_val($arr) {
		$val = 0;
		$a_arr = array();
		foreach($arr as $card) {
			$a_val = (($card + 1) % 13);
			if($a_val == 1) { // 有a
				$a_arr[] = $a_val;
			}
			if($a_val > 10) {
				$a_val = 10;
			}
			$val += $a_val;
		}

		if(count($a_arr) > 0 && ($val + 10) <= 21) {
			$val += 10; // 有a就加10
		}
		return $val;
	}

	function do_banker_bet($tab_id, $user_id, $times, $hall_id) {
		$this -> load -> model('Rl_tab_round_dao', 'nn_tab_round_dao');

		$ret = array();
		$ret['success'] = TRUE;

		$a_round_list = $this -> nn_tab_round_dao -> find_unfinished($tab_id, $hall_id);
		if(count($a_round_list) > 0) {
			$a_round = $a_round_list[0];

			$list = $this -> find_all_by_round_and_user($a_round -> id, $user_id);

			if(count($list) == 0) { // 不存在
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
		$this -> db -> order_by("pos", "asc");
		$list = $this -> db -> get($this -> table_name) -> result();
		return $list;
	}

	function find_all_by_round_and_user($round_id, $user_id) {
		$this -> db -> where("round_id", $round_id);
		$this -> db -> where("user_id", $user_id);
		$list = $this -> db -> get($this -> table_name) -> result();
		return $list;
	}

	function find_all_by_round_pos_and_user($round_id, $pos, $user_id) {
		$this -> db -> where("round_id", $round_id);
		$this -> db -> where("user_id", $user_id);
		$this -> db -> where("pos", $pos);
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
