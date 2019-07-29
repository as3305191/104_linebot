<?php
class Baccarat_test extends MY_Base_Controller {

	function __construct() {
		parent::__construct();
		$this -> load -> model('Baccarat_tab_dao', 'bc_dao');
		$this -> load -> model('Users_dao', 'users_dao');
		$this -> load -> model('Images_dao', 'img_dao');
		$this -> load -> model('Corp_dao', 'corp_dao');

		$this -> load -> model('Baccarat_tab_round_dao', 'btr_dao');
		$this -> load -> model('Baccarat_tab_round_detail_dao', 'btrd_dao');
		$this -> load -> model('Baccarat_tab_round_bet_dao', 'btrb_dao');
		$this -> load -> model('Baccarat_tab_tx_dao', 'tab_tx_dao');
		$this -> load -> model('Baccarat_tab_safe_detail_dao', 'safe_dao');
		$this -> load -> model('Baccarat_tab_record_dao', 'trec_dao');

		$this -> load -> model('Wallet_tx_dao', 'wtx_dao');
		$this -> load -> model('Lottery_tx_dao', 'ltx_dao');
	}

	public function do_test() {
		echo "test";
		$res = array();

		$a_detail = $this -> btrd_dao -> find_by_id(497886);

		// echo json_encode($a_detail)
		// return ;
		$tab_id = 18;
		$a_round_id = 12970;
		$a_round = $this -> btr_dao -> find_by_id($a_round_id);
		$ret = $this -> check_detail($a_detail, $res, $tab_id, $a_round_id);
		echo json_encode($ret) . '<br/>';
		echo json_encode($res) . '<br/>';
		return;
		if($ret['result'] < 0) {
			// secure once
			$next_winner = ($a_detail -> winner == 1 ? 2 : 1);
			$safe = $this -> safe_dao -> random_one($next_winner);
			// update detail
			$sdu = array();
			$sdu['player_c_0'] = $safe ->  player_c_0;
			$sdu['player_c_1'] = $safe ->  player_c_1;
			$sdu['player_c_2'] = $safe ->  player_c_2;
			$sdu['player_val'] = $safe ->  player_val;
			$sdu['banker_c_0'] = $safe ->  banker_c_0;
			$sdu['banker_c_1'] = $safe ->  banker_c_1;
			$sdu['banker_c_2'] = $safe ->  banker_c_2;
			$sdu['banker_val'] = $safe ->  banker_val;
			$sdu['winner'] = $safe ->  winner;
			$sdu['winner_type'] = $safe ->  winner_type;
			$sdu['is_changed'] = 1;
			$this -> btrd_dao -> update($sdu, $a_detail -> id);
			// find again
			$a_detail = $this -> btrd_dao -> find_by_id($a_detail -> id);
			$ret = $this -> check_detail($a_detail, $res, $tab_id, $a_round_id);

			$res[$a_round_id]['result'] = $ret['result'];
			echo json_encode($ret) . '<br/>';
			echo json_encode($res) . '<br/>';
		}

		// 最多需要底二次
		if($ret['result'] < 0) {
			// secure once
			$next_winner = ($a_detail -> winner == 1 ? 2 : 1);
			$safe = $this -> safe_dao -> random_one($next_winner);

			// update detail
			$sdu = array();
			$sdu['player_c_0'] = $safe ->  player_c_0;
			$sdu['player_c_1'] = $safe ->  player_c_1;
			$sdu['player_c_2'] = $safe ->  player_c_2;
			$sdu['player_val'] = $safe ->  player_val;
			$sdu['banker_c_0'] = $safe ->  banker_c_0;
			$sdu['banker_c_1'] = $safe ->  banker_c_1;
			$sdu['banker_c_2'] = $safe ->  banker_c_2;
			$sdu['banker_val'] = $safe ->  banker_val;
			$sdu['winner'] = $safe ->  winner;
			$sdu['winner_type'] = $safe ->  winner_type;
			$sdu['is_changed'] = 2;
			$this -> btrd_dao -> update($sdu, $a_detail -> id);
			// find again
			$a_detail = $this -> btrd_dao -> find_by_id($a_detail -> id);
			$ret = $this -> check_detail($a_detail, $res, $tab_id, $a_round_id);

			$res[$a_round_id]['result'] = $ret['result'];
			echo json_encode($ret) . '<br/>';
			echo json_encode($res) . '<br/>';
		}

		// do save 最後就存擋了
		$ret = $this -> check_detail($a_detail, $res, $tab_id, $a_round_id, FALSE);
	}

