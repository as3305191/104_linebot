<?php
class Bj_tab_bet_dao extends MY_Model {

	function __construct() {
		parent::__construct();

		// initialize table name
		parent::set_table_name('bj_tab_bet');

		$this -> alias_map = array(

		);
	}

	function do_player_bet($tab_id, $pos, $pos_sub, $user_id, $bet_amt, $hall_id) {
		$this -> load -> model('Bj_tab_round_dao', 'nn_tab_round_dao');
		$this -> load -> model('Bj_tab_dao', 'nn_tab_dao');
		$this -> load -> model('Bj_tab_status_dao', 'nn_tab_status_dao');
		$this -> load -> model('Wallet_tx_dao', 'wtx_dao');
		$this -> load -> model('Wallet_tx_bkc_dao', 'wtx_bkc_dao');

		$ret = array();
		$ret['success'] = TRUE;

		$a_round_list = $this -> nn_tab_round_dao -> find_unfinished($tab_id, $hall_id);
		$item = NULL;
		$a_round = NULL;

		$status_item = $this -> nn_tab_status_dao -> get_status($tab_id, $hall_id);
		if(count($a_round_list) > 0) {
			$a_round = $a_round_list[0];

			$ret['round'] = $a_round;

			if($status_item -> status != 0) {
				$ret['error_msg'] = "未在下注狀態";
			} else {
				$prop = "pos_{$pos}_user";
				if($status_item -> $prop == $user_id) {
					// 在此位置
					$pos_list = $this -> find_all_by_round_and_pos($a_round -> id, $pos, $pos_sub);
					// error_log(json_encode($pos_list));
					if(count($pos_list) == 0) {
						// 沒有，就新增
						$last_id = $this -> insert(array(
							'round_id' => $a_round -> id,
							'user_id' => $user_id,
							'tab_id' => $tab_id,
							'pos' => $pos,
							'pos_sub' => $pos_sub,
							'hall_id' => $hall_id,
							'bet_amt' => $bet_amt,
						));
						$item = $this -> find_by_id($last_id);

						// 扣款
						$tx = array();
						$tx['corp_id'] = 1;
						$tx['bj_bet_id'] = $item -> id;
						$tx['user_id'] = $item -> user_id;
						$tx['amt'] = -$item -> bet_amt;
						$tx['type_id'] = 132; // bj 下注
						$tx['brief'] = "BJ 下注 {$item->bet_amt} ";
						if($hall_id < 0) {
							$this -> wtx_bkc_dao -> insert($tx);
						} else {
							$this -> wtx_dao -> insert($tx);
						}

					} else {
						// 有，更新
						$obj = $pos_list[0];
						// update
						$this -> update(array(
							'bet_amt' => $bet_amt
		 				), $obj -> id);

						// 移除前次扣款
						if($hall_id < 0) {
							$this -> wtx_bkc_dao -> delete_by_bj_bet_id_and_type($obj->id, 132);
						} else {
							$this -> wtx_dao -> delete_by_bj_bet_id_and_type($obj->id, 132);
						}

						$item = $this -> find_by_id($obj -> id);

						// 扣款
						$tx = array();
						$tx['corp_id'] = 1;
						$tx['bj_bet_id'] = $item -> id;
						$tx['user_id'] = $item -> user_id;
						$tx['amt'] = -$item -> bet_amt;
						$tx['type_id'] = 132; // bj 下注
						$tx['brief'] = "BJ 下注 {$item->bet_amt} ";
						if($hall_id < 0) {
							$this -> wtx_bkc_dao -> insert($tx);
						} else {
							$this -> wtx_dao -> insert($tx);
						}

					}

				} else {
					$ret['error_msg'] = "該位置已有其他玩家";
				}
			}

		} else {
			$ret['error_msg'] = "尚未開局";
		}

		$ret['bet'] = $item;
		// $ret['round'] = $a_round;
		return $ret;
	}

