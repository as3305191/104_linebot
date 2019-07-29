<?php
class User_level extends MY_Base_Controller {

	function __construct() {
		parent::__construct();

		// setup models
		$this -> load -> model('Users_dao', 'u_dao');
		$this -> load -> model('User_level_dao', 'ul_dao');
		$this -> load -> model('Wallet_tx_dao', 'wtx_dao');
		$this -> load -> model('Baccarat_tab_round_bet_dao', 'btrb_dao');
	}

	public function do_it() {
		$lastMonth = date('Y-m', strtotime("first day of previous month"));

		$ul_list = $this -> ul_dao -> find_all_by_thresh();

		$u_list = $this -> u_dao -> find_all_by('status', 0);
		foreach ($u_list as $user) {
			$user -> rolling = $this -> btrb_dao -> sum_rolling_by_ym($user -> id, $lastMonth);
			$lv = 0;
			foreach($ul_list as $each_level) {
				if($user -> rolling >=  $each_level -> thresh) {
					$lv = $each_level -> id;
				}
			}

			$keep_bonus = 0;
			$upgrade_bonus = 0;
			$u_lv = NULL;
			if($lv < $user -> user_level) {
				$u_lv = $this -> ul_dao -> find_by_id($user -> user_level);
				if($user -> rolling >= ($u_lv -> thresh / 2)) {
					// keep level
					$lv = $user -> user_level;
					$keep_bonus = $u_lv -> keep_bonus;
				}
			} else if($lv == $user -> user_level) {
				// keep
				$u_lv = $this -> ul_dao -> find_by_id($user -> user_level);
				$keep_bonus = $u_lv -> keep_bonus;
			} else {
				// upgrade
				$u_lv = $this -> ul_dao -> find_by_id($lv);
				$upgrade_bonus = $u_lv -> vip_bonus;
			}

			echo "$user->id lv:$lv keep:$keep_bonus upgrade:$upgrade_bonus <br/>";

			// update user
			if(empty($user->lv_ym) || $user->lv_ym != $lastMonth) {
				$this -> u_dao -> update(array(
					'user_level' => $lv,
					'lv_ym' => $lastMonth
				), $user -> id);

				$ym_list = $this -> wtx_dao -> check_ym($lastMonth, $user -> id);
				if(count($ym_list) == 0) {
					if($upgrade_bonus > 0) {
						$tx = array();
						$tx['user_id'] = $user -> id;
						$tx['amt'] = $upgrade_bonus;
						$tx['type_id'] = 21;
						$tx['brief'] = "會員升級$lastMonth $u_lv->level_name  獲得 $upgrade_bonus 點";
						$tx['note'] = $tx['brief'] ;
						$tx['ym'] = $lastMonth;
						$this -> wtx_dao -> insert($tx);
					}

					if($keep_bonus > 0) {
						$tx = array();
						$tx['user_id'] = $user -> id;
						$tx['amt'] = $keep_bonus;
						$tx['type_id'] = 22;
						$tx['brief'] = "會員保級$lastMonth $u_lv->level_name  獲得 $keep_bonus 點";
						$tx['note'] = $tx['brief'] ;
						$tx['ym'] = $lastMonth;
						$this -> wtx_dao -> insert($tx);
					}
				} else {
					echo "exist.........";
				}
			}
		}
	}
}
?>
