<?php
class Game_list_dao extends MY_Model {

	function __construct() {
		parent::__construct();

		// initialize table name
		parent::set_table_name('game_list');

		$this -> alias_map = array(

		);
	}

	function check_clone() {
		$this -> load -> model('Corp_dao', 'corp_dao');
		$all_game = $this -> find_all();
		$all_corp = $this -> corp_dao -> find_all();
		foreach($all_corp as $corp) {
			foreach($all_game as $game) {
				$item = $this -> find_by_corp_and_game($corp -> id, $game -> id);
				if(empty($item)) {
					// do insert
					$i_data = array();
					$i_data['game_list_id'] = $game -> id;
					$i_data['corp_id'] = $corp -> id;
					$i_data['status'] = $game -> status;
 					$this->db->insert('game_list_corp', $i_data);
				}
			}
		}
	}

	function find_by_corp_and_game($corp_id, $game_list_id) {
		$this -> db -> where("game_list_id", $game_list_id);
		$this -> db -> where("corp_id", $corp_id);
		$list = $this -> db -> get("game_list_corp") -> result();

		if(count($list) > 0) {
			return $list[0];
		}
		return NULL;

	}
	function find_all_by_corp($corp_id) {
		$this -> db -> select('_m.status');
		$this -> db -> select('gl.id');
		$this -> db -> select('gl.game_name');
		$this -> db -> where('corp_id', $corp_id);
		$this -> db -> from('game_list_corp as _m');
		$this -> db -> join("game_list as gl", "_m.game_list_id = gl.id", "left");
		$list = $this -> db -> get() -> result();
		return $list;
	}
}
?>