	function do_next_card($tab_id, $pos, $pos_sub, $user_id, $hall_id) {

		$this -> load -> model('Bj_tab_round_dao', 'nn_tab_round_dao');

		$ret = array();
		$ret['success'] = TRUE;

		$a_round_list = $this -> nn_tab_round_dao -> find_unfinished($tab_id, $hall_id);
		$item = NULL;
		$a_round = NULL;

		if(count($a_round_list) > 0) {
			$a_round = $a_round_list[0];

			$list = $this -> find_all_by_round_pos_and_user($a_round -> id, $pos, $pos_sub, $user_id);
			if(count($list) == 0) {
				$ret['error_msg'] = "尚未下注";
			} else {
				$obj = $list[0];
				if($obj -> is_next == 1) {
					$item = $this -> player_get_card($a_round -> id, $obj -> id);
				} else {
					$ret["error_msg"] = "並非下一個";
				}
			}
		} else {
			$ret['error_msg'] = "尚未開局";
		}

		$ret['bet'] = $item;
		// $ret['round'] = $a_round;
		return $ret;
	}

	function do_double($tab_id, $pos, $pos_sub, $user_id, $hall_id) {

		$this -> load -> model('Bj_tab_round_dao', 'nn_tab_round_dao');

		$ret = array();
		$ret['success'] = TRUE;

		$a_round_list = $this -> nn_tab_round_dao -> find_unfinished($tab_id, $hall_id);
		$item = NULL;
		$a_round = NULL;

		if(count($a_round_list) > 0) {
			$a_round = $a_round_list[0];

			$list = $this -> find_all_by_round_pos_and_user($a_round -> id, $pos, $pos_sub, $user_id);
			if(count($list) == 0) {
				$ret['error_msg'] = "尚未下注";
			} else {
				$obj = $list[0];
				if($obj -> is_double == 0) {
					$this -> update(array(
						"is_double" => 1,
						'is_next_time' => time()
					), $obj -> id);

					// 發一張卡
					$this -> do_next_card($tab_id, $pos, $pos_sub, $user_id, $hall_id);

					// 發完卡後結束這一局
					$this -> end_player_game($tab_id, $hall_id, $pos, $pos_sub);
				} else {
					$ret["error_msg"] = "已設定double過了";
				}
				$item = $this -> find_by_id($obj -> id);
			}
		} else {
			$ret['error_msg'] = "尚未開局";
		}

		$ret['bet'] = $item;
		// $ret['round'] = $a_round;
		return $ret;
	}

	function do_surrender($tab_id, $pos, $pos_sub, $user_id, $hall_id) {

		$this -> load -> model('Bj_tab_round_dao', 'nn_tab_round_dao');

		$ret = array();
		$ret['success'] = TRUE;

		$a_round_list = $this -> nn_tab_round_dao -> find_unfinished($tab_id, $hall_id);
		$item = NULL;
		$a_round = NULL;

		if(count($a_round_list) > 0) {
			$a_round = $a_round_list[0];

			$list = $this -> find_all_by_round_pos_and_user($a_round -> id, $pos, $pos_sub, $user_id);
			if(count($list) == 0) {
				$ret['error_msg'] = "尚未下注";
			} else {
				$obj = $list[0];
				if($obj -> is_surrender == 0) {
					$this -> update(array(
						"is_surrender" => 1,
						'is_next_time' => time()
					), $obj -> id);

					// 結束
					$this -> end_player_game($tab_id, $hall_id, $pos, $pos_sub);
				} else {
					$ret["error_msg"] = "已設定投降過了";
				}
				$item = $this -> find_by_id($obj -> id);
			}
		} else {
			$ret['error_msg'] = "尚未開局";
		}

		$ret['bet'] = $item;
		// $ret['round'] = $a_round;
		return $ret;
	}

	function do_insurence($tab_id, $pos, $pos_sub, $user_id, $hall_id) {

		$this -> load -> model('Bj_tab_round_dao', 'nn_tab_round_dao');

		$ret = array();
		$ret['success'] = TRUE;

		$a_round_list = $this -> nn_tab_round_dao -> find_unfinished($tab_id, $hall_id);
		$item = NULL;
		$a_round = NULL;

		if(count($a_round_list) > 0) {
			$a_round = $a_round_list[0];

			$list = $this -> find_all_by_round_pos_and_user($a_round -> id, $pos, $pos_sub, $user_id);
			if(count($list) == 0) {
				$ret['error_msg'] = "尚未下注";
			} else {
				$obj = $list[0];
				$a_val = -1;
				if($obj -> is_insurence == 0) {
					if($obj -> card_1 > -1) {
						$card = $obj -> card_1;
						$a_val = (($card + 1) % 13);
						if($a_val == 1) {
							$this -> update(array(
								"is_insurence" => 1,
								'is_next_time' => time()
							), $obj -> id);
						} else {
							$ret["error_msg"] = "第一張牌不是A";
						}
					} else {
						$ret["error_msg"] = "沒有第一張牌";
					}
				} else {
					$ret["error_msg"] = "已設定投降過了";
				}
				$item = $this -> find_by_id($obj -> id);
			}
		} else {
			$ret['error_msg'] = "尚未開局";
		}

		$ret['bet'] = $item;
		// $ret['round'] = $a_round;
		return $ret;
	}

