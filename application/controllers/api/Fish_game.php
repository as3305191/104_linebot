<?php
class Fish_game extends MY_Base_Controller {

	function __construct() {
		parent::__construct();

		$this -> load -> model('Users_dao', 'users_dao');
		$this -> load -> model('Images_dao', 'img_dao');
		$this -> load -> model('Corp_dao', 'corp_dao');

		$this -> load -> model('Fish_tab_dao', 'fish_tab_dao');
		$this -> load -> model('Fish_tab_user_dao', 'fish_tab_user_dao');
		$this -> load -> model('Fish_tab_lottery_dao', 'fish_tab_lottery_dao');
		$this -> load -> model('Fish_bet_dao', 'fish_bet_dao');
		$this -> load -> model('Fish_config_dao', 'fish_config_dao');
		$this -> load -> model('Fish_rounds_dao', 'fish_rounds_dao');
		$this -> load -> model('Fish_rounds_king_dao', 'fish_rounds_king_dao');

		$this -> load -> model('Wallet_tx_dao', 'wtx_dao');
		$this -> load -> model('Wallet_tx_bkc_dao', 'wtx_bkc_dao');
		$this -> load -> model('User_level_dao', 'ul_dao');

		$this -> load -> model('Lucky_draw_tx_dao', 'ld_tx_dao');
		$this -> load -> model('Marquee_dao', 'marquee_dao');

		$this -> load -> model('Lottery_tx_dao', 'ltx_dao');
		$this -> load -> model('Lottery_user_dao', 'lu_dao');
		$this -> load -> model('Wallet_tx_lucky_draw_dao', 'wtx_lucky_draw_dao');

		$this -> load -> model('Lottery_tx_key_dao', 'lt_key_dao');

		$this -> load -> model('Jp_tx_dao', 'jp_tx_dao');
		$this -> load -> model('Jp_tx_key_dao', 'jp_tx_key_dao');

		$this -> load -> model('Fish_game_msg_dao', 'fgm_dao');

		$this -> load -> model('Products_dao', 'products_dao');
		$this -> load -> model('Product_items_dao', 'product_items_dao');
		$this -> load -> model('Product_strengthen_dao', 'product_strengthen_dao');
	}

	public function index() {
		echo "hello..";
	}

	public function get_lottery_tx($user_id, $tab_id) {
		$res = array('success' => TRUE);
		$res['lottery_list'] = array();

		// 保證只有一組在執行
		$key_id = $this -> lt_key_dao -> get_key_id();
		$un_done_list = array();
		do {
			$un_done_list = $this -> lt_key_dao -> get_un_done_list($key_id);
		} while(count($un_done_list) > 0);
		// --------- start lock
		$l_user = $this -> lu_dao -> find_by_user_and_tab($user_id, $tab_id);

		if($l_user -> amt >= 50000) {
			$user = $this -> users_dao -> find_by_id($l_user -> user_id);

			$num = floor($l_user -> amt / 50000);
			$this -> lu_dao -> add_amt($user_id, $tab_id, -50000 * $num);
			// add lottery tx
			for($i = 0 ; $i < $num; $i++) {
				$lottery_tx = $this -> ltx_dao -> create_tx($user_id, $tab_id);
				$res['lottery_list'][] = $lottery_tx;

				// add fish game messsage
				$this -> fgm_dao -> insert(array(
					'user_id' => $user -> id,
					'corp_id' => $user -> corp_id,
					'msg_type' => "lottery",
					'title' => "獲得摸彩劵 : {$lottery_tx->sn}",
				));

				// add line message
				if(!empty($user -> line_sub)) {
					// $p = array();
					// $p['to'] = $user -> line_sub;
					// $p['messages'][] = array(
					// 	"type" => "text",
					// 	"text" => "獲得摸彩劵 : {$lottery_tx->sn}"
					// );
					// $res = call_line_api("POST", "https://api.line.me/v2/bot/message/push", json_encode($p), CHANNEL_ACCESS_TOKEN);
				}
			}
		}
		// --------- end lock
		$this -> lt_key_dao -> mark_key_id($key_id);

		$this -> to_json($res);
	}

	public function get_jp_tx($user_id, $tab_id) {
		$res = array('success' => TRUE);
		$res['jp_tx_list'] = array();

		// 保證只有一組在執行
		$key_id = $this -> jp_tx_key_dao -> get_key_id();
		$un_done_list = array();
		do {
			$un_done_list = $this -> jp_tx_key_dao -> get_un_done_list($key_id);
		} while(count($un_done_list) > 0);
		// --------- start lock
		$user = $this -> users_dao -> find_by_id($user_id);
		$corp = $this -> corp_dao -> find_by_id($user -> corp_id);
		$max_amt = 100000000;
		if($corp -> fish_jp_amt >= $max_amt) {
			// $amt = rand(1, $max_amt);

			$this -> corp_dao -> db -> query("update corp set fish_jp_amt = fish_jp_amt - {$max_amt} where id = {$corp->id}");

			$jp_tx = $this -> jp_tx_dao -> create_tx($user_id, $tab_id, $max_amt);
			$res['jp_tx_list'][] = $jp_tx;

			// add fish game messsage
			$msg_json = json_encode(array(
				'amt' => $jp_tx-> amt
			));
			$last_id = $this -> fgm_dao -> insert(array(
				'user_id' => $user -> id,
				'corp_id' => $user -> corp_id,
				'msg_type' => "jp_tx",
				'title' => "獲得JP獎金 : {$jp_tx->amt}",
				'msg_json' => $msg_json
			));


			$tx = array();
			$tx['tx_id'] = $jp_tx -> id;
			$tx['tx_type'] = "jp_reward"; // 收取贈禮
			$tx['corp_id'] = $user -> corp_id; // corp id
			$tx['user_id'] = $user -> id;
			$tx['amt'] = ($jp_tx -> amt);

			$tx['brief'] = "獲得JP獎金 : {$jp_tx->amt}";
			$this -> wtx_dao -> insert($tx);

			// add line message
			if(!empty($user -> line_sub)) {
				$p = array();
				$p['to'] = $user -> line_sub;
				$p['messages'][] = array(
					"type" => "text",
					"text" => "獲得JP獎金 : {$jp_tx->amt}"
				);
				$res = call_line_api("POST", "https://api.line.me/v2/bot/message/push", json_encode($p), CHANNEL_ACCESS_TOKEN);
			}
		}
		// --------- end lock
		$this -> jp_tx_key_dao -> mark_key_id($key_id);

		$this -> to_json($res);
	}

	public function list_tab() {
		$res = array('success' => TRUE);

		$corp_id = $this -> get_post('corp_id');
		$hall_id = $this -> get_post('hall_id');
		if(!empty($corp_id)) {
			$res['hall_id'] = $hall_id;
			$list = $this -> fish_tab_dao -> find_all_active_by_corp_id($corp_id, $hall_id);
			foreach($list as $each) {
				$tab_lottery_info = $this -> fish_tab_lottery_dao -> find_current_with_lottery_info($each -> id);
				$tab_lottery_info -> img_url = BASE_URL . "/api/images/get/{$tab_lottery_info->image_id}";
				$each -> lottery_info = $tab_lottery_info;
			}

			$res['list'] = $list;
		} else {
			$res['error_msg'] = "缺少必要參數";
		}
		$this -> to_json($res);
	}