	public function gen_round() {
		$res = array();
		$tb_list = $this -> bc_dao -> find_all();
		//$res['list'] = $tb_list;
		foreach($tb_list as $tb) {
			if($tb -> status == 1) {
				// stop it
				$res['stop'][] = $tb -> id;
			} else if($tb -> status == 0) {
				if($tb -> id == 1) {

				}
				// $res['gen_round_by_tab_id'][] = $tb -> id;
				// try generate round first
				$this -> gen_round_by_tab_id($tb -> id);

				// do opening job
				// if($tb -> id != 7) {
				// 	$this -> game_tick($tb -> id, $res);
				// }
				$this -> game_tick($tb -> id, $res);

			} else {
				$res['impossible'][] = 1;
			}
		}

		$res['success'] = TRUE;
		$this -> to_json($res);
	}

	function game_tick($tab_id, $res) {
		echo "start<br/>";
		if(!is_array($res)){
			$res = array();
		}
 		$show = array();
		$paly_seconds = PLAY_SECONDS; // 等待下注 -> 停止下注
		$opening_seconds = OPENING_SECONDS; // 停止下注 -> 開牌
		$open_seconds = OPEN_SECONDS; // 開牌 -> 派彩
		$bonus_seconds = BONUS_SECONDS; // 派彩 -> 結束

		// current round
		$a_round = $this -> btr_dao -> find_current_round($tab_id);
		$a_round_id = $a_round -> id;
		$res[$a_round_id] = array();
		if(!empty($a_round)) {
			$a_detail = $this -> btrd_dao -> find_current_round_detail($a_round -> id);
			if(empty($a_detail)) {
				// no detail then end game
				$u_data = array();
				$u_data['status'] = 2; // finish
				$u_data['finish_time'] = date('Y-m-d H:i:s'); // finish time
				$u_data['finish_time_unix'] = time(); // finish time
				$this -> btr_dao -> update($u_data, $a_round -> id);
				$res[$a_round_id]['msg'] = "finished...";
			} else {
				// updae detail
				$u_data = array();
				if($a_detail -> status == 0) {
					$u_data['status'] = 1;
					$u_data['start_time'] = date('Y-m-d H:i:s');
					$u_data['start_time_unix'] = time();
					$show['detail_id'] = $a_detail-> id;

					// update round
					$ru_data = array();
					$ru_data['status'] = 1; // 開始下注
					$ru_data['current_detail_pos'] = $a_detail -> pos; // current_detail_pos
					$ru_data['start_time'] = date('Y-m-d H:i:s'); // start time

					$this -> btr_dao -> update($ru_data, $a_round -> id);
					$res[$a_round_id]['status'] = 0;
				} else if($a_detail -> status == 1) { // 開始下注
					$diff = (strtotime(date('Y-m-d H:i:s')) - strtotime($a_detail -> start_time));
					if($diff > $paly_seconds) { // play seconds
						$u_data['opening_time'] = date('Y-m-d H:i:s');
						$u_data['opening_time_unix'] = time();
						$u_data['status'] = 2;
						$show['detail_id'] = $a_detail-> id;
					} else {
						// $res["$a_detail->id 下注中"][] = "$diff 秒";
					}
					$res[$a_round_id]['status'] = 1;

				} else if($a_detail -> status == 2) {
					$diff = (strtotime(date('Y-m-d H:i:s')) - strtotime($a_detail -> opening_time));
					if($diff > $opening_seconds) { // 停止下注 -> 開牌
						$u_data['open_time'] = date('Y-m-d H:i:s');
						$u_data['open_time_unix'] = time();
						$u_data['status'] = 3; //開牌
						$show['detail_id'] = $a_detail-> id;
					} else {
						// $res["$a_detail->id 停止下注"][] = "$diff 秒";
						// 驗證彩池
						$res[$a_round_id]['is_checked'] = $a_detail -> is_checked;
						if($a_detail -> is_checked == 0) {
							// mark as checked
							$this -> btrd_dao -> update(array('is_checked' => 1 ), $a_detail -> id);
							// $res["$a_detail->id checked..........."];
							$ret = $this -> check_detail($a_detail, $res, $tab_id, $a_round_id);
							echo json_encode($ret) . '<br/>';
							echo json_encode($res) . '<br/>';
							if($ret['result'] < 0) {
								// secure once
								$safe = $this -> safe_dao -> random_one();
								// update detail
								$sdu = array();
								$sdu['player_c_0'] = $safe ->  player_c_0;
								$sdu['player_c_1'] = $safe ->  player_c_1;
								$sdu['player_c_2'] = $safe ->  player_c_2;
								$sdu['player_val'] = $safe ->  player_val;
								$sdu['banker_c_0'] = $safe ->  banker_c_0;
								$sdu['banker_c_1'] = $safe ->  banker_c_1;
								$sdu['banker_c_2'] = $safe ->  banker_c_2;
								$sdu['banker_val'] = $safe ->  banker_val;
								$sdu['winner'] = $safe ->  winner;
								$sdu['winner_type'] = $safe ->  winner_type;
								$sdu['is_changed'] = 1;
								$this -> btrd_dao -> update($sdu, $a_detail -> id);
								// find again
								$a_detail = $this -> btrd_dao -> find_current_round_detail($a_round -> id);
								$ret = $this -> check_detail($a_detail, $res, $tab_id, $a_round_id);

								$res[$a_round_id]['result'] = $ret['result'];
								echo json_encode($ret) . '<br/>';
								echo json_encode($res) . '<br/>';
							}

							// 最多需要三次
							if($ret['result'] < 0) {
								// secure once
								$safe = $this -> safe_dao -> random_one();
								// update detail
								$sdu = array();
								$sdu['player_c_0'] = $safe ->  player_c_0;
								$sdu['player_c_1'] = $safe ->  player_c_1;
								$sdu['player_c_2'] = $safe ->  player_c_2;
								$sdu['player_val'] = $safe ->  player_val;
								$sdu['banker_c_0'] = $safe ->  banker_c_0;
								$sdu['banker_c_1'] = $safe ->  banker_c_1;
								$sdu['banker_c_2'] = $safe ->  banker_c_2;
								$sdu['banker_val'] = $safe ->  banker_val;
								$sdu['winner'] = $safe ->  winner;
								$sdu['winner_type'] = $safe ->  winner_type;
								$sdu['is_changed'] = 1;
								$this -> btrd_dao -> update($sdu, $a_detail -> id);
								// find again
								$a_detail = $this -> btrd_dao -> find_current_round_detail($a_round -> id);
								$ret = $this -> check_detail($a_detail, $res, $tab_id, $a_round_id);

								$res[$a_round_id]['result'] = $ret['result'];
								echo json_encode($ret) . '<br/>';
								echo json_encode($res) . '<br/>';
							}

							// do save
							$ret = $this -> check_detail($a_detail, $res, $tab_id, $a_round_id, TRUE);
						}
					}
					$res[$a_round_id]['status'] = 2;
				} else if($a_detail -> status == 3) { // 開牌中... 彩池機制
					$diff = (strtotime(date('Y-m-d H:i:s')) - strtotime($a_detail -> open_time));
					if($diff > $open_seconds) { // 開牌結束 -> 派彩
						$u_data['bonus_time'] = date('Y-m-d H:i:s');
						$u_data['bonus_time_unix'] = time();
						$u_data['status'] = 4;
						$show['detail_id'] = $a_detail-> id;
					} else {

						// $res["$a_detail->id 開牌中"][] = "$diff 秒";
					}
					$res[$a_round_id]['status'] = 3;

				} else if($a_detail -> status == 4) {
					$diff = (strtotime(date('Y-m-d H:i:s')) - strtotime($a_detail -> bonus_time));
					if($diff > $bonus_seconds) { // 開牌 -> 結束
						$u_data['finish_time'] = date('Y-m-d H:i:s');
						$u_data['finish_time_unix'] = time();
						$u_data['status'] = 5;
						$show['detail_id'] = $a_detail-> id;

						// check end of last detail
						if($a_detail -> pos == $a_round -> round_details) {
							$ru_data = array();
							$ru_data['status'] = 2; // 結束
							$ru_data['finish_time'] = date('Y-m-d H:i:s'); // start time
							$this -> btr_dao -> update($ru_data, $a_round -> id);
						}
						$res[$a_round_id]['status'] = 4;

					} else {
						// $res["$a_detail->id 派彩中"][] = "$diff 秒";
					}
				} else {
					// finish
				}
				// echo json_encode(array_merge($u_data, $show));
				if(count($u_data) > 0) {
					$this -> btrd_dao -> update($u_data, $a_detail -> id);
				}
			}

		}

		// update pool val
		$pool_val = $this -> tab_tx_dao -> sum_by_tab_id($tab_id);
		$this -> bc_dao -> update(array('pool_val' => $pool_val), $tab_id);
	}

