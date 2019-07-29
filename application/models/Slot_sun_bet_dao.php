<?php
class Slot_sun_bet_dao extends MY_Model {

	function __construct() {
		parent::__construct();

		// initialize table name
		parent::set_table_name('slot_sun_bet');

		$this -> alias_map = array(

		);
	}

	function sum_total_amt($tab_id, $hall_id) {
		$sql = "select sum(win_amt) as samt from {$this->table_name} where tab_id = {$tab_id} and hall_id = {$hall_id}";
		$list = $this -> db -> query($sql) -> result();
		if(count($list) > 0) {
			return !empty($list[0] -> samt) ? $list[0] -> samt : 0;
		}
		return 0;
	}

	function sum_total_amt_sp($tab_id, $hall_id) {
		$sql = "select sum(sp_total_win_amt) as samt from {$this->table_name} where tab_id = {$tab_id} and hall_id = {$hall_id}";
		$list = $this -> db -> query($sql) -> result();
		if(count($list) > 0) {
			return !empty($list[0] -> samt) ? $list[0] -> samt : 0;
		}
		return 0;
	}

	function get_non_free_game_rounds($tab_id, $hall_id = 0) {
		$sql = "SELECT count(id) as samt FROM slot_sun_rounds where tab_id ={$tab_id} and hall_id ={$hall_id} and id >
(select id from slot_sun_rounds where tab_id ={$tab_id} and hall_id ={$hall_id} and is_sp > 0 order by id desc limit 1) ";
		$list = $this -> db -> query($sql) -> result();
		if(count($list) > 0) {
			return !empty($list[0] -> samt) ? $list[0] -> samt : 0;
		}
		return 0;
	}

	function get_last_free_game_id($tab_id, $hall_id = 0, $max_id) {
		$sql = "select id from slot_sun_rounds where tab_id ={$tab_id} and hall_id ={$hall_id} and is_sp > 0 " . ($max_id > -1 ? " and id < {$max_id} " : "") .  " order by id desc limit 1";
		$list = $this -> db -> query($sql) -> result();
		if(count($list) > 0) {
			return !empty($list[0] -> id) ? $list[0] -> id : 0;
		}
		return 0;
	}

	function count_num_by_id($tab_id, $hall_id = 0, $max_id, $min_id) {
		$sql = "SELECT count(id) as samt FROM slot_sun_rounds where tab_id ={$tab_id} and hall_id ={$hall_id} and id > {$min_id}  " . ($max_id > 0 ? " and id <= {$max_id} " : "");
		$list = $this -> db -> query($sql) -> result();
		if(count($list) > 0) {
			return !empty($list[0] -> samt) ? $list[0] -> samt : 0;
		}
		return 0;
	}

	function get_last_over_100_id($tab_id, $hall_id = 0, $max_id) {
		$sql = "select id from slot_sun_rounds where tab_id ={$tab_id} and hall_id ={$hall_id} and has_100 > 0 " . ($max_id > -1 ? " and id < {$max_id} " : "") .  " order by id desc limit 1";
		$list = $this -> db -> query($sql) -> result();
		if(count($list) > 0) {
			return !empty($list[0] -> id) ? $list[0] -> id : 0;
		}
		return 0;
	}

	function get_non_over_100_rounds($tab_id, $hall_id = 0) {
		$sql = "SELECT count(id) as samt FROM slot_sun_rounds where tab_id ={$tab_id} and hall_id ={$hall_id} and id >
(select id from slot_sun_rounds where tab_id ={$tab_id} and hall_id ={$hall_id} and has_100 > 0 order by id desc limit 1) ";
		$list = $this -> db -> query($sql) -> result();
		if(count($list) > 0) {
			return !empty($list[0] -> samt) ? $list[0] -> samt : 0;
		}
		return 0;
	}

	function sum_free_game($user_id) {
		$sql = "select sum(is_sp) as samt from {$this->table_name} where user_id = {$user_id} ";
		$list = $this -> db -> query($sql) -> result();
		if(count($list) > 0) {
			return !empty($list[0] -> samt) ? $list[0] -> samt : 0;
		}
		return 0;
	}
}
?>
