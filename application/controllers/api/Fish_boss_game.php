<?php
class Fish_boss_game extends MY_Base_Controller {

	function __construct() {
		parent::__construct();

		$this -> load -> model('Users_dao', 'users_dao');
		$this -> load -> model('Images_dao', 'img_dao');
		$this -> load -> model('Corp_dao', 'corp_dao');

		$this -> load -> model('Fish_boss_game_dao', 'fbg_dao');
		$this -> load -> model('Fish_boss_game_bet_dao', 'fbg_bet_dao');


		$this -> load -> model('Wallet_tx_dao', 'wtx_dao');

		$this -> load -> model('Fish_game_msg_dao', 'fgm_dao');

		$this -> load -> model('Products_dao', 'products_dao');
		$this -> load -> model('Product_items_dao', 'product_items_dao');
	}

	public function list_today() {
		$res = array("success" => TRUE);

		$payload = $this -> get_payload();
		$user_id = $payload['user_id'];
		if(!empty($user_id)) {
			$item = $this -> fbg_bet_dao -> find_today_by_user($user_id);
			$item -> game_1_time_rem = !empty($item -> game_1_time) ? $this -> get_rem(time() - strtotime($item -> game_1_time), 60) : 0;
			$item -> game_2_time_rem = !empty($item -> game_2_time) ? $this -> get_rem(time() - strtotime($item -> game_2_time), 50) : 0;
			$item -> game_3_time_rem = !empty($item -> game_3_time) ? $this -> get_rem(time() - strtotime($item -> game_3_time), 40) : 0;
			$item -> game_4_time_rem = !empty($item -> game_4_time) ? $this -> get_rem(time() - strtotime($item -> game_4_time), 30) : 0;

			$res['fish_boss_game_bet'] = $item;
		} else {
			$res['error_msg'] = "缺少必要欄位";
		}
		$this -> to_json($res);
	}

	public function enter_game() {
		$res = array("success" => TRUE);

		$fish_boss_game_id = $this -> get_post("fish_boss_game_id");
		$payload = $this -> get_payload();
		$user_id = $payload['user_id'];
		if(!empty($user_id) && !empty($fish_boss_game_id)) {
			$item = $this -> fbg_bet_dao -> find_today_by_user($user_id);
			$is_game_col = "is_game_{$fish_boss_game_id}";
			$is_game_time_col = "game_{$fish_boss_game_id}_time";
			if($item -> $is_game_col == 0) {
				$this -> fbg_bet_dao -> db -> set($is_game_time_col, 'NOW()', FALSE);
				$this -> fbg_bet_dao -> update(array(
					"{$is_game_col}" => 1,
				), $item -> id);
				$item = $this -> fbg_bet_dao -> find_today_by_user($user_id);
			}

			$item -> game_1_time_rem = !empty($item -> game_1_time) ? $this -> get_rem(time() - strtotime($item -> game_1_time), 60) : 0;
			$item -> game_2_time_rem = !empty($item -> game_2_time) ? $this -> get_rem(time() - strtotime($item -> game_2_time), 50) : 0;
			$item -> game_3_time_rem = !empty($item -> game_3_time) ? $this -> get_rem(time() - strtotime($item -> game_3_time), 40) : 0;
			$item -> game_4_time_rem = !empty($item -> game_4_time) ? $this -> get_rem(time() - strtotime($item -> game_4_time), 30) : 0;
			$res['fish_boss_game_bet'] = $item;
		} else {
			$res['error_msg'] = "缺少必要欄位";
		}
		$this -> to_json($res);
	}