	function check_detail($a_detail, &$res, $tab_id, $a_round_id, $is_save = FALSE) {
		$ret = array();

		$winner = $a_detail -> winner;
		$winner_type = $a_detail -> winner_type;

		// $tab_amt = $this -> tab_tx_dao -> sum_by_tab_id($tab_id); // 彩池金額
		$tab_amt = 1313; // 彩池金額
		$res[$a_round_id]['tab_amt'] = $tab_amt;

		// list all bets
		$bet_list = $this -> btrb_dao -> list_by_detail_id($a_detail -> id);
		$total_bet = 0;
		$total_win = 0;
		$result = $tab_amt;
		$result_bare = 0;
		foreach($bet_list as $a_bet) {
			$user_win = 0;

			$bu_data = array();

			// reset win first
			$rsw = array();
			for($i = 0 ; $i < 5 ; $i++) {
				$_col = "win_$i";
				$rsw[$_col] = 0;
			}
			$this -> btrb_dao -> update($rsw, $a_bet -> id);

			// find total bet
			$total_bet += $a_bet -> total_bet;

			// check tie
			if($a_bet -> bet_0 > 0) {
				if($winner == 0 ) {
					$win_amt = get_bet_multiply($winner) * $a_bet -> bet_0;
					$total_win += ($win_amt + $a_bet -> bet_0);
					$user_win += ($win_amt + $a_bet -> bet_0);
					$bu_data["win_0"] = $win_amt;
				}
			}

			// check banker
			if($a_bet -> bet_1 > 0) {
				if($winner == 0 ) { // tie refound
					$total_win += ($a_bet -> bet_1);
					$user_win += ($a_bet -> bet_1);
					$bu_data["win_1"] = 0;
				} else if($winner == 1){ // win
					$win_amt = get_bet_multiply($winner) * $a_bet -> bet_1;
					$total_win += ($win_amt + $a_bet -> bet_1);
					$user_win += ($win_amt + $a_bet -> bet_1);
					$bu_data["win_1"] = $win_amt;
				}
			}

			// check player
			if($a_bet -> bet_2 > 0) {
				if($winner == 0 ) { // tie refound
					$total_win += ($a_bet -> bet_2);
					$user_win += ($a_bet -> bet_2);
					$bu_data["win_2"] = 0;
				} else if($winner == 2) {
					$win_amt = get_bet_multiply($winner) * $a_bet -> bet_2;
					$total_win += ($win_amt + $a_bet -> bet_2);
					$user_win += ($win_amt + $a_bet -> bet_2);
					$bu_data["win_2"] = $win_amt;
				}
			}

			// check pair
			if($winner_type >= 3) { // 3,4,6
				if($winner_type == 3 || $winner_type == 6) {
					if($a_bet -> bet_3 > 0) {
						$win_amt = get_bet_multiply(3) * $a_bet -> bet_3;
						$total_win += ($win_amt + $a_bet -> bet_3);
						$user_win += ($win_amt + $a_bet -> bet_3);
						$bu_data['win_3'] = $win_amt;
					}
				}
				if($winner_type == 4 || $winner_type == 6) {
					if($a_bet -> bet_4 > 0) {
						$win_amt = get_bet_multiply(4) * $a_bet -> bet_4;
						$total_win += ($win_amt + $a_bet -> bet_4);
						$user_win += ($win_amt + $a_bet -> bet_4);
						$bu_data['win_4'] = $win_amt;
					}
				}
			}

			if(!empty($bu_data)) {
				$this -> btrb_dao -> update($bu_data, $a_bet -> id);
			}

			$result += ($total_bet - $total_win); // 彩池剩餘總金額
			$a_bet -> result_bare = ($total_bet - $total_win);// 此局彩池盈餘(此局輸贏)
			$result_bare += ($a_bet -> result_bare); //累計此局彩池盈餘

			if($is_save) { // 最後的結果,如果有存擋就是代表彩池一定是贏錢
				$w_amt = 0; // 如果大於零就是代表有贏錢
				$this_bet = $this -> btrb_dao -> find_by_id($a_bet -> id);
				for($i = 0 ; $i < 5 ; $i++) {
					$_bet_col = "bet_$i";
					$_win_col = "win_$i";
					if($this_bet -> $_bet_col  > 0) {
						// 有下注
						$_is_win = ($this_bet -> $_win_col > 0);
						$sb = array();
						$sb['detail_id'] = $a_detail -> id;
						$sb['bet_type'] = $i;
						$sb['is_win'] = $i;
						$sb['bet_amt'] = $this_bet -> $_bet_col;

						$pay_bet = -($this_bet -> $_bet_col);
						if($i == 1 || $i == 2) { // banker or player
							if($winner == 0) { // tie refund
								$pay_bet = 0;
							}
						}

						$w_amt = ($_is_win ? $this_bet -> $_win_col : $pay_bet);
						$sb['win_amt'] = $w_amt;
						$sb_id = $this -> trec_dao -> insert($sb);

						// save to wallet
						$wtx = array();
						$wtx['user_id'] = $a_bet -> user_id;
						$wtx['baccarat_tab_record_id'] = $sb_id;
						$wtx['type_id'] = 19; //獎金輸入
						$wtx['amt'] = $w_amt;
						$note = "";
						if($w_amt != 0) {
							if($_is_win ) {
								$note = "獲得百家樂獎金 $w_amt 元";
							}else {
								$note = "百家樂交易 $w_amt 元";
							}
							$wtx['note'] = $note;
							$this -> wtx_dao -> insert($wtx);
						} else {
							// nothing to do
						}
					}
				}


			} // end of save
		}

		// save back to pool
		if($is_save) {
			if($result_bare != 0) { // 這一局有輸贏
				$ttx = array();
				$ttx['tab_id'] = $tab_id;
				$ttx['round_id'] = $a_detail -> round_id;
				$ttx['detail_id'] = $a_detail -> id;
				$ttx['tx_amt_origin'] = $result_bare;

				$result_amt = ($result_bare > 0 ? (floatval($result_bare) * 0.95) : $result_bare);
				$ttx['tx_amt'] = $result_amt;
				$amt_share = $result_bare - $result_amt;
				$ttx['tx_amt_share'] = $amt_share;
				$tab_tx_id = $this -> tab_tx_dao -> insert($ttx);

				if($amt_share > 0) {
					// share to each bet
					foreach($bet_list as $a_bet) {
						$this_amt_share = floatval($amt_share) * floatval($a_bet -> total_bet) / floatval($total_bet);

						$ttx_u = array();
						$tx_amt_intro = floatval($this_amt_share) * 0.2;
						$tx_amt_manager = floatval($this_amt_share) * 0.2;
						$tx_amt_sh = floatval($this_amt_share) * 0.2;
						$tx_amt_lottery = floatval($this_amt_share) * 0.2;
						$tx_amt_player = floatval($this_amt_share) * 0.2;
						$ttx_u['tx_amt_intro'] = $tx_amt_intro;
						$ttx_u['tx_amt_manager'] = $tx_amt_manager;
						$ttx_u['tx_amt_sh'] = $tx_amt_sh;
						$ttx_u['tx_amt_lottery'] = $tx_amt_lottery;
						$ttx_u['tx_amt_player'] = $a_bet -> result_bare < 0 ? $tx_amt_player : 0; // 輸錢分佣金
						$this -> tab_tx_dao -> update($ttx_u, $tab_tx_id);

						$l_user = $this -> users_dao -> find_by_id($a_bet -> user_id);

						// player
						$tx = array();
						$tx['baccarat_tab_tx_id'] = $tab_tx_id;
						$tx['corp_id'] = $l_user -> corp_id; // corp id
						$tx['user_id'] = $l_user -> id;

						$tx['amt'] = $tx_amt_player;
						$tx['type_id'] = 20; // 個人分潤
						$tx['brief'] = "會員 $l_user->account 百家樂下注，利潤 $tx_amt_player 點";
						$this -> wtx_dao -> insert($tx);

						// intro
						$tx = array();
						$tx['baccarat_tab_tx_id'] = $tab_tx_id;
						$tx['corp_id'] = $l_user -> corp_id; // corp id
						$tx['user_id'] = $l_user -> intro_id;

						$tx['amt'] = $tx_amt_intro;
						$tx['type_id'] = 5; // 會員收入
						$tx['brief'] = "會員 $l_user->account 百家樂下注，利潤 $tx_amt_intro 點";
						$this -> wtx_dao -> insert($tx);

						// manager
						$tx = array();
						$tx['baccarat_tab_tx_id'] = $tab_tx_id;
						$tx['corp_id'] = $l_user -> corp_id; // corp id
						$tx['user_id'] = $l_user -> manager_id;

						$tx['amt'] = $tx_amt_manager;
						$tx['type_id'] = 6; // 經理人收入
						$tx['brief'] = "會員 $l_user->account 百家樂下注，利潤 $tx_amt_manager 點";
						$this -> wtx_dao -> insert($tx);

						// share holder
						$tx = array();
						$tx['baccarat_tab_tx_id'] = $tab_tx_id;
						$tx['corp_id'] = $l_user -> corp_id; // corp id
						$tx['user_id'] = $l_user -> shareholder_id;;
						$tx['amt'] = $tx_amt_sh;
						$tx['type_id'] = 7; // 股東收入
						$tx['brief'] = "會員 $l_user->account 百家樂下注，利潤 $tx_amt_sh 點";
						$this -> wtx_dao -> insert($tx);

						// lottery
						$tx = array();
						$tx['baccarat_tab_tx_id'] = $tab_tx_id;
						$tx['corp_id'] = $l_user -> corp_id; // corp id
						$tx['amt'] = $tx_amt_lottery;
						$tx['brief'] = "會員 $l_user->account 百家樂下注，利潤 $tx_amt_lottery 點";
						$this -> ltx_dao -> insert($tx);
					}
				}
			}
		}

		$ret['tab_amt'] = $tab_amt;
		$ret['total_bet'] = $total_bet;
		$ret['total_win'] = $total_win;
		$ret['result'] = $result;
		$ret['result_bare'] = $result_bare;
		return $ret;
	}

