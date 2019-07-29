<?php
class Com_tab_dao extends MY_Model {

	function __construct() {
		parent::__construct();

		// initialize table name
		parent::set_table_name('com_tab');

		$this -> alias_map = array(

		);
	}

	function find_all_by_com_id($com_id) {
		$this -> db -> where('com_id', $com_id);
		$this -> db -> order_by('tab_id', 'asc');
		$list=  $this -> find_all();
		return $list;
	}

	function find_by_com_and_tab($com_id, $tab_id) {
		$this -> db -> where('com_id', $com_id);
		$this -> db -> where('tab_id', $tab_id);
		$list=  $this -> find_all();
		return $list[0];
	}


}
?>
