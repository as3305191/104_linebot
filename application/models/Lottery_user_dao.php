<?php
class Lottery_user_dao extends MY_Model {

	function __construct() {
		parent::__construct();

		// initialize table name
		parent::set_table_name('lottery_user');

		$this -> alias_map = array(

		);
	}

	function add_amt($user_id, $tab_id, $amt) {
		$item_id = 0;
		$item = $this -> find_by_user_and_tab($user_id, $tab_id);
		if(!empty($item)) {
			$this -> db -> query("update {$this->table_name} set amt = amt + {$amt}
											where user_id = {$user_id} and tab_id = {$tab_id}");
			$item_id = $item -> id;
		} else {
			// empty then create
			$item_id = $this -> insert(array(
				'tab_id' => $tab_id,
				'user_id' => $user_id,
				'amt' => $amt,
			));
		}

		$item = $this -> find_by_id($item_id);
		return $item -> amt;
	}

	function find_by_user_and_tab($user_id, $tab_id) {
		$this -> db -> where('user_id', $user_id);
		$this -> db -> where('tab_id', $tab_id);
		$list = $this -> db -> get($this -> table_name) -> result();
		if(count($list) > 0) {
			return $list[0];
		}
		return NULL;
	}
}
?>