	public function gen_round_by_tab_id($tab_id) {
		$ng_list = $this -> btr_dao -> need_gen_round_list($tab_id); // find next round need to generate
		if(count($ng_list) > 0) {
			// do gen
			foreach($ng_list as $a_round) {
				$this -> gen_round_detail($a_round);
			}
		}
	}

	public function tryround() {
		$a_round = $this -> btr_dao -> find_by_id(2115); // find next round need to generate
		echo "tryround... $a_round->id <br/>";
		$this -> gen_round_detail($a_round);

		echo "done...<br/>";
	}

	function gen_round_detail($a_round, $bypass = FALSE) {
		// init card deck
		$aCardValue=$this -> gen_card_value();
    $aCardDeck=$this -> gen_card_deck();

		$num_rounds = 65;
		for($pos = 1 ; $pos <= $num_rounds ; $pos++) {
				$i_data = array();
				$i_data['round_id'] = $a_round -> id;
				$i_data['pos'] = $pos;

				 $aTmpPlayerCards = array();
				 $aTmpPlayerCards[] = array_splice($aCardDeck, 0,1)[0];
				 $aTmpPlayerCards[] = array_splice($aCardDeck, 0,1)[0];

				 $aTmpDealerCards = array();
				 $aTmpDealerCards[] = array_splice($aCardDeck, 0,1)[0];
				 $aTmpDealerCards[] = array_splice($aCardDeck, 0,1)[0];

				 $iRet = $this -> simulateHand($aTmpPlayerCards,$aTmpDealerCards, $aCardValue, $aCardDeck, $i_data);
				 $i_data['winner'] = $iRet;

				 for($i = 0 ; $i < count($aTmpPlayerCards) ; $i++) {
						 $i_data["player_c_$i"] = $aTmpPlayerCards[$i];
				 }

				 for($i = 0 ; $i < count($aTmpDealerCards) ; $i++) {
						$i_data["banker_c_$i"] = $aTmpDealerCards[$i];
				 }

				 $found_empty = FALSE;
				 if(empty($i_data['player_c_' . 0])) {
					 $target = 'player_c_' . 0;
					 $val = $aTmpPlayerCards[0];
					 $found_empty = TRUE;
					 echo "found...................$target =  $val<br/>";
					 echo json_encode($aTmpPlayerCards);
				 }
				 if(empty($i_data['player_c_' . 1])) {
					 $target = 'player_c_' . 1;
					 $val = $aTmpPlayerCards[1];
					  $found_empty = TRUE;
					echo "found...................$target = $val<br/>";
					echo json_encode($aTmpPlayerCards);
				}
				 if(empty($i_data['banker_c_' . 0])) {
					 $target = 'banker_c_' . 0;
					 $val = $aTmpDealerCards[0];
					  $found_empty = TRUE;
					echo "found...................$target =  $val<br/>";
					echo json_encode($aTmpDealerCards);
				}
				 if(empty($i_data['banker_c_' . 1])) {
				  $target = 'banker_c_' . 1;
					$val = $aTmpDealerCards[1];
					 $found_empty = TRUE;
					echo "found...................$target =  $val<br/>";
					echo json_encode($aTmpDealerCards);
				}

				 // is pair
				 $i_data['winner_type'] = $iRet;
				 $pair_count = 0;
				 if(($aTmpPlayerCards[0] % 13) == ($aTmpPlayerCards[1] % 13)) {
					 // player pair
					 $i_data['winner_type'] = WIN_PLAYER_PAIR;
					 $pair_count++;
				 }
				 if(($aTmpDealerCards[0] % 13) == ($aTmpDealerCards[1] % 13)) {
					 // banker pair
					 $i_data['winner_type'] = WIN_BANKER_PAIR;
					  $pair_count++;
				 }
				 if($pair_count == 2) {
					 $i_data['winner_type'] = WIN_BANKER_AND_PLAYER_PAIR; // two pair
				 }

				 if($bypass == FALSE) {
					 $last_id = $this -> btrd_dao -> insert($i_data);
					 if( $found_empty) {
						 echo "last_id: $last_id <br/>";
					 }
				 }

		}

		if($bypass == FALSE) {
			$this -> btr_dao -> update(array(
				'round_details' => $num_rounds
			), $a_round -> id);
		}
	}

