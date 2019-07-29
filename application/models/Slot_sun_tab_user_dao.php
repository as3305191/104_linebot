<?php
class Slot_sun_tab_user_dao extends MY_Model {

	function __construct() {
		parent::__construct();

		// initialize table name
		parent::set_table_name('slot_sun_tab_user');

		$this -> alias_map = array(

		);
	}

	function delete_expired($minutes = 5) {
		$sql = "delete from {$this->table_name} where TIMESTAMPDIFF(MINUTE,update_time,NOW()) > {$minutes}";
		$this -> db -> query($sql);
	}

	function find_by_tab_id($tab_id, $hall_id = 0) {
		$this -> db -> where('tab_id', $tab_id);
		$this -> db -> where('hall_id', $hall_id);
		$list = $this -> find_all();
		if(count($list) > 0) {
			return $list[0];
		}
		return NULL;
	}

	function find_all_by_user_id($user_id, $hall_id = 0) {
		$this -> db -> where('user_id', $user_id);
		$this -> db -> where('hall_id', $hall_id);
		$list = $this -> find_all();
		return $list;
	}

	function find_by_user_id($user_id, $hall_id = 0) {
		$this -> db -> where('user_id', $user_id);
		$this -> db -> where('hall_id', $hall_id);
		$list = $this -> find_all();
		if(count($list) > 0) {
			return $list[0];
		}
		return NULL;
	}

	function delete_by_tab_id($tab_id) {
		$this -> db -> query("delete from {$this->tab_name} where tab_id = $tab_id ");
	}
}
?>