	function do_split($tab_id, $pos, $pos_sub, $user_id, $hall_id) {

		$this -> load -> model('Bj_tab_round_dao', 'nn_tab_round_dao');
		$this -> load -> model('Wallet_tx_dao', 'wtx_dao');
		$this -> load -> model('Wallet_tx_bkc_dao', 'wtx_bkc_dao');

		$ret = array();
		$ret['success'] = TRUE;

		$a_round_list = $this -> nn_tab_round_dao -> find_unfinished($tab_id, $hall_id);
		$item = NULL;
		$a_round = NULL;

		if(count($a_round_list) > 0) {
			$a_round = $a_round_list[0];

			$list = $this -> find_all_by_round_pos_and_user($a_round -> id, $pos, $pos_sub, $user_id);
			if(count($list) == 0) {
				$ret['error_msg'] = "尚未下注";
			} else {
				$obj = $list[0];
				$a_val = -1;
				if($obj -> is_insurence == 0) {
					if($obj -> card_1 > -1 && $obj -> card_2 > -1) {
						$card_1 = $obj -> card_1;
						$card_2 = $obj -> card_1;
						$val_1 = (($card_1 + 1) % 13);
						$val_2 = (($card_2 + 1) % 13);

						if($val_1 == 0 || $val_1 > 10) {
							$val_1 = 10;
						}
						if($val_2 == 0 || $val_2 > 10) {
							$val_2 = 10;
						}

						if($val_1 == $val_2) {
							$split_list = $this -> find_all_split_by_round_and_pos($a_round -> id, $pos);
							if(count($split_list) < 4) {
								$sum_amt = 0;
								if($hall_id < 0) {
									$sum_amt = $this -> wtx_bkc_dao -> get_sum_amt($user_id);
								} else {
									$sum_amt = $this -> wtx_dao -> get_sum_amt($user_id);
								}

								if($obj -> bet_amt > $sum_amt) {
									$ret["error_msg"] = "餘額不足";
								} else {
									$this -> update(array(
										"is_split" => 1,
										"card_2" => -1, // remvoe card 2
										'is_next_time' => time()
									), $obj -> id);
									$this -> player_get_card($a_round -> id, $obj -> id);

									$split_list = $this -> find_all_split_by_round_and_pos($a_round -> id, $pos);

									// create another
									$last_id = $this -> insert(array(
										'round_id' => $a_round -> id,
										'user_id' => $obj -> user_id,
										'tab_id' => $obj -> tab_id,
										'pos' => $obj -> pos,
										'pos_sub' => count($split_list),
										'hall_id' => $obj -> hall_id,
										'bet_amt' => $obj -> bet_amt,
										'is_split' => 1,
										'card_1' => $obj -> card_2, // add card 1
									));
									$this -> player_get_card($a_round -> id, $last_id);

									$a_bet = $this -> find_by_id($last_id);
									// 扣款
									$tx = array();
									$tx['bj_bet_id'] = $last_id;
									$tx['user_id'] = $a_bet -> user_id;
									$tx['amt'] = -$a_bet -> bet_amt;
									$tx['type_id'] = 130;
									$tx['brief'] = "BJ 下注 {$a_bet->bet_amt} ";
									if($hall_id < 0) {
										$this -> wtx_bkc_dao -> insert($tx);
									} else {
										$this -> wtx_dao -> insert($tx);
									}
								}
							} else {
								$ret["error_msg"] = "最多分4局";
							}

						} else {
							$ret["error_msg"] = "第1張牌與第2張牌沒有同點數";
						}
					} else {
						$ret["error_msg"] = "沒有第1張牌與第2張牌";
					}
				} else {
					$ret["error_msg"] = "已設定投降過了";
				}
				$item = $this -> find_by_id($obj -> id);
			}
		} else {
			$ret['error_msg'] = "尚未開局";
		}

		$ret['bet'] = $item;
		// $ret['round'] = $a_round;
		return $ret;
	}