	function simulateHand(&$aPlayerCard, &$aDealerCards, $aCardValue, &$aCardDeck, &$i_data) {
				$iPlayerValue = 0;
        $iDealerValue = 0;
        for($i=0;$i<count($aPlayerCard);$i++){
            $iPlayerValue += $aCardValue[$aPlayerCard[$i]];
            $iDealerValue += $aCardValue[$aDealerCards[$i]];
        }

        $iPlayerValue = $iPlayerValue%10;
        $iDealerValue = $iDealerValue%10;

        $szWin;
        if($iDealerValue > 7){ // 8 or 9
            if($iDealerValue > $iPlayerValue){
                $szWin = WIN_DEALER;
            }else if($iDealerValue === $iPlayerValue){
                $szWin = WIN_TIE;
            }else{
                $szWin = WIN_PLAYER;
            }

						$i_data['player_val'] = $iPlayerValue;
						$i_data['banker_val'] = $iDealerValue;
            return $szWin;
        }

        $bDealToDealer = FALSE;
        if($iPlayerValue > 7){ // p:8 or 9, b:0~7
						$i_data['player_val'] = $iPlayerValue;
						$i_data['banker_val'] = $iDealerValue;
            return WIN_PLAYER;
        }else if($iPlayerValue < 6){ // p:0~5, b:0~7
            //PLAYER MUST GET ANOTHER CARD
            $iCard = array_splice($aCardDeck, 0,1)[0];
            $iThirdCardValue = $aCardValue[$iCard];
            $aPlayerCard[] = $iCard;
            $iPlayerValue += $iThirdCardValue;
            $iPlayerValue = $iPlayerValue%10;

            //BANKER TURN
            if($iDealerValue < 3){
                $bDealToDealer = TRUE;
            }else if($iDealerValue == 3 && $iThirdCardValue !=8){
                $bDealToDealer = TRUE;
            }else if($iDealerValue == 4 && ($iThirdCardValue > 1 && $iThirdCardValue < 8) ){
                $bDealToDealer = TRUE;
            }else if($iDealerValue == 5 && ($iThirdCardValue > 3 && $iThirdCardValue <8) ){
                $bDealToDealer = TRUE;
            }else if($iDealerValue == 6 && ($iThirdCardValue == 6 || $iThirdCardValue == 7) ){
                $bDealToDealer = FALSE;
            }

            if($bDealToDealer){
              	$iCard = array_splice($aCardDeck, 0,1)[0];
                $aDealerCards[] = $iCard;
							  $iDealerValue += $aCardValue[$iCard];
                $iDealerValue = $iDealerValue%10;
            }

        }else{ // p:6 or7, b:0~7
            if($iDealerValue < 6){  // p:6 or7, b: 0~5
                //DEALER MUST TAKE ANOTHER CARD
                $iCard = array_splice($aCardDeck, 0,1)[0];
								$aDealerCards[] = $iCard;
								$iDealerValue += $aCardValue[$iCard];
                $iDealerValue = $iDealerValue%10;
            } else { // p:6 or7, b:6 or 7 不補牌

						}
        }

				$i_data['player_val'] = $iPlayerValue;
				$i_data['banker_val'] = $iDealerValue;
        if($iDealerValue === $iPlayerValue){
            return WIN_TIE;
        }else if($iDealerValue > $iPlayerValue){
            return WIN_DEALER;
        }else{
            return WIN_PLAYER;
        }
	}

