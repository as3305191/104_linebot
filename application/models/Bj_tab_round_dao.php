<?php
class Bj_tab_round_dao extends MY_Model {

	function __construct() {
		parent::__construct();

		// initialize table name
		parent::set_table_name('bj_tab_round');

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

	function next_card($round_id) {
		$obj = $this -> find_by_id($round_id);
		$next_card = "1";
		$cards = json_decode($obj -> data);
		$next_card = array_shift($cards);
		// // save
		$cards = json_encode($cards);
		$this -> update(array('data' => $cards), $obj -> id);
		return $next_card;
	}

	function get_round($tab_id, $hall_id) {
		$res = array('success' => TRUE);

		$this -> load -> model('Bj_tab_users_dao', 'nn_tab_users_dao');
		$this -> load -> model('Wallet_tx_dao', 'wtx_dao');
		$this -> load -> model('Wallet_tx_bkc_dao', 'wtx_bkc_dao');

		$list = $this -> find_unfinished($tab_id, $hall_id);
		$item = NULL;
		$bypass_users = array();
		if(count($list) == 0) {
			// check users
			$tb_users = $this -> nn_tab_users_dao -> find_all_by_tab_id($tab_id, $hall_id);

			// 至少1人
			if(count($tb_users) >= 1) {
				// create
				// gen card
				$aCardDeck=array();
				for($j=0 ; $j<52 ; $j++){
						$aCardDeck[] = $j;
				}
				shuffle($aCardDeck);

				$last_id = $this -> insert(array(
					'corp_id' => 1,
					'tab_id' => $tab_id,
					'hall_id' => $hall_id,
					'start_time' => date('Y-m-d H:i:s'),
					'data' => json_encode($aCardDeck),
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
