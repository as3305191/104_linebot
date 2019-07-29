<?php
class Niuniu_tab_round_dao extends MY_Model {

	function __construct() {
		parent::__construct();

		// initialize table name
		parent::set_table_name('niuniu_tab_round');

		$this -> alias_map = array(

		);
	}

	function nn_min_amt($hall_id) {
		switch($hall_id) {
			case 0:
				return 100 * 300;
			case 1:
				return 1000 * 300;
			case 2:
				return 10000 * 300;
			default: // -1
				return 10 * 300;
		}
	}

	function nn_min_bet($hall_id) {
		switch($hall_id) {
			case 0:
				return 100;
			case 1:
				return 1000;
			case 2:
				return 10000;
			default: // -1
				return 10;
		}
	}

	function get_round($tab_id, $hall_id) {
		$res = array('success' => TRUE);

		$this -> load -> model('Niuniu_tab_users_dao', 'nn_tab_users_dao');
		$this -> load -> model('Wallet_tx_dao', 'wtx_dao');
		$this -> load -> model('Wallet_tx_bkc_dao', 'wtx_bkc_dao');

		$list = $this -> find_unfinished($tab_id, $hall_id);
		$item = NULL;
		$bypass_users = array();
		if(count($list) == 0) {
			// check users
			$tb_users = $this -> nn_tab_users_dao -> find_all_by_tab_id($tab_id, $hall_id);

			$user_id_list = array();
			if(count($tb_users) >= 2) { // 至少2人
				foreach($tb_users as $a_user) {
					$s_amt = 0;
					if($hall_id < 0) {
						$s_amt = $this -> wtx_bkc_dao -> get_sum_amt($a_user -> user_id);
					} else {
						$s_amt = $this -> wtx_dao -> get_sum_amt($a_user -> user_id);
					}

					$min_amt = $this -> nn_min_amt($hall_id);

					$last_update_time = strtotime($a_user -> last_update_time);
					if(time() - $last_update_time > 7 && FALSE) {
						// 如果7秒沒有回應就踢除
						// $this -> nn_tab_users_dao -> leave_tab($tab_id, $a_user -> user_id, $hall_id);
					} else {
						if($s_amt < $min_amt) { // 錢不夠
							if($hall_id < 0) {
								// 測試
								$tx = array();
								$tx['user_id'] = $a_user -> user_id;
								$tx['type_id'] = 9898;
								$tx['amt'] = $min_amt;
								$tx['brief'] = "體驗自動充值";
								$this -> wtx_bkc_dao -> insert($tx);

								// add to user id list
								$user_id_list[] = $a_user -> user_id;
							} else {
								// bypass 使用者，錢不夠就踢除
								$bypass_users[] = $a_user;
								$this -> nn_tab_users_dao -> leave_tab($tab_id, $a_user -> user_id, $hall_id);
							}
						} else { // 錢夠
							$user_id_list[] = $a_user -> user_id;
						}
					}
				}
			}

			// 人要夠
			if(count($user_id_list) >= 2) {
				// create
				$last_id = $this -> insert(array(
					'corp_id' => 1,
					'tab_id' => $tab_id,
					'hall_id' => $hall_id,
					'user_id_list' => implode(",", $user_id_list),
					'start_time' => date('Y-m-d H:i:s'),
					'bypass_users' => json_encode($bypass_users),
				));

				$item = $this -> find_by_id($last_id);
			}

		} else { // 已存在
			$item = $list[0];
		}

		$res['item'] = $item;
		$res['bypass_users'] = $bypass_users;
		return $res;
	}

	function find_unfinished($tab_id, $hall_id) {
		$this -> db -> where("tab_id", $tab_id);
		$this -> db -> where("hall_id", $hall_id);
		$this -> db -> where("is_finish", 0);
		$this -> db -> order_by("id", "asc");
		$list = $this -> find_all();
		return $list;
	}

	function find_all_round_users($tab_id, $hall_id) {

	}
}
?>
