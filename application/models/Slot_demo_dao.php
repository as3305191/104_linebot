<?php
class Slot_demo_dao extends MY_Model {

	function __construct() {
		parent::__construct();

		// initialize table name
		parent::set_table_name('slot_demo');

		$this -> alias_map = array(

		);
	}

	function sum_total_amt($tab_id) {
		$sql = "select sum(total_amt) as samt from {$this->table_name} where tab_id = {$tab_id}";
		$list = $this -> db -> query($sql) -> result();
		if(count($list) > 0) {
			return !empty($list[0] -> samt) ? $list[0] -> samt : 0;
		}
		return 0;
	}
}
?>
