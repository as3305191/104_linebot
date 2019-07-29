<?php
class Racing_bet_dao extends MY_Model {

	function __construct() {
		parent::__construct();

		// initialize table name
		parent::set_table_name('racing_bet');

		$this -> alias_map = array(

		);
	}

	function get_bet_win_list_by_type($game_id, $type = 0, $v1 = 0, $v2 = 0, $v3 = 0, $v4 = 0) {
		$sql = "select d1.bet_id, rb.bet_amt from racing_bet_tube_detail d1 ";
		if($type >= 2) {
			$sql .= " join(
			select * from racing_bet_tube_detail where tube_num = 2 and val = {$v2}
			) as d2 on d2.bet_id = d1.bet_id ";
		}
		if($type >= 3) {
			$sql .= " join(
			select * from racing_bet_tube_detail where tube_num = 3 and val = {$v3}
			) as d3 on d3.bet_id = d1.bet_id ";
		}
		if($type >= 4) {
			$sql .= " join(
			select * from racing_bet_tube_detail where tube_num = 4 and val = {$v4}
			) as d4 on d4.bet_id = d1.bet_id ";
		}
		$sql .= " join racing_bet rb on rb.id = d1.bet_id and rb.bet_type = $type ";
		$sql .= " where d1.game_id = {$game_id} and d1.tube_num = 1 and d1.val = {$v1} ";
		$list = $this -> db -> query($sql) -> result();
		return $list;
	}

	function sum_bet_amt_by_game($game_id) {
		$this -> db -> select("sum(bet_amt) as samt");
		$this -> db -> where('game_id', $game_id);
		$list = $this -> find_all();
		$samt = 0;
		if(count($list) > 0) {
			$samt = $list[0] -> samt;
		}
		if(empty($samt)) {
			$samt = 0;
		}
		return $samt;
	}

	function sum_pool_amt_by_game($game_id) {
		$this -> db -> select("sum(pool_amt) as samt");
		$this -> db -> where('game_id', $game_id);
		$list = $this -> find_all();
		$samt = 0;
		if(count($list) > 0) {
			$samt = $list[0] -> samt;
		}
		if(empty($samt)) {
			$samt = 0;
		}
		return $samt;
	}

	function find_all_by_game_id($game_id) {
		$this -> db -> where('game_id', $game_id);
		$list = $this -> find_all();
		return $list;
	}

	function list_all_bets_by_game_and_user($game_id, $user_id) {
		$this -> db -> select("_m.*");
		$this -> db -> select("rg.sn");
		$this -> db -> where('_m.game_id', $game_id);
		$this -> db -> where('_m.user_id', $user_id);
		$this -> db -> from($this -> table_name . " as _m");
		$this -> db -> join("racing_games rg", "rg.id = _m.game_id", "left");
		$this -> db -> order_by('id', 'asc');

		$list  = $this -> db -> get() -> result();
		// echo $this -> db -> last_query();
		return $list;
	}

}
?>