	public function beat_boss() {
		$res = array("success" => TRUE);

		$fish_boss_game_id = $this -> get_post("fish_boss_game_id");
		$fish_boss_game_bet_id = $this -> get_post("fish_boss_game_bet_id");
		$payload = $this -> get_payload();
		$user_id = $payload['user_id'];
		$corp_id = $payload['corp_id'];

		if(!empty($user_id) && !empty($fish_boss_game_id) && !empty($fish_boss_game_bet_id)) {
			$item = $this -> fbg_bet_dao -> find_by_id($fish_boss_game_bet_id);
			$is_game_col = "is_game_{$fish_boss_game_id}";
			$is_win_game_col = "is_win_game_{$fish_boss_game_id}";
			$is_game_time_col = "game_{$fish_boss_game_id}_time";

			if(!empty($item)) {
				if($item -> $is_game_col == 1) {
					if($item -> $is_win_game_col == 0) {
						$this -> fbg_bet_dao -> update(array(
							"{$is_win_game_col}" => 1,
						), $item -> id);
						$item = $this -> fbg_bet_dao -> find_by_id($fish_boss_game_bet_id);
						$res['fish_boss_game_bet'] = $item;

						$win_amt = 0;
						$product_arr = array();
					  if($fish_boss_game_id == 1) {
							$win_amt = 30000;
							$product_arr[] = 23; // 強化
							$product_arr[] = 23; // 強化
							$product_arr[] = 13; // 電池A
							$product_arr[] = 14; // 電池B
							$product_arr[] = 15; // 電池C
						}
					  if($fish_boss_game_id == 2) {
							$win_amt = 60000;
							$product_arr[] = 23; // 強化
							$product_arr[] = 23; // 強化
							$product_arr[] = 23; // 強化
							$product_arr[] = 23; // 強化
							$product_arr[] = 13; // 電池A
							$product_arr[] = 13; // 電池A
							$product_arr[] = 14; // 電池B
							$product_arr[] = 14; // 電池B
							$product_arr[] = 15; // 電池C
							$product_arr[] = 15; // 電池C
						}
					  if($fish_boss_game_id == 3) {
							$win_amt = 120000;
							$product_arr[] = 23; // 強化
							$product_arr[] = 23; // 強化
							$product_arr[] = 23; // 強化
							$product_arr[] = 23; // 強化
							$product_arr[] = 23; // 強化
							$product_arr[] = 23; // 強化
							$product_arr[] = 23; // 強化
							$product_arr[] = 23; // 強化
							$product_arr[] = 13; // 電池A
							$product_arr[] = 13; // 電池A
							$product_arr[] = 13; // 電池A
							$product_arr[] = 13; // 電池A
							$product_arr[] = 14; // 電池B
							$product_arr[] = 14; // 電池B
							$product_arr[] = 14; // 電池B
							$product_arr[] = 14; // 電池B
							$product_arr[] = 15; // 電池C
							$product_arr[] = 15; // 電池C
							$product_arr[] = 15; // 電池C
							$product_arr[] = 15; // 電池C
						}

					  if($fish_boss_game_id == 4) {
							$product_arr[] = 1; // E武士刀
							$product_arr[] = 7; // E砲塔
						}

						foreach($product_arr as $product_id) {
							$this -> product_items_dao -> insert(array(
								'user_id' => $user_id,
								'product_id' => $product_id,
								'tx_id' => $item -> id,
								'tx_type' => "beat_boss_reward_{$fish_boss_game_id}",
							));
						}

						if($win_amt > 0) {
							$tx = array();
							$tx['tx_type'] = "beat_boss_reward_{$fish_boss_game_id}";
							$tx['tx_id'] = $item -> id;
							$tx['corp_id'] = $corp_id; // corp id
							$tx['user_id'] = $user_id;
							$tx['amt'] = $win_amt;

							$tx['brief'] = "挑戰BOSS 獲得 $win_amt";
							$this -> wtx_dao -> insert($tx);
						}

					}	else {
						$res['error_msg'] = "已經贏過了";
					}
				} else {
					$res['error_msg'] = "尚未進行此遊戲";
				}
			} else {
				$res['error_msg'] = "查無遊戲紀錄";
			}

			$res['fish_boss_game_bet'] = $item;
		} else {
			$res['error_msg'] = "缺少必要欄位";
		}
		$this -> to_json($res);
	}

	private function get_rem($val, $max) {
		if($val > $max) {
			return 0;
		} else {
			return ($max - $val);
		}
	}

}
?>