	function gen_card_deck() {
		$aCardDeck=array();
		for($i=0 ; $i< 8 ; $i++){ // 8 decks
      for($j=0 ; $j<52 ; $j++){
          $aCardDeck[] = $j;
          $iRest=($j+1)%13;
          if($iRest > 10 || $iRest === 0){
                  $iRest = 10;
          }
          $aCardValue[] = $iRest;
      }
    }
		shuffle($aCardDeck);
		return $aCardDeck;
	}

	function gen_card_value() {
		$aCardValue=array();
		for($j=0 ; $j<52 ; $j++){
				$iRest=($j+1)%13;
				if($iRest > 10 || $iRest === 0){
								$iRest = 10;
				}
				$aCardValue[] = $iRest;
		}
		return $aCardValue;
	}

	// public shuffle_card_deck($aCardDeck) {
	// 	var $aTmpDeck=array();
	// 	// clone
	// 	for($i=0;$i<count($aCardDeck);$i++){
	// 		$aTmpDeck[$i]=$aCardDeck[$i];
	// 	}
	//
	// 	$aShuffledCardDecks = array();
	// 	while (count($aTmpDeck) > 0) {
	// 		$aShuffledCardDecks.push(aTmpDeck.splice(Math.round(Math.random() * (aTmpDeck.length - 1)), 1)[0]);
	// 	}
	//
	// 	return _aShuffledCardDecks;
	// }

	public function test() {

		// init card deck
		$aCardValue=$this -> gen_card_value();
    $aCardDeck=$this -> gen_card_deck();

		$aTmpPlayerCards = array();
		$aTmpPlayerCards[] = 41;
		$aTmpPlayerCards[] = 15;

		$aTmpDealerCards = array();
		$aTmpDealerCards[] = 2;
		$aTmpDealerCards[] = 9;

		$i_data = array();
		$iRet = $this -> simulateHand($aTmpPlayerCards,$aTmpDealerCards, $aCardValue, $aCardDeck, $i_data);

		echo json_encode($i_data);
		echo json_encode($aTmpPlayerCards);
		echo json_encode($aTmpDealerCards);
	}
}
?>
