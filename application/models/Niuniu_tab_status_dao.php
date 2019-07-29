<?php
class Niuniu_tab_status_dao extends MY_Model {

	function __construct() {
		parent::__construct();

		// initialize table name
		parent::set_table_name('niuniu_tab_status');

		$this -> alias_map = array(

		);
	}

	function find_all_order($hall_id) {
		$this -> db -> select("tab.*");
		$this -> db -> select("_m.status");
		$this -> db -> select("_m.user_count");
		$this -> db -> select("sn.status_name");
		$this -> db -> from("{$this->table_name} as _m");
		$this -> db -> where("_m.hall_id", $hall_id);
		$this -> db -> join("niuniu_tab as tab", "tab.id = _m.tab_id", "left");
		$this -> db -> join("niuniu_tab_status_name as sn", "sn.id = _m.status", "left");

		$this -> db -> order_by("tab.id", "asc");
		$list = $this -> db -> get() -> result();
		return $list;
	}

	function set_status($tab_id, $hall_id, $status) {
		$item = NULL;
		$list = $this -> find_all_by_tab($tab_id, $hall_id);
		if(count($list) > 0) {
			$item = $list[0];
		} else {
			$last_id = $this -> db -> insert(array(
				'tab_id' => $tab_id,
				'hall_id' => $hall_id,
			));
			$item = $this -> find_by_id($last_id);
		}

		$this -> update(array(
			"status" => $status
 		), $item -> id);

		return $item;
	}

	function set_user_count($tab_id, $hall_id, $user_count) {
		$item = NULL;
		$list = $this -> find_all_by_tab($tab_id, $hall_id);
		if(count($list) > 0) {
			$item = $list[0];
		} else {
			$last_id = $this -> db -> insert(array(
				'tab_id' => $tab_id,
				'hall_id' => $hall_id,
			));
			$item = $this -> find_by_id($last_id);
		}

		$this -> update(array(
			"user_count" => $user_count
 		), $item -> id);

		return $item;
	}

	function get_status($tab_id, $hall_id) {
		$item = NULL;
		$list = $this -> find_all_by_tab($tab_id, $hall_id);
		if(count($list) > 0) {
			$item = $list[0];
		} else {
			$last_id = $this -> insert(array(
				'tab_id' => $tab_id,
				'hall_id' => $hall_id,
			));
			$item = $this -> find_by_id($last_id);
		}

		$item -> status_name = $this -> get_status_name($item -> status);
		return $item;
	}

	function get_status_name($status) {
		$name = "";
		$sql = "select * from niuniu_tab_status_name where id = $status";
		$list = $this -> db -> query($sql) -> result();
		if(count($list) > 0) {
			$item = $list[0];
			return $item -> status_name;
		}

		return $name;
	}

	function find_all_by_tab($tab_id, $hall_id = 0) {
		$this -> db -> where('tab_id', $tab_id);
		$this -> db -> where('hall_id', $hall_id);
		$this -> db -> order_by('id', 'asc'); // 確保是第一個
		$list = $this -> find_all();
		return $list;
	}
}
?>