	public function get_lottery_info() {
		$res = array('success' => TRUE);

		$payload = $this -> get_payload();
		$user_id = $payload['user_id'];

		$tab_id = $this -> get_post('tab_id');
		$hall_id = $this -> get_post('hall_id');
		if(!empty($tab_id) && !empty($user_id)) {
			$fish_tab_lottery = $this -> fish_tab_lottery_dao -> find_current_with_lottery_info($tab_id);
			$res['fish_tab_lottery_no'] = $fish_tab_lottery -> lottery_no;
			$res['fish_tab_lottery_id'] = $fish_tab_lottery -> id;
			$res['lottery_count'] = $this -> ltx_dao -> count_all_by_fish_tab_lottery_and_user($fish_tab_lottery -> id, $user_id);
		} else {
			$res['error_msg'] = "缺少必要參數";
		}
		$this -> to_json($res);
	}

	public function get_jp_amt() {
		$res = array('success' => TRUE);

		$payload = $this -> get_payload();
		$user_id = $payload['user_id'];
		$corp_id = $payload['corp_id'];
		$corp = $this -> corp_dao -> find_by_id($corp_id);
		$res['jp_amt'] = $corp -> fish_jp_amt;
		$this -> to_json($res);
	}

	public function tab_info() {
		$res = array('success' => TRUE);

		$payload = $this -> get_payload();
		$user_id = $payload['user_id'];

		$tab_id = $this -> get_post('tab_id');
		$hall_id = $this -> get_post('hall_id');
		if(!empty($tab_id) && !empty($user_id)) {
			$info = $this -> fish_tab_dao -> find_by_id($tab_id);
			$res['info'] = $info;

			$tab_lottery_info = $this -> fish_tab_lottery_dao -> find_current_with_lottery_info($tab_id);

			$tab_lottery_info -> img_url = BASE_URL . "/api/images/get/{$tab_lottery_info->image_id}";
			$info -> lottery_info = $tab_lottery_info;
		} else {
			$res['error_msg'] = "缺少必要參數";
		}
		$this -> to_json($res);
	}

