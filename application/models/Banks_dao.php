<?php
class Banks_dao extends MY_Model {

	function __construct() {
		parent::__construct();

		// initialize table name
		parent::set_table_name('banks');

		$this -> alias_map = array(

 		);
	}

	function find_my_all() {
		$this -> db -> order_by('pos', 'desc');
		$this -> db -> order_by('bank_id', 'asc');
		return $this -> find_all();
	}

	function find_all_by_country($country) {
		$this -> db -> order_by('pos', 'desc');
		$this -> db -> order_by('bank_id', 'asc');
		$this -> db -> where('country', $country);
		return $this -> find_all();
	}

}
?>
