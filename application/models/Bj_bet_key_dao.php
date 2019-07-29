<?php
class Bj_bet_key_dao extends MY_Model {

	function __construct() {
		parent::__construct();

		// initialize table name
		parent::set_table_name('bj_bet_key');

		$this -> alias_map = array(

		);
	}

	function get_key_id() {
		$last_id = $this -> insert(array('note' => ''));
		return $last_id;
	}
	function mark_key_id($id) {
		$this -> update(array('is_finish' => '1'), $id);
	}

	function get_un_done_list($key_id) {
		$this -> remove_deprecate();

		$this -> db -> where("id < {$key_id}");
		$this -> db -> where("is_finish", 0);
		$list = $this -> find_all();
		return $list;
	}

	function remove_deprecate() {
		$this -> db -> query("delete from {$this->table_name} where TIME_TO_SEC(TIMEDIFF(now(), create_time))  > 10");
	}
}
?>
