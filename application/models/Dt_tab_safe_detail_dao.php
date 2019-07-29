<?php
class Dt_tab_safe_detail_dao extends MY_Model {

	function __construct() {
		parent::__construct();

		// initialize table name
		parent::set_table_name('dt_tab_safe_detail');

		$this -> alias_map = array(
			// 'corp_id' => '_m.corp_id'
			// 'pay_type_name' => 'pt.type_name'
 		);
	}

	function random_one($winner = 0) {
		$list = $this -> db -> query("select * from {$this->table_name} where winner = $winner order by rand() limit 1 ") -> result();
		if(count($list) > 0) {
			return $list[0];
		}
		return NULL;
	}
}
?>