	function end_player_game($tab_id, $hall_id, $pos, $pos_sub) {

		$this -> load -> model('Bj_tab_round_dao', 'nn_tab_round_dao');

		$ret = array();
		$ret['success'] = TRUE;

		$a_round_list = $this -> nn_tab_round_dao -> find_unfinished($tab_id, $hall_id);
		$a_round = NULL;

		if(count($a_round_list) > 0) {
			$a_round = $a_round_list[0];
			$all_bets = $this -> nn_tab_bet_dao -> find_all_by_round($a_round -> id);

			$is_end = FALSE;
			$cnt = 0;
			$has_next = FALSE;
			foreach($all_bets as $a_bet) {
				$cnt++;

				if($has_next) {
					$has_next = FALSE;
					// 設定下一個
					$this -> update(array(
						'is_next' => 1,
						'is_next_time' => time(),
					), $a_bet -> id);
				}

				if($a_bet -> is_next == 1 && $pos == $a_bet -> pos && $pos_sub == $a_bet -> pos_sub) { // 正確
					// 看是不是最後一個

					if($cnt == count($all_bets)) {
						// 最後一個
						$is_end = TRUE;
					} else {
						// 不是就設定下一個
						$has_next = TRUE;
					}
					// 清除自己
					$this -> update(array(
						'is_next' => 0
					), $a_bet -> id);
				} else {
					$ret['error_msg'] = "並非下一個";
				}
			}

			if($is_end) {
				// 莊家補牌或是開牌
				if($a_round -> card_val < 17) { // 小於 17一律補牌
					// 補牌
					$a_round = $this -> banker_get_card($a_round -> id);
				}

				// 標記玩家完成
				$this -> nn_tab_round_dao -> update(array(
					'all_done' => 1
				), $a_round -> id);
			}

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
		// $card = 0; // always ace

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

		$val = $this -> bj_val($card_arr);

		$this -> update(array(
			"card_val" => $val,
			'is_next_time' => time()
		), $obj -> id);

		$obj = $this -> find_by_id($obj -> id);

		return $obj;
	}

	function banker_get_card($round_id) {
		$this -> load -> model('Bj_tab_round_dao', 'nn_tab_round_dao');

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
		$val = $this -> bj_val($card_arr);

		// 更新分數
		$this -> nn_tab_round_dao -> update(array(
			"card_val" => $val
		), $a_round -> id);

		// reload
		$a_round = $this -> nn_tab_round_dao -> find_by_id($a_round -> id);
		return $a_round;
	}


	function test() {
		echo "test\r\n";
		$val = $this -> bj_val(array(
			38,
			1
		));
		echo "val: $val \r\n";
	}

	function bj_val($arr) {
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
			if($a_val == 0 && $card > 0) { // K
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
		$this -> load -> model('Bj_tab_round_dao', 'nn_tab_round_dao');

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
		$this -> db -> order_by("pos_sub", "asc");
		$list = $this -> db -> get($this -> table_name) -> result();
		return $list;
	}

	function find_all_by_round_and_user($round_id, $user_id) {
		$this -> db -> where("round_id", $round_id);
		$this -> db -> where("user_id", $user_id);
		$list = $this -> db -> get($this -> table_name) -> result();
		return $list;
	}

	function find_all_by_round_pos_and_user($round_id, $pos, $pos_sub, $user_id) {
		$this -> db -> where("round_id", $round_id);
		$this -> db -> where("user_id", $user_id);
		$this -> db -> where("pos", $pos);
		$this -> db -> where("pos_sub", $pos_sub);
		$list = $this -> db -> get($this -> table_name) -> result();

		// error_log($this -> db -> last_query());

		return $list;
	}

	function find_all_by_round_and_pos($round_id, $pos, $pos_sub) {
		$this -> db -> where("round_id", $round_id);
		$this -> db -> where("pos", $pos);
		$this -> db -> where("pos_sub", $pos_sub);
		$list = $this -> db -> get($this -> table_name) -> result();
		return $list;
	}

	function find_all_split_by_round_and_pos($round_id, $pos) {
		$this -> db -> where("round_id", $round_id);
		$this -> db -> where("pos", $pos);
		$this -> db -> where("is_split", 1);
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
