<?php
class Fish_boss_game_bet_dao extends MY_Model {

	function __construct() {
		parent::__construct();

		// initialize table name
		parent::set_table_name('fish_boss_game_bet');
	}

	function find_today_by_user($user_id) {
		$today = date("Y-m-d");
		$this -> db -> where("bet_date", $today);
		$this -> db -> where("user_id", $user_id);
		$list = $this -> find_all();
		if(count($list) == 0) {
			$item = $this -> create_by_user($user_id, $today);
			return $item;
		}
		return $list[0];
	}

	function create_by_user($user_id, $date) {
		$last_id = $this -> insert(array(
			'user_id' => $user_id,
			'bet_date' => $date,
		));
		$obj = $this -> find_by_id($last_id);
		return $obj;
	}
}
?>
