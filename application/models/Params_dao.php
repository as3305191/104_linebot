<?php
class Params_dao extends MY_Model {

	function __construct() {
		parent::__construct();

		// initialize table name
		parent::set_table_name('params');

		$this -> alias_map = array(

 		);
	}

	function find_by_corp_id($corp_id) {
		$item = $this -> find_by('corp_id', $corp_id);
		if(!empty($item)) {
			return $item;
		}
		$id = $this -> insert(array('corp_id' => $corp_id));
		return $this -> find_by_id($id);
	}

}
?>
