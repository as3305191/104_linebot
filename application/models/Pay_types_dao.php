<?php
class Pay_types_dao extends MY_Model {

	function __construct() {
		parent::__construct();

		// initialize table name
		parent::set_table_name('pay_types');
	}

	function find_all_visible($visible = TRUE) {
		$this -> db -> where('status', $visible ? 0 : 1);
		$this -> db -> order_by('id', 'asc');
		return $this -> find_all();
	}

	function find_pay_options($pay_type_id) {
		$this -> db -> where('pay_type_id', $pay_type_id);
		$this -> db -> from('pay_options');
		$query = $this -> db -> get();
		return $query -> result();
	}

	function find_all_local() {
		$this -> db -> where("id in (1,2)");
		return $this -> find_all();
	}

	function find_all_foreign() {
		$this -> db -> where('id in (5,6,7)');
		return $this -> find_all();
	}
}
?>
