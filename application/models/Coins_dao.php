<?php
class Coins_dao extends MY_Model {

	function __construct() {
		parent::__construct();

		// initialize table name
		parent::set_table_name('coins');

		$this -> alias_map = array(

		);
	}

	function find_by_currency($code) {
		$this -> db -> where('currency', $code);
		$list = $this -> find_all();
		if(count($list) > 0) {
			return $list[0];
		}
		return NULL;
	}

	function find_default_corp() {
		$this -> db -> where('is_default', 1);
		$list = $this -> find_all();
		if(count($list) > 0) {
			return $list[0];
		}
		return NULL;
	}

	function find_all_company() {
		$this -> db -> where('status', 0);
		$list=  $this -> find_all();
		return $list;
	}

	function find_my_company($id) {
		$this -> db -> where('status', 0);
		$this -> db -> where('id', $id);
		$list=  $this -> find_all();
		return $list;
	}
}
?>
