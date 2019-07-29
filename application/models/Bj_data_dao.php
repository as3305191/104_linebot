<?php
class Bj_data_dao extends MY_Model {

	function __construct() {
		parent::__construct();

		// initialize table name
		parent::set_table_name('bj_data');

		$this -> alias_map = array(

		);
	}

	function random_one() {
		$list = $this -> db -> query("select * from {$this->table_name} order by rand() limit 1 ") -> result();
		if(count($list) > 0) {
			return $list[0];
		}
		return NULL;
	}

}
?>