	public function enter_tab() {
		$res = array();
		$res['success'] = TRUE;

		$payload = $this -> get_payload();
		$user_id = $payload['user_id'];

		$hall_id = $this -> get_post('hall_id');
		if(empty($hall_id)) {
			$hall_id = 0; // default 0
		}

		if(!empty($user_id)) {
			$user = $this -> users_dao -> find_by_id($user_id);
			$corp_id = $user -> corp_id;

			// delete expired first, default is 5 minutes
			$this -> fish_tab_user_dao -> delete_expired();

			// find tab
			$tab_id = 0;

			// find by user
			$tab_user = $this -> fish_tab_user_dao -> find_by_user_id($user_id, $hall_id);
			if(!empty($tab_user)) {
				$tab_id = $tab_user -> tab_id;
				$hall_id = $tab_user -> hall_id;
				$this -> fish_tab_user_dao -> db -> simple_query("update fish_tab_user set update_time = now() where
				tab_id = $tab_id and hall_id = $hall_id and user_id = $user_id ");
			} else {
				$keep_loop = FALSE;
				$l_count = 0;
				do{
					$list = $this -> fish_tab_user_dao -> db -> query("select * from fish_tab where corp_id = {$corp_id} and id not in(select tab_id from fish_tab_user where hall_id = {$hall_id}) limit 1") -> result();
					if(count($list) > 0) {
						// found tab
						$tab = $list[0];
						$tab_id = $tab -> id;
					} else {
						// create new tab
						// $tab_id = $this -> fish_tab_dao -> insert(array('corp_id' => $corp_id));
						// $this -> fish_tab_dao -> update(array('tab_name' => "NO{$tab_id}"), $tab_id);

					}
					$keep_loop = !$this -> fish_tab_user_dao -> db -> simple_query("insert into fish_tab_user(hall_id, tab_id, user_id, update_time) values($hall_id, $tab_id, $user_id, now())") && $l_count++ < 100;
				} while($keep_loop);
			}
		} else {
			$res['error_msg'] = "缺少USER ID";
		}

		$res['tab'] = $this -> fish_tab_dao -> find_by_id($tab_id);

		$this -> to_json($res);
	}

	public function enter_one_tab() {
		$res = array();
		$res['success'] = TRUE;

		$payload = $this -> get_payload();
		$user_id = $payload['user_id'];

		$tab_id = $this -> get_post('tab_id');
		$hall_id = $this -> get_post('hall_id');
		if(empty($hall_id)) {
			$hall_id = 0; // default 0
		}

		$user = $this -> users_dao -> find_by_id($user_id);
		$corp_id = $user -> corp_id;

		// enter tab
		$tab_user = $this -> fish_tab_user_dao -> find_by_tab_and_user($tab_id, $user_id, $hall_id);
		if(!empty($tab_user)) {
			// update
			$this -> fish_tab_user_dao -> update(array(
				'update_time' => date("Y-m-d H:i:s")
			), $tab_user -> id);
		} else {
			$this -> fish_tab_user_dao -> insert(array(
				'tab_id' => $tab_id,
				'user_id' => $user_id,
				'hall_id' => $hall_id,
			));
		}

		// delete expired first, default is 5 minutes
		$this -> fish_tab_user_dao -> delete_expired();

		$this -> to_json($res);
	}

	public function exit_tab() {
		$res = array();
		$res['success'] = TRUE;

		$payload = $this -> get_payload();
		$user_id = $payload['user_id'];

		$tab_id = $this -> get_post('tab_id');
		$hall_id = $this -> get_post('hall_id');
		if(empty($hall_id)) {
			$hall_id = 0; // default 0
		}

		if(!empty($user_id)) {
			$tab_user = $this -> fish_tab_user_dao -> find_by_tab_and_user($tab_id, $user_id, $hall_id);
			if(!empty($tab_user)) {
				$this -> fish_tab_user_dao -> delete($tab_user -> id);
			} else {
				$res['error_msg'] = "未在此桌";
			}
		} else {
			$res['error_msg'] = "查無USER";
		}

		$this -> to_json($res);
	}

	public function check_alive() {
		$res = array();
		$res['success'] = TRUE;

		$tab_id = $this -> get_post('tab_id');
		$payload = $this -> get_payload();
		$user_id = $payload['user_id'];
		$hall_id = $this -> get_post('hall_id');
		if(empty($hall_id)) {
			$hall_id = 0; // default 0
		}

		$item = $this -> fish_tab_user_dao -> find_by_tab_and_user($tab_id, $user_id, $hall_id);
		if(!empty($item)) {
			$this -> fish_tab_user_dao -> db -> simple_query(
				"update fish_tab_user set update_time = now() where
				 tab_id = $tab_id and hall_id = $hall_id and user_id = $user_id ");
		} else {
			$res['error_msg'] = "未進入該桌";
		}

		$this -> to_json($res);
	}

	public function check_remaining() {
		$res = array();
		$res['success'] = TRUE;

		$tab_id = $this -> get_post('tab_id');
		$payload = $this -> get_payload();
		$user_id = $payload['user_id'];

		$hall_id = $this -> get_post('hall_id');
		if(empty($hall_id)) {
			$hall_id = 0; // default 0
		}

		$item = $this -> fish_tab_user_dao -> find_by_tab_and_user($tab_id, $user_id, $hall_id);
		if(!empty($item)) {
			$due_time_linux = strtotime( $item -> update_time . ' + 5 minutes');
			$due_time = date("Y-m-d H:i:s", $due_time_linux);
			$res['due_time'] = $due_time;
			$res['remaining_sec'] = ($due_time_linux - time());
		} else {
			$res['error_msg'] = "未進入該桌";
		}

		$this -> to_json($res);
	}

	public function bet() {
		$bet_amt = $this -> get_post('bet_amt'); // 下注
		$corp_id = $this -> get_post('corp_id');
		$tab_id = $this -> get_post('tab_id');
		$user_id = $this -> get_post('user_id');
		$hall_id = $this -> get_post('hall_id');
		if(empty($hall_id)) {
			$hall_id = 0; // default 0
		}

		$data = array('success' => TRUE);
		if(empty($bet_amt) || empty($corp_id) || empty($tab_id) || empty($user_id)) {
			$data['error_msg'] = "缺少必要欄位";
 		} else {
			if(FALSE && intval($bet_amt * 100) % 50 > 0) {
				$data['error_msg'] = "須為50的倍數";
			} else {
				$data = $this -> do_bet($bet_amt, $corp_id, $tab_id, $user_id, $hall_id);
				if(!empty($data['slot_bet_id'])) {
					$this -> fish_bet_dao -> update(array(
						'json' => json_encode($data)
					), $data['slot_bet_id']);
				}
			}
		}
		$this -> to_json($data);
	}

	public function do_bet($bet_amt, $corp_id, $tab_id, $user_id, $hall_id = 0)
	{
		$win_amt = 0;
		$sp_amt = 0;
		$total_win = 0;
		$is_special = FALSE;
		$bounus_line = 25;

		$tmp_pool = 0;

		$pool_before = $this -> fish_bet_dao -> sum_total_amt($tab_id, $hall_id);
		$pool_before_sp = $this -> fish_bet_dao -> sum_total_amt_sp($tab_id, $hall_id);
		$data['pool_before'] = $pool_before;
		$data['pool_before_sp'] = $pool_before_sp;

		// get all icons
		$bonus_count = 1;
		$wild_count = 1;
		$ez = 1;
		if($hall_id == 0) {
			if($pool_before_sp > 100) {
				$bonus_count = 1 * $ez;
			}
			if($pool_before_sp > 1000) {
				$bonus_count = 1 * $ez;
			}
			if($pool_before_sp > 10000) {
				$wild_count = 2 * $ez;
				$bonus_count = 1 * $ez;
			}
			if($pool_before_sp > 100000) {
				$wild_count = 2 * $ez;
				$bonus_count = 2 * $ez;
			}
		} else if($hall_id == 1) {
			if($pool_before_sp > 1000) {
				$bonus_count = 2 * $ez;
			}
			if($pool_before_sp > 10000) {
				$bonus_count = 2 * $ez;
			}
			if($pool_before_sp > 100000) {
				$bonus_count = 2 * $ez;
			}
			if($pool_before_sp > 1000000) {
				$wild_count = 2 * $ez;
				$bonus_count = 2 * $ez;
			}
		} else if($hall_id == -1) {
			if($pool_before_sp > 10) {
				$bonus_count = 2 * $ez;
			}
			if($pool_before_sp > 50) {
				$bonus_count = 2 * $ez;
				$wild_count = 3 * $ez;
			}
			if($pool_before_sp > 250) {
				$bonus_count = 2 * $ez;
				$wild_count = 4 * $ez;
			}
			if($pool_before_sp > 1250) {
				$bonus_count = 3 * $ez;
				$wild_count = 5 * $ez;
			}
		}

		if($corp_id == 1) {
			$bonus_count = 10;
			// $wild_count = 100;
		}
		$icons = $this -> get_icons($bonus_count, $wild_count);
		$count_icons = count($icons) - 1;

		$each_bet_amt = $bet_amt / $bounus_line; // 每條線的下注

		$rs = array();
		$has88_idx = array();

		$data['match_arr'] = array();
		$data['origin_arr'] = array();

		// get all reward array
		$reward_arr = $this -> get_reward_array();
		$bypass_arr = array('wild');

		// check reward
		$bonus_icon_count = 0;
		$bonus_icon_arr = array();
		$mul_count = 0;
		$has_100 = 0;
		do {
			$bonus_icon_count = 0; // reset
			$bonus_icon_arr = array(); // reset
			$mul_count = 0;
			$has_100 = 0;

			$rs = array();

			$wild_tube_j_idx = array();
			for($i = 0 ; $i < 5 ; $i++) {
				$arr = array();
				for($j = 0 ; $j < 3 ; $j++) {
					$is_88_ok = TRUE;
					do{
						$res_icon = $icons[rand(0, $count_icons)];
						if($res_icon == 'bonus' || $res_icon == 'wild') {

							if($res_icon == 'wild') {
								if($i == 0 || $i == 4) { // 第 2,3,4 軸才會
									$is_88_ok = TRUE;
								} else {
									if(!in_array($j, $wild_tube_j_idx)) { // 不再j tube紀錄中則存入
										$wild_tube_j_idx[] = $j;
									}
									$is_88_ok = FALSE;
								}
							}

							if($res_icon == 'bonus') {
								if(in_array($j, $wild_tube_j_idx) || in_array($i, $bonus_icon_arr)) { // 有wild就不會有BONUS
									$is_88_ok = TRUE;
								} else { // 第三軸
									$is_88_ok = FALSE;
									$bonus_icon_arr[] = $i;
									$bonus_icon_count++;
								}
							}
						} else {
							$is_88_ok = FALSE;
						}
					}while($is_88_ok);

					$arr[] = $res_icon;
				}
				$rs[] = $arr;
			}

			$is_special = ($bonus_icon_count > 2); // 至少2個bonus

			$win_amt = 0;
			$total_win = 0;
			$total_amt = 0;

			$data['match_arr'] = array();
			$data['origin_arr'] = array();
			$data['first_match_arr'] = array();
			$data['match_count_arr'] = array();
			$data['multiply_arr'] = array();
			$data['special_order'] = array();
			$data['special_multiply'] = array();
			$data['special_round'] = array();

			foreach($reward_arr as $each) {
				$c_count = count($each -> coord);
				$match_coord = array();
				$match_icons = array();

				$first_match = "";
				$not_cont = FALSE;
				for($i = 0 ; $i < $c_count ; $i++) {
					$j = $each -> coord[$i];
					$this_icon = $rs[$i][$j];

					if(!$not_cont) {
						if($i == 0) {
							// first
							$first_match = $this_icon;
							$match_icons[] = $this_icon;
							$match_coord[] = $each -> coord[$i];
						} else {
							// compare
							if($first_match == $this_icon || in_array($this_icon, $bypass_arr) || in_array($first_match, $bypass_arr)) {
								if(in_array($first_match, $bypass_arr)) { // 取代first match
									if(!in_array($this_icon, $bypass_arr)) {
										$first_match = $this_icon;
									}
								}
								$match_icons[] = $this_icon;
								$match_coord[] = $j;
							} else {
								$not_cont = TRUE;
							}
						}
					}
				}

				if(count($match_icons) > 2 && $first_match != 'bonus') { // bonus 不計分
					$data['match_arr'][] = $match_coord;
					$data['first_match_arr'][] = $first_match;
					$data['match_count_arr'][] = count($match_icons);
					$data['multiply_arr'][] = $this -> get_multiply($first_match, count($match_icons));
					$data['origin_arr'][] = $each -> coord;
				}
			}

			for($i = 0 ; $i < count($data['first_match_arr']) ; $i++) {
				$this_match = $data['first_match_arr'][$i];
				$this_match_count = $data['match_count_arr'][$i];
				$this_multiply = $data['multiply_arr'][$i];
				if($this_multiply > 100) {
					$has_100 = 1;
				}
				$win_amt += ($each_bet_amt * $this_multiply);
			}

			$total_win = $win_amt;
			$total_amt = -$bet_amt + $total_win;
		} while(
			($total_win > 0 && ($pool_before < $total_amt))
			|| ($is_special && $pool_before_sp < ($bet_amt * 2))  // 如果彩池小於下注數值就不能有特殊遊戲
		);

		$data['corp_id'] = $corp_id;
		$data['hall_id'] = $hall_id;
		$data['tab_id'] = $tab_id;
		$data['l_cnt'] = 0;
		$data['rs'] = $rs;
		$data['is_special'] = $is_special ? 1 : 0;
		$data['bet_amt'] = $bet_amt;
		$data['win_amt'] = $win_amt;
		$data['total_amt'] = $total_amt;

		// 計算分潤
		$this_amt_share = $total_amt < 0 ? -$total_amt : 0;
		$cfg = $this -> fish_config_dao -> find_one();

		$i_data = array();
		$i_data['rs'] = json_encode($rs);
		$i_data['corp_id'] = $corp_id;
		$i_data['tab_id'] = $tab_id;
		$i_data['hall_id'] = $hall_id;
		$i_data['user_id'] = $user_id;
		$i_data['bet_amt'] = $bet_amt;
		$i_data['win_amt'] = $win_amt;
		$i_data['is_sp'] = $is_special ? 1 : 0;
		$i_data['sp_amt_temp'] = $sp_amt;
		$i_data['total_amt_origin'] = -$total_amt;
		$i_data['total_amt'] = $this_amt_share > 0 ? (floatval($this_amt_share) * (floatval($cfg -> pool_pct) / 100.0)) : (-$total_amt); // 彩池
		$i_data['total_amt_sp'] = $this_amt_share > 0 ? (floatval($this_amt_share) * (floatval($cfg -> pool_pct_sp) / 100.0)) : 0; // 彩池
		$last_id = $this -> fish_bet_dao -> insert($i_data);

		$data['slot_bet_id'] = $last_id;

		$user = $this -> users_dao -> find_by_id($user_id);

		// has 100
		$tx_r = array();
		$tx_r['tab_id'] = $tab_id;
		$tx_r['hall_id'] = $hall_id;
		$tx_r['user_id'] = $user_id;
		$tx_r['has_100'] = $has_100;
		$tx_r['slot_bet_id'] = $last_id;
		$tx_r['is_sp'] = 0;
		$this -> fish_rounds_dao -> insert($tx_r);

		// 計算分潤
		if($this_amt_share > 0 && $hall_id > 0 ) {
			$tx_amt_player = floatval($this_amt_share) * (floatval($cfg -> player_pct) / 100.0);
			$tx_amt_intro = floatval($this_amt_share) * (floatval($cfg -> intro_pct) / 100.0);
			$tx_amt_com = floatval($this_amt_share) * (floatval($cfg -> com_pct) / 100.0);
			$tx_amt_lucky_draw = floatval($this_amt_share) * (floatval($cfg -> lucky_draw_pct) / 100.0);

			if($hall_id > -1) {
				// player
				$tx = array();
				$tx['slot_bet_id'] = $last_id;
				$tx['corp_id'] = $user -> corp_id; // corp id
				$tx['user_id'] = $user -> id;
				$tx['amt'] = $tx_amt_player;
				$tx['type_id'] = 80; // 個人分潤
				$tx['brief'] = "會員 $user->account 孫行者下注，個人 分潤 $tx_amt_player 點 - {$cfg->player_pct}%";
				$this -> wtx_dao -> insert($tx);

				// intro
				$tx = array();
				$tx['slot_bet_id'] = $last_id;
				$tx['corp_id'] = $user -> corp_id; // corp id
				$tx['user_id'] = $user -> intro_id;

				$tx['amt'] = $tx_amt_intro;
				$tx['type_id'] = 81; // 介紹人分潤收入
				$tx['brief'] = "會員 $user->account 孫行者下注，介紹人 分潤 $tx_amt_intro 點 - {$cfg->intro_pct}%";
				$this -> wtx_dao -> insert($tx);

				// company
				$tx = array();
				$tx['slot_bet_id'] = $last_id;
				$tx['corp_id'] = $user -> corp_id; // corp id
				$tx['user_id'] = 0; // 公司

				$tx['amt'] = $tx_amt_com;
				$tx['type_id'] = 82; // 公司分潤收入
				$tx['brief'] = "會員 $user->account 孫行者下注，公司 分潤 $tx_amt_com 點 - {$cfg->com_pct}%";
				$this -> wtx_dao -> insert($tx);

				// lucky draw
				$tx = array();
				$tx['slot_bet_id'] = $last_id;
				$tx['corp_id'] = $user -> corp_id; // corp id
				$tx['amt'] = $tx_amt_lucky_draw;
				$tx['type_id'] = 30; // 孫行者分潤
				$tx['brief'] = "會員 $user->account 孫行者下注，摸彩 分潤 $tx_amt_lucky_draw 點 - {$cfg->lucky_draw_pct}%";
				$this -> wtx_lucky_draw_dao -> insert($tx);
			}

			// update sum lucky draw
			$s_lucky_draw = $this -> wtx_lucky_draw_dao -> sum_all($user -> corp_id);
			$this -> corp_dao -> update(array(
				'sum_lucky_draw' => $s_lucky_draw
			), $user -> corp_id);
		}

		//--- end of 計算分潤

		$tx = array();
		$tx['slot_bet_id'] = $last_id;
		$tx['corp_id'] = $user -> corp_id; // corp id
		$tx['user_id'] = $user -> id;
		$tx['amt'] = $total_amt;
		$tx['type_id'] = 170;
		$tx['brief'] = "會員 {$user->account} 孫行者下注派彩 {$total_amt} ";
		if($hall_id > -1) {
			$this -> wtx_dao -> insert($tx);
		} else {
			$this -> wtx_bkc_dao -> insert($tx);
		}

		$pool_val = $this -> fish_bet_dao -> sum_total_amt($tab_id, $hall_id);
		$pool_val_sp = $this -> fish_bet_dao -> sum_total_amt_sp($tab_id, $hall_id);
		$data['pool_after'] = $pool_val;
		$data['pool_after_sp'] = $pool_val_sp;

		// chekc special
		$data['sp_arr'] = array();

		$result_arr = array();
		if($is_special) {

			// get total multiply
			$mul_count = 1;

			// 如果沒倍數就代表彩池不足，清空
			$rs_win_amt = 0;
			$sp_rs_data = array();
			$round_count = 0; // 預設
			if($mul_count == 0) {
				$result_arr = array();
				$data['tmp_pool'] = 0;
			} else {
				// 有倍數
				$tmp_pool = $pool_val_sp;
				$data['tmp_pool'] = $tmp_pool;

				$sp_l_count = 0;
				$sp_l_count_max = 100;

				$round_count = 8; // 免費局數
				$mul_count = 0; // 免費倍數
				if($bonus_icon_count == 3) {
					$round_count = 8;
					$mul_count = 2;
				}
				if($bonus_icon_count == 4) {
					$round_count = 12;
					$mul_count = 20;
				}
				if($bonus_icon_count >= 5) {
					$round_count = 20;
					$mul_count = 200;
				}

				$data['multiply_count'] = $mul_count;
				$data['round_count_sp'] = $round_count;
				$data['bonus_icon_count'] = $bonus_icon_count;

				$is_loop = FALSE;

				$win_count = 0;
				$loose_count = 0;
				do {
					// get special round
					$mode = 0;  //mode 0 一般 mode 1 只有贏, mode 2 只有輸
					$r_data = $this -> get_sp_result($bet_amt, $tmp_pool, $mul_count, $mode, $round_count);

					$test_tmp_pool = $tmp_pool - $r_data['total_win'];
					$rs_win_amt += $r_data['total_win'];
					$tmp_pool = $test_tmp_pool;
					$sp_rs_data[] = $r_data;

					$round_count = $r_data['current_round_count'];

					if($r_data['total_win'] > 0) {
						$win_count++;
					} else {
						$loose_count++;
					}

					$is_loop = count($sp_rs_data) < $round_count;
				} while($is_loop); // 沒有超過最大局數
			}

			$data['s_tmp_pool'] = $tmp_pool;

			// append rounds
			$data['rs_win_amt'] = $rs_win_amt;
			$data['round_count'] = $round_count;
			$data['round_count_sp'] = $round_count;

			$data['result_arr'] = $result_arr;
			$data['sp_rs_arr'] = $sp_rs_data;
			$data['sp_win_count'] = $win_count;
			$data['sp_loose_count'] = $loose_count;

			// update record
			$this -> fish_bet_dao -> update(array(
				'sp_amt_temp' => $rs_win_amt,
				'sp_rs' => json_encode($sp_rs_data)
			), $last_id);
		}

		// check sum bonus game
		$user = $this -> users_dao -> find_by_id($user_id);
		$sum_bonus_game = $this -> fish_bet_dao -> sum_free_game($user_id);
		$data['sum_free_game'] = $sum_bonus_game;
		if($sum_bonus_game > 0 && ($sum_bonus_game % 10 == 0) && $sum_bonus_game > $user -> last_sum_free_game ) {
			// 提供摸彩卷
			$this -> users_dao -> update(array(
				'last_sum_free_game' => $sum_bonus_game
			), $user_id);

			$slot_tab = $this -> fish_tab_dao -> find_by_id($tab_id);
			// save tx
			$tx = array();
			$tx['corp_id'] = $corp_id;
			$tx['user_id'] = $user_id;
			$tx['slot_bet_id'] = $data['slot_bet_id'];
			$tx['num'] = 1;
			$tx['brief'] = "user id: {$user->nick_name} 孫行者 獲得一張摸彩卷";
			$this -> ld_tx_dao -> insert($tx);

			// 恭喜獲得摸彩卷 跑馬燈
			$mq = array();
			$mq['corp_id'] = $corp_id;
			$mq['need_delay'] = 20;
			$mq['title'] = "恭喜 {$user->nick_name} 在孫行者 {$slot_tab->tab_name} 獲得一張摸彩卷";
			$this -> marquee_dao -> insert($mq);
		}

		return $data;
	}

	public function get_sp_result($bet_amt, $pool_before, $mul_count, $bonus_mode = 0, $current_round_count) {
		$bonus_icons = 1;
		$wild_icons = 1;

		// get all icons
		$icons = $this -> get_icons($bonus_icons, $wild_icons); // bonus game
		$count_icons = count($icons) - 1;
		$bounus_line = 25;
		$each_bet_amt = $bet_amt / $bounus_line; // 每條線的下注

		$rs = array();
		$has88_idx = array();

		$data['match_arr'] = array();
		$data['origin_arr'] = array();

		// get all reward array
		$reward_arr = $this -> get_reward_array();
		$bypass_arr = array('wild');

		// check reward

		$is_loop = FALSE;
		$is_special = 0;
		$bonus_icon_count = 0;
		$bonus_icon_arr = array(); // reset
		$append_round_count = 0;
		$wild_tube_j_idx = array();
		$wild_tube_i_idx = array();

		$has_100 = 0;
		do {
			$bonus_icon_count = 0;
			$bonus_icon_arr = array(); // reset
			$append_round_count = 0;
			$has_100 = 0;

			$rs = array();

			$wild_tube_j_idx = array(); // reset
			$wild_tube_i_idx = array(); // reset
			for($i = 0 ; $i < 5 ; $i++) {
				$arr = array();
				for($j = 0 ; $j < 3 ; $j++) {
					$is_88_ok = TRUE;
					do{
						$res_icon = $icons[rand(0, $count_icons)];
						if($res_icon == 'bonus' || $res_icon == 'wild') {

							if($res_icon == 'wild') {
								if($i == 0 || $i == 4) { // 第 2,3,4 軸才會
									$is_88_ok = TRUE;
								} else {
									if(!in_array($j, $wild_tube_j_idx)) { // 不再j tube紀錄中則存入
										$wild_tube_j_idx[] = $j;
									}
									if(!in_array($i, $wild_tube_i_idx)) { // 不再j tube紀錄中則存入
										$wild_tube_i_idx[] = $i;
									}
									$is_88_ok = FALSE;
								}
							}

							if($res_icon == 'bonus') {
								if(in_array($j, $wild_tube_j_idx) || in_array($i, $bonus_icon_arr)) { // 有wild就不會有BONUS
									$is_88_ok = TRUE;
								} else { // 第三軸
									$is_88_ok = FALSE;
									$bonus_icon_arr[] = $i;
									$bonus_icon_count++;
								}
							}
						} else {
							$is_88_ok = FALSE;
						}
					}while($is_88_ok);

					$arr[] = $res_icon;
				}
				$rs[] = $arr;
			}

			$is_special = ($bonus_icon_count > 2); // 兩局以上

			if($bonus_icon_count >= 3) {
				$append_round_count = 8;
			}
			if($bonus_icon_count >= 4) {
				$append_round_count = 12;
			}
			if($bonus_icon_count >= 5) {
				$append_round_count = 20;
			}

			$win_amt = 0;
			$total_win = 0;
			$total_amt = 0;

			$data['match_arr'] = array();
			$data['origin_arr'] = array();
			$data['first_match_arr'] = array();
			$data['match_count_arr'] = array();
			$data['multiply_arr'] = array();
			$data['special_order'] = array();
			$data['special_multiply'] = array();
			$data['special_round'] = array();

			$data['is_reverse'] = array();
			$data['is_wild'] = array();

			foreach($reward_arr as $each) {
				$c_count = count($each -> coord);
				$match_coord = array();
				$match_icons = array();
				$match_coord_wld = array();
				$match_icons_wld = array();

				$match_coord_rev = array();
				$match_icons_rev = array();
				$match_coord_rev_wld = array();
				$match_icons_rev_wld = array();

				$first_match = "";
				$first_match_wld = "";
				$first_match_rev = "";
				$first_match_rev_wld = "";
				$not_cont = FALSE;
				$not_cont_wld = FALSE;
				$not_cont_rev = FALSE;
				$not_cont_rev_wld = FALSE;

				// left to right
				for($i = 0 ; $i < $c_count ; $i++) {
					$j = $each -> coord[$i];
					$this_icon = $rs[$i][$j];

					if(!$not_cont) {
						if($i == 0) {
							// first
							$first_match = $this_icon;
							$match_icons[] = $this_icon;
							$match_coord[] = $each -> coord[$i];
						} else {
							// compare
							if($first_match == $this_icon || in_array($this_icon, $bypass_arr) || in_array($first_match, $bypass_arr)) {
								if(in_array($first_match, $bypass_arr)) { // 取代first match
									if(!in_array($this_icon, $bypass_arr)) {
										$first_match = $this_icon;
									}
								}
								$match_icons[] = $this_icon;
								$match_coord[] = $j;
							} else {
								$not_cont = TRUE;
							}
						}
					}
				}

				// left to right _wild
				if(count($wild_tube_i_idx) > 0) {
					for($i = 0 ; $i < $c_count ; $i++) {
						$j = $each -> coord[$i];
						$this_icon = $rs[$i][$j];
						if(in_array($i, $wild_tube_i_idx)) { // 變成wild
							$this_icon = 'wild';
						}

						if(!$not_cont_wld) {
							if($i == 0) {
								// first
								$first_match_wld = $this_icon;
								$match_icons_wld[] = $this_icon;
								$match_coord_wld[] = $each -> coord[$i];
							} else {
								// compare
								if($first_match_wld == $this_icon || in_array($this_icon, $bypass_arr) || in_array($first_match_wld, $bypass_arr)) {
									if(in_array($first_match_wld, $bypass_arr)) { // 取代first match
										if(!in_array($this_icon, $bypass_arr)) {
											$first_match_wld = $this_icon;
										}
									}
									$match_icons_wld[] = $this_icon;
									$match_coord_wld[] = $j;
								} else {
									$not_cont_wld = TRUE;
								}
							}
						}
					}
				}

				//right to left
				for($i = ($c_count - 1)  ; $i > -1 ; $i--) {
					$j = $each -> coord[$i];
					$this_icon = $rs[$i][$j];

					if(!$not_cont_rev) {
						if($i == ($c_count - 1)) {
							// first
							$first_match_rev = $this_icon;
							$match_icons_rev[] = $this_icon;
							$match_coord_rev[] = $each -> coord[$i];
						} else {
							// compare
							if($first_match_rev == $this_icon || in_array($this_icon, $bypass_arr) || in_array($first_match_rev, $bypass_arr)) {
								if(in_array($first_match_rev, $bypass_arr)) { // 取代first match
									if(!in_array($this_icon, $bypass_arr)) {
										$first_match_rev = $this_icon;
									}
								}
								$match_icons_rev[] = $this_icon;
								$match_coord_rev[] = $j;
							} else {
								$not_cont_rev = TRUE;
							}
						}
					}
				}

				// right to left _wld
				// left to right _wild
				if(count($wild_tube_i_idx) > 0) {
					for($i = ($c_count - 1)  ; $i > -1 ; $i--) {
						$j = $each -> coord[$i];
						$this_icon = $rs[$i][$j];
						if(in_array($i, $wild_tube_i_idx)) { // 變成wild
							$this_icon = 'wild';
						}

						if(!$not_cont_rev_wld) {
							if($i == ($c_count - 1)) {
								// first
								$first_match_rev_wld = $this_icon;
								$match_icons_rev_wld[] = $this_icon;
								$match_coord_rev_wld[] = $each -> coord[$i];
							} else {
								// compare
								if($first_match_rev_wld == $this_icon || in_array($this_icon, $bypass_arr) || in_array($first_match_rev_wld, $bypass_arr)) {
									if(in_array($first_match_rev_wld, $bypass_arr)) { // 取代first match
										if(!in_array($this_icon, $bypass_arr)) {
											$first_match_rev_wld = $this_icon;
										}
									}
									$match_icons_rev_wld[] = $this_icon;
									$match_coord_rev_wld[] = $j;
								} else {
									$not_cont_rev_wld = TRUE;
								}
							}
						}
					}
				}

				if(count($match_icons) > 2 && $first_match != 'wild' && $first_match != 'bonus') {
					$data['is_reverse'][] = 0;
					$data['is_wild'][] = 0;
					$data['match_arr'][] = $match_coord;
					$data['first_match_arr'][] = $first_match;
					$data['match_count_arr'][] = count($match_icons);
					$data['multiply_arr'][] = $this -> get_multiply($first_match, count($match_icons));
					$data['origin_arr'][] = $each -> coord;
				}

				if(count($match_icons_wld) > 2 && $first_match_wld != 'wild' && $first_match_wld != 'bonus') {
					$data['is_reverse'][] = 0;
					$data['is_wild'][] = 1;
					$data['match_arr'][] = $match_coord_wld;
					$data['first_match_arr'][] = $first_match_wld;
					$data['match_count_arr'][] = count($match_icons_wld);
					$data['multiply_arr'][] = $this -> get_multiply($first_match_wld, count($match_icons_wld));
					$data['origin_arr'][] = $each -> coord;
				}

				if(count($match_icons_rev) > 2 && $first_match_rev != 'wild' && $first_match_rev != 'bonus') {
					$data['is_reverse'][] = 1;
					$data['is_wild'][] = 0;
					$data['match_arr'][] = $match_coord_rev;
					$data['first_match_arr'][] = $first_match_rev;
					$data['match_count_arr'][] = count($match_icons_rev);
					$data['multiply_arr'][] = $this -> get_multiply($first_match_rev, count($match_icons_rev));
					$data['origin_arr'][] = $each -> coord;
				}

				if(count($match_icons_rev_wld) > 2 && $first_match_rev_wld != 'wild' && $first_match_rev_wld != 'bonus') {
					$data['is_reverse'][] = 1;
					$data['is_wild'][] = 1;
					$data['match_arr'][] = $match_coord_rev_wld;
					$data['first_match_arr'][] = $first_match_rev_wld;
					$data['match_count_arr'][] = count($match_icons_rev_wld);
					$data['multiply_arr'][] = $this -> get_multiply($first_match_rev_wld, count($match_icons_rev_wld));
					$data['origin_arr'][] = $each -> coord;
				}
			}

			for($i = 0 ; $i < count($data['first_match_arr']) ; $i++) {
				$this_match = $data['first_match_arr'][$i];
				$this_match_count = $data['match_count_arr'][$i];
				$this_multiply = $data['multiply_arr'][$i];
				if(($this_multiply * $mul_count) >= 100) {
					$has_100 = 1;
				}
				$win_amt += ($each_bet_amt * $this_multiply * $mul_count);
			}

			$total_win = $win_amt;

			$is_loop = ($total_win > 0 && ($pool_before < $total_win));
		} while(
			$is_loop
		);

		$data['rs'] = $rs;

		$current_round_count += $append_round_count;
		$current_round_count = $current_round_count > 120 ? 120 : $current_round_count;
		$data['current_round_count'] = $current_round_count;
		$data['append_round_count'] = $append_round_count;

		$data['bet_amt'] = $bet_amt;
		$data['win_amt'] = $win_amt;
		$data['is_special'] = $is_special ? 1 : 0;
		$data['result_mode'] = $total_win > 0 ? 1 : 2;
		$data['total_win'] = $total_win;
		$data['has_100'] = $has_100;
		return $data;
	}

	public function get_multiply($icon, $num_match) {

		if(in_array($icon, array("A"))) {
			switch ($num_match) {
				case 3:
					return 5;
				case 4:
					return 50;
				case 5:
					return 100;
				default:
					return 1;
			}
		}
		if(in_array($icon, array("Q", "K"))) {
			switch ($num_match) {
				case 3:
					return 5;
				case 4:
					return 30;
				case 5:
					return 100;
				default:
					return 1;
			}
		}
		if(in_array($icon, array("J", "10"))) {
			switch ($num_match) {
				case 3:
					return 5;
				case 4:
					return 20;
				case 5:
					return 100;
				default:
					return 1;
			}
		}
		if(in_array($icon, array("9"))) {
			switch ($num_match) {
				case 3:
					return 5;
				case 4:
					return 15;
				case 5:
					return 100;
				default:
					return 1;
			}
		}

		if($icon == 'bonus') {
			switch ($num_match) {
				case 3:
					return 20;
				case 4:
					return 100;
				case 5:
					return 400;
				default:
					return 1;
			}
		}

		if($icon == 'wild') {
			switch ($num_match) {
				case 3:
					return 25;
				case 4:
					return 200;
				case 5:
					return 1000;
				default:
					return 1;
			}
		}
		if($icon == '壺') {
			switch ($num_match) {
				case 3:
					return 50;
				case 4:
					return 150;
				case 5:
					return 1000;
				default:
					return 1;
			}
		}
		if($icon == '珠') {
			switch ($num_match) {
				case 3:
					return 50;
				case 4:
					return 100;
				case 5:
					return 400;
				default:
					return 1;
			}
		}
		if($icon == '扇') {
			switch ($num_match) {
				case 3:
					return 10;
				case 4:
					return 50;
				case 5:
					return 250;
				default:
					return 1;
			}
		}
		if($icon == '書') {
			switch ($num_match) {
				case 3:
					return 10;
				case 4:
					return 50;
				case 5:
					return 125;
				default:
					return 1;
			}
		}
	}

	public function get_sp_round_arr() {
		$sp_arr = [
			1,
			1,
			2,
			2,
			3,
			3,
			5,
			5
		];
		return $sp_arr;
	}

	public function get_sp_mul_arr() {
		$sp_arr = [
			1,
			1,
			1,
			1,
			1,
			1,
			1,
			1,
			1,
			1,
			1,
			1,
			1,
			3,
			3,
			3,
			3,
			3,
			3,
			5,
			5,
			5,
			5,
			5,
			6,
			6,
			6,
			6,
			6,
			6,
			6,
			6,
			10,
			20,
			20,
			20,
		];
		return $sp_arr;
	}

	public function get_type1() {
		$game_type1 = [
			"壺",
			"珠",
			"扇",
			"書"
		];
		return $game_type1;
	}

	public function get_type2() {
		$game_type2 = [
			"A",
			"J",
			"K",
			"Q",
			"9",
			"10"
		];
		return $game_type2;
	}

	public function get_icons($bonus_count = 100, $wild_count = 1) {
		$game_type1 = $this -> get_type1();
		$game_type2 = $this -> get_type2();

		$all_arr = array();
		foreach($game_type1 as $each) {
			for($i = 0 ; $i < 1 ; $i++) {
				$all_arr[] = $each;
			}
		}

		foreach($game_type2 as $each) {
			for($i = 0 ; $i < 2 ; $i++) {
				$all_arr[] = $each;
			}
		}

		if($bonus_count > 0) {
			for($i = 0 ; $i < $bonus_count ; $i++) {
				$all_arr[] = "bonus";
			}
		}

		for($i = 0 ; $i < $wild_count ; $i++) {
			$all_arr[] = "wild";
		}
		shuffle($all_arr);
		return $all_arr;
	}

	public function get_reward_array() {
		$json =  '[{"coord":[1,1,1,1,1],"idx":1},{"coord":[0,0,0,0,0],"idx":2},{"coord":[2,2,2,2,2],"idx":3},{"coord":[0,1,2,1,0],"idx":4},{"coord":[2,1,0,1,2],"idx":5},{"coord":[0,0,1,2,2],"idx":6},{"coord":[2,2,1,0,0],"idx":7},{"coord":[1,0,1,2,1],"idx":8},{"coord":[1,2,1,0,1],"idx":9},{"coord":[0,1,1,1,2],"idx":10},{"coord":[2,1,1,1,0],"idx":11},{"coord":[1,0,0,1,2],"idx":12},{"coord":[1,2,2,1,0],"idx":13},{"coord":[1,1,0,1,2],"idx":14},{"coord":[1,1,2,1,0],"idx":15},{"coord":[0,0,1,2,1],"idx":16},{"coord":[2,2,1,0,1],"idx":17},{"coord":[1,0,1,2,2],"idx":18},{"coord":[1,2,1,0,0],"idx":19},{"coord":[0,0,0,1,2],"idx":20},{"coord":[2,2,2,1,0],"idx":21},{"coord":[0,1,2,2,2],"idx":22},{"coord":[2,1,0,0,0],"idx":23},{"coord":[0,1,2,1,2],"idx":24},{"coord":[2,1,0,1,0],"idx":25}]';
		return json_decode($json);
	}

	public function enter_free_game() {
		$res = array('success' => TRUE);
		$slot_bet_id = $this -> get_post('slot_bet_id');

		if(empty($slot_bet_id)) {
			$res['error_msg'] = '缺少必要欄位';
		} else {
			$bet = $this -> fish_bet_dao -> find_by_id($slot_bet_id);
			$user = $this -> users_dao -> find_by_id($bet -> user_id);

			// add new
			$i_data = array();
			$i_data['parent_id'] = $slot_bet_id;
			$i_data['tab_id'] = $bet -> tab_id;
			$i_data['hall_id'] = $bet -> hall_id;
			$i_data['user_id'] = $bet -> user_id;
			$i_data['corp_id'] = $bet -> corp_id;
			$i_data['bet_amt'] = $bet -> bet_amt;
			$i_data['win_amt'] = $bet -> sp_amt_temp;
			$i_data['is_sp'] = 1;
			$i_data['total_amt_sp'] = -$bet -> sp_amt_temp;
			$last_id = $this -> fish_bet_dao -> insert($i_data);

			$tx = array();
			$tx['slot_bet_id'] = $last_id;
			$tx['corp_id'] = $user -> corp_id; // corp id
			$tx['user_id'] = $user -> id;
			$tx['amt'] = $bet -> sp_amt_temp;
			$tx['type_id'] = 171;
			$tx['brief'] = "會員 {$user->account} 孫行者下注，免費遊戲贏得籌碼 {$bet->sp_amt_temp} ";

			if($bet -> hall_id > -1) {
				$this -> wtx_dao -> insert($tx);
			} else {
				$this -> wtx_bkc_dao -> insert($tx);
			}

			if($bet->sp_amt_temp > 0) {
				$mq = array();
				$mq['corp_id'] = $user -> corp_id;
				$mq['need_delay'] = 120;
				$mq['slot_bet_id'] = $slot_bet_id;
				$a_amt = $bet->sp_amt_temp * 100;
				$mq['title'] = "恭喜會員 {$user->nick_name} 在孫行者免費遊戲贏得籌碼 {$a_amt} ";
				$this -> marquee_dao -> insert($mq);
			}

			if(!empty($bet -> json)) {
				$a_bet_obj = json_decode($bet -> json);
				$sp_rs_arr = $a_bet_obj -> sp_rs_arr;
				foreach($sp_rs_arr as $each) {
					// has 100
					$tx_r = array();
					$tx_r['tab_id'] = $bet -> tab_id;
					$tx_r['hall_id'] = $bet -> hall_id;
					$tx_r['user_id'] = $bet -> user_id;
					$tx_r['has_100'] = $each -> has_100;
					$tx_r['slot_bet_id'] = $bet -> id;
					$tx_r['is_sp'] = 1;
					$this -> fish_rounds_dao -> insert($tx_r);
				}
			}

		}

		$this -> to_json($res);
	}

	public function finish_free_game() {
		$res = array('success' => TRUE);
		$slot_bet_id = $this -> get_post('slot_bet_id');

		if(empty($slot_bet_id)) {
			$res['error_msg'] = '缺少必要欄位';
		} else {
			$marquee_list = $this -> marquee_dao -> find_all_by('slot_bet_id', $slot_bet_id);
			foreach($marquee_list as $each) {
				$this -> marquee_dao -> update(array(
					'need_delay' => 0
				), $each -> id);
			}
		}

		$this -> to_json($res);
	}

	public function round_count() {
		$res = array('success' => TRUE);
		$tab_id = $this -> get_post('tab_id');
		$hall_id = $this -> get_post('hall_id');

		if($tab_id == '' || $hall_id == '') {
			$res['error_msg'] = '缺少必要欄位';
		} else {
			$res['rounds'] =  $this -> fish_rounds_dao -> count_rounds($tab_id, $hall_id);

			$list = $this -> fish_rounds_dao -> list_rounds($tab_id, $hall_id);
			$r_list = array();
			foreach($list as $each) {
				$r_list[] = $each -> round_count;
			}
			$res['history_list'] = $r_list;
		}

		$this -> to_json($res);
	}

	public function round_count_king() {
		$res = array('success' => TRUE);
		$tab_id = $this -> get_post('tab_id');
		$hall_id = $this -> get_post('hall_id');

		if($tab_id == '' || $hall_id == '') {
			$res['error_msg'] = '缺少必要欄位';
		} else {
			$res['rounds'] =  $this -> fish_rounds_king_dao -> count_rounds($tab_id, $hall_id);

			$list = $this -> fish_rounds_king_dao -> list_rounds($tab_id, $hall_id);
			$r_list = array();
			foreach($list as $each) {
				$r_list[] = $each -> round_count;
			}
			$res['history_list'] = $r_list;
		}

		$this -> to_json($res);
	}

	public function check_weapon() {
		$res = array('success' => TRUE);

		$payload = $this -> get_payload();
		$user_id = $payload['user_id'];
		$corp_id = $payload['corp_id'];

		$user = $this -> users_dao -> find_by_id($user_id);
		if(empty($corp_id) || empty($user_id) || empty($user)) {
			$res['error_msg'] = '缺少必要欄位';
		} else {
			$res['user_id'] = $user_id;

			$style_list = $this -> product_items_dao -> find_all_style();
			$poduct_items = $this -> product_items_dao -> find_all_by_user($user_id);

			$style_map = array();
			$style_map[1] = array();
			$style_map[2] = array();

			foreach($poduct_items as $each) {
				if(empty($style_map[$each -> style])) {
					$style_map[$each -> style] = $each;
				}
			}

			$need_again = FALSE;
			if(empty($style_map[1])) {
				// create sword
				$need_again = TRUE;
				$this -> product_items_dao -> insert(array(
					'user_id' => $user_id,
					'product_id' => 1,
					'is_base' => 1,
				));
			}
			if(empty($style_map[2])) {
				// create fort
				$need_again = TRUE;
				$this -> product_items_dao -> insert(array(
					'user_id' => $user_id,
					'product_id' => 7,
					'is_base' => 1,
				));
			}

			if($need_again) {
				$style_map = array();
				$style_map[1] = array();
				$style_map[2] = array();
				$poduct_items = $this -> product_items_dao -> find_all_by_user($user_id);

				foreach($poduct_items as $each) {
					if(empty($style_map[$each -> style])) {
						$style_map[$each -> style] = $each;
					}
				}
			}

			$res['style_list'] = $style_list;
			$res['style_map'] = array();
			// $res['style_map'][] = $style_map;
			$p1 = $style_map[1];
			$p1 -> level = $this -> product_strengthen_dao -> find_level_by_user_and_product($user_id, $p1 -> product_id);
			$p1 -> fatal_used_count = $this -> product_items_dao -> find_fatal_used_count($user_id, $p1 -> product_id);
			$res['style_map'][] = $p1;

			$p2 = $style_map[2];
			$p2 -> level = $this -> product_strengthen_dao -> find_level_by_user_and_product($user_id, $p2 -> product_id);
			$p2 -> fatal_used_count = $this -> product_items_dao -> find_fatal_used_count($user_id, $p2 -> product_id);
			$res['style_map'][] = $p2;
		}

		$this -> to_json($res);
	}

}
?>
