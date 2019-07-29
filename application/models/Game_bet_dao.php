<?php
class Game_bet_dao extends MY_Model {

	function __construct() {
		parent::__construct();

		// initialize table name
		parent::set_table_name('game_bet');

		$this -> alias_map = array(

		);
	}

	function add_game_bet($game_id, $corp_id, $user_id, $bet_amt) {
		$this -> load -> model('Users_dao', 'users_dao');

		$i = array();
		$i['game_id'] = $game_id;
		$i['corp_id'] = $corp_id;
		$i['user_id'] = $user_id;
		$i['bet_amt'] = $bet_amt;
		$this -> insert($i);

		$user = $this -> users_dao -> find_by_id($user_id);

		// check sum_bet
		$curr = $this -> sum_bet($user_id);
		$lv_last_bet_amt = $user -> lv_last_bet_amt;
		if($curr > ($lv_last_bet_amt + 1000)) {
		    $diff = $curr - $lv_last_bet_amt;
		    $lv_up = floor($diff / 1000);
		    $lv_last_bet_amt += ($lv_up * 1000);
				$this -> users_dao -> update(array(
					'lv_last_bet_amt' => $lv_last_bet_amt,
					'lv' => $user -> lv + $lv_up
				), $user_id);
		}
	}

	function sum_bet($user_id) {
		$sql = "select sum(bet_amt) as samt from {$this->table_name} where user_id = {$user_id} ";
		$list = $this -> db -> query($sql) -> result();
		if(count($list) > 0) {
			return !empty($list[0] -> samt) ? $list[0] -> samt : 0;
		}
		return 0;
	}

}
?>
