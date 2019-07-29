<?php
class Manual_dao extends MY_Model {

	function __construct() {
		parent::__construct();

		// initialize table name
		parent::set_table_name('manual');

		$this -> alias_map = array(

 		);
	}

	function get_val($corp_id) {
		$item = $this -> find_by('corp_id', $corp_id);
		if(empty($item)) {
			$last_id = $this -> insert(array(
				'corp_id' => $corp_id
 			));
			$item = $this -> find_by_id($last_id);
		}
		return $item -> val;
	}

	function get_val_item($corp_id) {
		$item = $this -> find_by('corp_id', $corp_id);
		if(empty($item)) {
			$last_id = $this -> insert(array(
				'corp_id' => $corp_id
 			));
			$item = $this -> find_by_id($last_id);
		}
		return $item;
	}

	function set_val($val, $m_id) {
		$this -> update(array('val' => $val), $m_id);
	}

}
?>
