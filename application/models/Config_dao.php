<?php
class Config_dao extends MY_Model {

	function __construct() {
		parent::__construct();

		// initialize table name
		parent::set_table_name('config');

		$this -> alias_map = array(

 		);
	}

	function get_val($key) {
		$item = $this -> find_by_id(1);
		return $item -> $key;
	}

	function get_val_by_corp($key, $corp) {
		$item = $this -> get_item_by_corp($corp);
		return $item -> $key;
	}

	function get_item() {
		$item = $this -> find_by_id(1);
		return $item;
	}

	function get_item_by_corp($corp_id) {
		$this -> db -> where('corp_id', $corp_id);
		$list = $this -> db -> get($this -> table_name) -> result();
		if(count($list) == 0) {
			$id = $this -> insert(array('corp_id' => $corp_id));
			$item = $this -> find_by_id($id);
			return $item;
		}
		return $list[0];
	}
}
?>
