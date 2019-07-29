<?php
class User_level_dao extends MY_Model {

	function __construct() {
		parent::__construct();

		// initialize table name
		parent::set_table_name('user_level');
	}

	function find_all_by_thresh() {
		$this -> db -> order_by('thresh', 'asc');
		$this -> db -> where('thresh >= 0');
		return $this -> find_all();
	}
}
?>
